<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\Environment;
use App\Models\Template;
use App\Services\EnvironmentService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Symfony\Component\Yaml\Yaml;

class EnvironmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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

    public function control(Environment $environment, string $option)
    {
        try {
            Http::timeout(3)
                ->withToken(TokenService::get())
                // Retry callback in case the request fails
                ->retry(2, 10, function (Exception $exception, PendingRequest $request) {
                    // If we are not getting a Request Exception, or a 401 status code, dont bother retrying the request
                    if (! $exception instanceof RequestException || $exception->response->status() !== 401) {
                        return false;
                    }

                    $request->withToken(TokenService::new());

                    return true;
                })
                ->withQueryParameters([
                    'node' => $environment->node->hostname,
                    'vmid' => $environment->vm_id,
                ])
                ->post(config('app.api.endpoint')."/cnc/vm/{$option}_vm");

        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with(['message' => Environment::ERROR_CONNECTION_FAILED]);
        }

        return redirect()->route('dashboard')->with(['message' => 'Performing action, please wait...']);
    }

    public function delete(Environment $environment)
    {
        try {
            Http::timeout(3)
                ->withToken(TokenService::get())
                // Retry callback in case the request fails
                ->retry(2, 10, function (Exception $exception, PendingRequest $request) {
                    // If we are not getting a Request Exception, a 401 status code, or a 422 status code, dont bother retrying the request
                    if (! $exception instanceof RequestException || $exception->response->status() !== 401) {
                        return false;
                    }

                    $request->withToken(TokenService::new());

                    return true;
                })
                ->withQueryParameters([
                    'node' => $environment->node->hostname,
                    'vmid' => $environment->vm_id,
                ])
                ->delete(config('app.api.endpoint')."/cnc/vm/delete_vm");
        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with(['error' => Environment::ERROR_CONNECTION_FAILED]);
        }

        return redirect()->route('dashboard');
    }

    /**
     * @param  int  $templateId
     * @return Dependency[]|\Illuminate\Database\Eloquent\HigherOrderBuilderProxy|\Illuminate\Support\HigherOrderCollectionProxy|\LaravelIdea\Helper\App\Models\_IH_Dependency_C|mixed
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
        // Merge extra information for the API
        // that the user does not need to know about or interact with
        $this->addPreConfigValues($request);

        // Validate the user's input from the request
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'node' => 'required',
            'cores' => 'required|digits_between:1,4',
            'memory' => 'required',
            'template' => 'required',
        ]);

        // Grab only the dependencies that have been selected by the user
        $dependencies = collect($request->get('dependencies'));
        $dependencies = $dependencies->filter(function ($value) {
            return $value === true;
        });

        // Generate a yaml file containing bash commands for installing the selected dependencies
        $yamlFile = $this->writeYamlFile($dependencies);

        // Create the VM with the pre-configured values
        try {
            $response = Http::timeout(3)
                ->withToken(TokenService::get())
                // Retry callback in case the request fails
                ->retry(2, 0, function (Exception $exception, PendingRequest $request) {
                    // If we are not getting a Request Exception, or a 401 status code, dont bother retrying the request
                    if (! $exception instanceof RequestException || $exception->response->status() !== 401) {
                        return false;
                    }

                    $request->withToken(TokenService::new());

                    return true;
                })
                ->withQueryParameters([
                    'node' => $validated['node'],
                    'sshkeys' => Auth::user()->public_key
                ])
                ->post(config('app.api.endpoint').'/vm/create-vm-pre-config', [
                    // Wrap the request inputs in a new array to satisfy the API's expectations
                    'config' => $this->getPreConfigValues($request)
                ]);

            if ($response->failed()) {
                return redirect()->back()->with('message', Environment::ERROR_CONNECTION_FAILED);
            }
        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with('message', Environment::ERROR_CONNECTION_FAILED);
        }

        Environment::query()->create([
            ...$validated,
            'vm_id' => $response->json('vmid'),
            'user_id' => Auth::id(),
        ]);

        // Send the bash commands to the VM to begin installing the dependencies
        $newResponse = Http::timeout(3)
            ->withQueryParameters([
                'node' => $validated['node'],
                'vmid' => $response->json('vmid')
            ])
            ->attach('file', $yamlFile)
            ->post(config('app.api.endpoint').'/vm/execute-commands');

        return redirect()->route('dashboard')->with('message', 'Environment created successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Convert the collection of dependencies into a new collection of the command for said dependency
     *
     * @param  array|Collection  $dependencies
     * @return array|Collection
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
     *
     * @param  Collection  $dependencies
     * @return string
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
     *
     * @param  Request  $request
     * @return array
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
        );
    }

    /**
     * Add the pre-configuration values that the VM-createion API expects
     *
     * @param  Request  $request
     * @return void
     */
    private function addPreConfigValues(Request $request)
    {
        $request->merge([
            'agent' => 'enabled=1',
            'boot' => 'order=scsi0;ide2',
            'cicustom' => 'vendor=local:snippets/base_ubuntu.yml',
            'cipassword' => config('app.api.cipassword'),
            'ciuser' => config('app.api.ciuser'),
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
