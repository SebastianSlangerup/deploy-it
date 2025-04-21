<?php

namespace App\Http\Controllers;

use App\Actions\DeleteInstanceAction;
use App\Data\ConfigurationData;
use App\Data\InstanceData;
use App\Data\PackageData;
use App\Enums\InstanceTypeEnum;
use App\Http\Requests\CreateInstanceRequest;
use App\Http\Requests\UpdateInstanceRequest;
use App\Jobs\CheckOnTaskIdJob;
use App\Jobs\CreateDockerImageJob;
use App\Jobs\CreateServerJob;
use App\Jobs\GetIpAddressWithQemuAgentJob;
use App\Jobs\GetQemuStatusJob;
use App\Jobs\InstallPackagesJob;
use App\Models\Configuration;
use App\Models\Container;
use App\Models\Instance;
use App\Models\Package;
use App\Models\Server;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class InstanceController extends Controller
{
    public function index(Request $request): Response
    {
        // Get all instances by user
        $instances = Instance::query()
            ->with('created_by')
            ->where('created_by', '=', $request->user()->id)
            ->get();

        return Inertia::render('Dashboard', [
            'instances' => InstanceData::collect($instances),
        ]);
    }

    public function show(Request $request, Instance $instance): Response
    {
        $instance->load('created_by');

        // We only check for Server instances here. Containers don't have a configuration
        if ($instance->type === InstanceTypeEnum::Server) {
            $configuration = $instance->instanceable->configuration;
        }

        return Inertia::render('Instances/ShowInstance', [
            'instance' => InstanceData::from($instance),
            'configuration' => $configuration ?? null,
        ]);
    }

    public function create(Request $request, InstanceTypeEnum $instanceType): Response
    {
        if ($instanceType === InstanceTypeEnum::Server) {
            Gate::authorize('interact-with-servers');
        }

        $configurations = Configuration::all();
        $packages = Package::all();

        return Inertia::render('Instances/CreateInstance', [
            'configurations' => ConfigurationData::collect($configurations),
            'packages' => PackageData::collect($packages),
            'instanceType' => $instanceType->value,
        ]);
    }

    public function store(CreateInstanceRequest $request): RedirectResponse|JsonResponse
    {
        $instanceType = $request->safe()->enum('instance_type', InstanceTypeEnum::class);

        if ($instanceType === InstanceTypeEnum::Server) {
            Gate::authorize('interact-with-servers');
        }

        $instance = Instance::query()->make([
            'name' => $request->safe()->string('name'),
            'description' => $request->safe()->string('description'),
            'hostname' => $request->safe()->string('hostname'),
            'created_by' => $request->user()->id,
        ]);

        if ($instanceType === InstanceTypeEnum::Server) {
            $this->setupServer($instance, $request);
        }

        if ($instanceType === InstanceTypeEnum::Container) {
            $this->setupContainer($instance, $request);
        }

        if ($request->expectsJson()) {
            return new JsonResponse(
                data: [
                    'message' => 'Instance Created Successfully. Jobs dispatched',
                    'data' => InstanceData::from($instance)->toArray(),
                ],
                status: JsonResponse::HTTP_CREATED,
            );
        }

        return redirect(route('instances.show', $instance));
    }

    public function servers(Request $request): Response
    {
        Gate::authorize('interact-with-servers');

        // Get all instances that are of type 'server'
        $instances = Instance::query()
            ->with('created_by')
            ->where('created_by', '=', $request->user()->id)
            ->where('instanceable_type', '=', Server::class)
            ->get();

        return Inertia::render('Servers/IndexServers', [
            'instances' => InstanceData::collect($instances),
        ]);
    }

    public function containers(Request $request): Response
    {
        // Get all instances that are of type 'container'
        $instances = Instance::query()
            ->with('created_by')
            ->where('created_by', '=', $request->user()->id)
            ->where('instanceable_type', '=', Container::class)
            ->get();

        return Inertia::render('Containers/IndexContainers', [
            'instances' => InstanceData::collect($instances),
        ]);
    }

    public function destroy(Request $request, Instance $instance): RedirectResponse|JsonResponse
    {
        if ($instance->instanceable_type === Server::class) {
            Gate::authorize('interact-with-servers');
        }

        try {
            app(DeleteInstanceAction::class)->execute($instance);
        } catch (ConnectionException $exception) {
            Log::error('Connection failed when attempting to delete instance: {instance}. Error message: {message}', [
                'job' => "[ID: {$instance->id}]",
                'message' => $exception->getMessage(),
            ]);
        }

        if ($request->expectsJson()) {
            return new JsonResponse(
                data: [
                    'message' => 'Instance deleted Successfully',
                    'id' => $instance->id,
                ],
                status: JsonResponse::HTTP_OK,
            );
        }

        return redirect()->back(303);
    }

    public function update(UpdateInstanceRequest $request, Instance $instance): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        if ($instance->instanceable_type === Server::class) {
            Gate::authorize('interact-with-servers');
        }

        if (! empty($validated['status'])) {
            $instance->status->transitionTo($validated['status']);
        }

        $instance->update($validated);

        if ($request->expectsJson()) {
            return new JsonResponse(
                data: [
                    'message' => 'Instance updated Successfully',
                    'data' => InstanceData::from($instance)->toArray(),
                ],
                status: JsonResponse::HTTP_OK,
            );
        }

        return redirect()->back(303);
    }

    public function setupServer(Instance $instance, Request $request): void
    {
        $model = Server::query()->create([
            'configuration_id' => $request->safe()->array('selected_configuration')['id'],
        ]);

        $instance->instanceable()->associate($model);

        $instance->save();

        $selectedConfiguration = ConfigurationData::from($request->safe()->array('selected_configuration'));
        $selectedPackages = Package::query()
            ->whereIn(
                'id',
                $request->collect('selected_packages')->pluck('id')
            )
            ->get();

        // Dispatch jobs to process the newly created server
        Bus::chain([
            new CreateServerJob($instance, $selectedConfiguration),
            new CheckOnTaskIdJob($instance),
            new GetQemuStatusJob($instance),
            new GetIpAddressWithQemuAgentJob($instance),
            new InstallPackagesJob($instance, $selectedPackages),
        ])->onQueue('polling')->dispatch();
    }

    public function setupContainer(Instance $instance, Request $request): void
    {
        $model = Container::query()->create([
            'docker_image' => $request->safe()->string('docker_image'),
        ]);

        $instance->instanceable()->associate($model);

        $instance->save();

        // Dispatch job to process the newly created container
        CreateDockerImageJob::dispatch($instance)->onQueue('polling');
    }
}
