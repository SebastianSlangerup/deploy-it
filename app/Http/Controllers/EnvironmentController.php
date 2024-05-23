<?php

namespace App\Http\Controllers;

use App\Services\EnvironmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class EnvironmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vms = (new EnvironmentService)->getEnvironments();

        return Inertia::render('Dashboard', [
            'vms' => $vms,
        ]);
    }

    public function controlEnvironment(int $vmid, string $option)
    {
        Http::withQueryParameters([
            'node' => 'pve',
            'vmid' => $vmid,
        ])->post(config('app.api.endpoint')."/vm/{$option}_vm");

        return redirect()->route('dashboard');
    }

    public function deleteEnvironment(int $vmid)
    {
        Http::withQueryParameters([
            'node' => 'pve',
            'vmid' => $vmid,
        ])->delete(config('app.api.endpoint')."/vm/delete_vm");

        return redirect()->route('dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
