<?php

namespace App\Http\Controllers;

use App\Models\Environment;
use App\Models\Template;
use App\Services\EnvironmentService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

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

    public function control(int $vmid, string $option)
    {
        try {
            Http::timeout(3)->withQueryParameters([
                'node' => 'pve',
                'vmid' => $vmid,
            ])->post(config('app.api.endpoint')."/vm/{$option}_vm");
        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with(['error' => Environment::ERROR_CONNECTION_FAILED]);
        }

        return redirect()->route('dashboard');
    }

    public function delete(int $vmid)
    {
        try {
            Http::timeout(3)->withQueryParameters([
                'node' => 'pve',
                'vmid' => $vmid,
            ])->delete(config('app.api.endpoint')."/vm/delete_vm");
        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with(['error' => Environment::ERROR_CONNECTION_FAILED]);
        }

        return redirect()->route('dashboard');
    }

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

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'node' => 'required|string',
            'cores' => 'required|digits_between:1,4',
            'memory' => 'required',
            'template' => 'required',
        ]);


        try {
            $response = Http::timeout(3)
                ->withQueryParameters([
                    'node' => $validated['node'],
                    'sshkeys' => Auth::user()->public_key
                ])
                ->post(config('app.api.endpoint').'/vm/create-vm-pre-config', [
                    // Wrap the request inputs in a new array to satisfy the API's expectations
                    'config' => $request->only(
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
                    ),
                ]);

            if ($response->failed()) {
                dd($response);
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
}
