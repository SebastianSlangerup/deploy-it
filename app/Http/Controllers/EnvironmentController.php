<?php

namespace App\Http\Controllers;

use App\Jobs\FinishEnvironmentSetupJob;
use App\Jobs\StartEnvironmentJob;
use App\Models\Dependency;
use App\Models\Environment;
use App\Models\Node;
use App\Models\Template;
use App\Services\EnvironmentService;
use App\Services\HttpService;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HigherOrderCollectionProxy;
use Inertia\Inertia;
use Inertia\Response;
use LaravelIdea\Helper\App\Models\_IH_Dependency_C;
use Symfony\Component\Yaml\Yaml;

class EnvironmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        try {
            $vms = (new EnvironmentService)->getEnvironments();
        } catch (ConnectionException) {
            return Inertia::render('Dashboard', [
                'error' => Environment::ERROR_CONNECTION_FAILED,
                'vms' => '',
            ]);
        }

        return Inertia::render('Dashboard', [
            'vms' => $vms,
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Contact');
    }

    public function control(Environment $environment, string $option): \Illuminate\Http\Client\Response|RedirectResponse
    {
        try {
            HttpService::prepareRequest()
                ->withQueryParameters([
                    'node' => $environment->node->hostname,
                    'vmid' => $environment->vm_id,
                ])
                ->post(config('app.api.endpoint')."/cnc/vm/{$option}_vm");

        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with(['error' => Environment::ERROR_CONNECTION_FAILED]);
        }

        return redirect()->back()->with(['info' => 'Performing action, please wait...']);
    }

    public function delete(Environment $environment): RedirectResponse
    {
        try {
            $status = EnvironmentService::getStatus($environment);
            if ($status === 'running') {
                return redirect()->back()->with(['warning' => 'Turn VM off before deleting']);
            }

            HttpService::prepareRequest()
                ->withQueryParameters([
                    'node' => $environment->node->hostname,
                    'vmid' => $environment->vm_id,
                ])
                ->delete(config('app.api.endpoint').'/cnc/vm/delete_vm');

            $environment->delete();

        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with(['error' => Environment::ERROR_CONNECTION_FAILED]);
        }

        return redirect()->back()->with(['success' => 'VM has been deleted']);
    }

    /**
     * @return Dependency[]|HigherOrderBuilderProxy|HigherOrderCollectionProxy|_IH_Dependency_C|mixed
     */
    public function getDependencies(int $templateId)
    {
        $template = Template::query()->findOrFail($templateId);

        return $template->dependencies;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Validate the user's input from the request
        $validated = $request->validate([
            'name' => 'required|alpha_num:ascii|max:100',
            'password' => 'required|max:100',
            'description' => 'required|string|max:60000',
            'node_id' => 'required|integer',
            'cores' => 'required|digits_between:1,4',
            'memory' => 'required',
            'template' => 'required',
        ]);

        // Merge extra information for the API
        // that the user does not need to know about or interact with
        $this->addPreConfigValues($request, $validated);

        $node = Node::query()->findOrFail($validated['node_id']);

        // Grab only the dependencies that have been selected by the user
        $dependencies = collect($request->get('dependencies'));
        $dependencies = $dependencies->filter(function ($value) {
            return $value === true;
        });

        // Generate a yaml file containing bash commands for installing the selected dependencies
        $yamlFile = $this->writeYamlFile($dependencies);

        // Create the VM with the pre-configured values
        try {
            $response = HttpService::prepareRequest()
                ->withQueryParameters([
                    'node' => $node->hostname,
                    'sshkeys' => Auth::user()->publicKeyContents(),
                ])
                ->post(config('app.api.endpoint').'/cnc/vm/create-vm-pre-config', [
                    // Wrap the request inputs in a new array to satisfy the API's expectations
                    'config' => $this->getPreConfigValues($request),
                ]);

            if ($response->failed()) {
                return redirect()->back()->with('error', Environment::ERROR_CONNECTION_FAILED);
            }
        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with('error', Environment::ERROR_CONNECTION_FAILED);
        }


        $environment = Environment::query()->create([
            ...$validated,
            'vm_id' => $response->json('vmid'),
            'user_id' => Auth::id(),
        ]);

        StartEnvironmentJob::dispatch($environment)->delay(now()->addSeconds(10));
        // Dispatch a job with our yaml file and environment to begin installing the dependencies on the newly created VM
        FinishEnvironmentSetupJob::dispatch($environment, $yamlFile)
            ->delay(now()->addMinutes(5))
            ->onQueue('setups');

        return redirect()->route('dashboard')->with(['success' => "VM has been created!\nIt may take up to 5 minutes to finish setting up your VM"]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Environment $environment)
    {
        $response = HttpService::prepareRequest()
            ->withQueryParameters([
                'node' => $environment->node->hostname,
                'vmid' => $environment->vm_id,
            ])
            ->get(config('app.api.endpoint').'/cnc/vm/get_vm_status');
        $environmentStatus = $response->json();

        // Merge more details values from the endpoint with our environment model
        $environment = array_merge($environment->toArray(), $environmentStatus);

        return Inertia::render('Environment/Show', [
            'environment' => $environment
        ]);
    }

    /**
     * Convert the collection of dependencies into a new collection of the command for said dependency
     */
    private function getDependencyCommands(array|Collection $dependencies): array|Collection
    {
        $dependencyCommands = collect();
        foreach ($dependencies as $key => $value) {
            $dependency = Dependency::query()->where('name', $key)->first();
            $dependencyCommands->push($dependency->command);
            $dependencies->pull($key);
        }

        return $dependencyCommands;
    }

    /**
     * Write a yaml file containing bash commands for installing dependencies
     */
    private function writeYamlFile(Collection $dependencies): string
    {
        $dependencyCommands = $this->getDependencyCommands($dependencies);

        $yamlArray = [];
        foreach ($dependencyCommands as $command) {
            $yamlArray['runcmd'][] = $command;
        }

        return Yaml::dump($yamlArray);
    }

    /**
     * Return the pre-configuration values that the VM-creation API expects
     */
    private function getPreConfigValues(Request $request): array
    {
        return $request->only(
            'agent',
            'boot',
            'cicustom',
            'cipassword',
            'ciuser',
            'ide2',
            'ipconfig0',
            'cores',
            'memory',
            'name',
            'net0',
            'scsi0',
            'scsihw',
            'serial0',
            'vga',
            'start'
        );
    }

    /**
     * Add the pre-configuration values that the VM-createion API expects
     */
    private function addPreConfigValues(Request $request, array $validated = null): void
    {
        $request->merge([
            'agent' => 'enabled=1',
            'boot' => 'order=scsi0;ide2',
            'cicustom' => 'vendor=local:snippets/base_ubuntu.yml',
            'cipassword' => $validated['password'] ?? config('app.api.cipassword'),
            'ciuser' => $validated['name'] ?? config('app.api.ciuser'),
            'ide2' => 'local:cloudinit',
            'ipconfig0' => 'ip=dhcp',
            'net0' => 'virtio,bridge=vmbr0',
            'scsi0' => 'local:0,import-from=/root/jammy-server-cloudimg-amd64.img',
            'scsihw' => 'virtio-scsi-pci',
            'serial0' => 'socket',
            'vga' => 'serial0',
        ]);
    }
}
