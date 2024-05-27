<?php

namespace App\Services;

use App\Models\Environment;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class EnvironmentService
{
    public array $vms = [];

    public function getEnvironments(): array
    {
        $response = Http::get(config('app.api.endpoint').'/vm/list_all_vm_ids');

        $vmids = [];

        if (! $response->failed()) {
            $json = $response->json();

            foreach ($json as $node) {
                foreach ($node['vm_ids'] as $vm) {

                    //$vmids[] = $vm['vmid'];
                    //Environment::query()->whereIn('vm_id', $vmids)->get();

                    // Convert the uptime in seconds into a more human-readable format, before saving
                    $vm['uptime'] = CarbonInterval::seconds($vm['uptime'])->cascade()->forHumans();

                    $environment = Environment::query()->create([
                        'name' => $vm['name'],
                        'user_id' => 1,
                        'vm_id' => $vm['vmid'],
                        'cores' => $vm['cpus'],
                        'memory' => $vm['maxmem'],
                    ]);

                    $vmArray = [];
                    $vmArray['name'] = $environment->name;
                    $vmArray['vmid'] = $environment->vm_id;
                    $vmArray['uptime'] = $vm['uptime'];
                    $vmArray['status'] = $vm['status'];
                    $vmArray['cores'] = $environment->cores;
                    $vmArray['memory'] = $environment->memory;
                    $vmArray['created_by'] = $environment->user_id;

                    $this->vms[] = $vmArray;
                }
            }
        }

        return $this->vms;
    }
}
