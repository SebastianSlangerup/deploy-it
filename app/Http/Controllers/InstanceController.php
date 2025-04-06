<?php

namespace App\Http\Controllers;

use App\Data\ConfigurationData;
use App\Data\InstanceData;
use App\Enums\InstanceTypeEnum;
use App\Http\Requests\InstanceRequest;
use App\Jobs\CreateServerJob;
use App\Models\Configuration;
use App\Models\Container;
use App\Models\Instance;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

        return Inertia::render('Instances/ShowInstance', [
            'instance' => InstanceData::from($instance),
        ]);
    }

    public function create(Request $request, InstanceTypeEnum $instanceType): Response
    {
        if ($instanceType === InstanceTypeEnum::Server) {
            Gate::authorize('interact-with-servers');
        }

        $configurations = Configuration::all();

        return Inertia::render('Instances/CreateInstance', [
            'configurations' => ConfigurationData::collect($configurations),
            'instanceType' => $instanceType->value,
        ]);
    }

    public function store(InstanceRequest $request)
    {
        $instanceType = $request->safe()->enum('instance_type', InstanceTypeEnum::class);

        if ($instanceType === InstanceTypeEnum::Server) {
            Gate::authorize('interact-with-servers');
        }

        $instance = Instance::query()->make([
            'name' => $request->safe()->string('name'),
            'description' => $request->safe()->string('description'),
            'created_by' => $request->user()->id,
        ]);

        if ($instanceType === InstanceTypeEnum::Server) {
            $model = Server::query()->create([
                'configuration_id' => $request->safe()->array('selected_configuration')['id'],
            ]);
        }

        if ($instanceType === InstanceTypeEnum::Container) {
            $model = Container::query()->create([
                'docker_image' => $request->safe()->string('docker_image'),
            ]);
        }

        if (isset($model)) {
            $instance->instanceable()->associate($model);
        }

        $instance->save();

        // Dispatch jobs to process the newly created server
        if ($instanceType === InstanceTypeEnum::Server) {
            CreateServerJob::dispatch($instance)->onQueue('polling');
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
}
