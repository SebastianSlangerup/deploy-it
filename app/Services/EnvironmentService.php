<?php

namespace App\Services;

use App\Models\Environment;
use Carbon\CarbonInterval;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class EnvironmentService
{
    public array $vms = [];

    /**
     * @throws ConnectionException
     */
    public function getEnvironments(): array
    {
        $response = Http::timeout(3)->get(config('app.api.endpoint').'/vm/list_all_vm_ids');

        // TODO: BIG REWRITES YOU DUMMY!
        if (! $response->failed()) {
            $json = $response->json();

            $environments = Environment::all();

            $array1 = [];
            $array2 = [];

            foreach ($json as $node) {
                foreach ($node['vm_ids'] as $vm) {
                    // Convert the uptime in seconds into a more human-readable format, before saving
                    $apiArray['uptime'] = CarbonInterval::seconds($vm['uptime'])->cascade()->forHumans();
                    $apiArray['status'] = $vm['status'];

                    $array1[] = $apiArray;
                }
            }

            foreach ($environments as $environment) {
                $vmArray['name'] = $environment->name;
                $vmArray['vmid'] = $environment->vm_id;
                $vmArray['node'] = $environment->node;
                $vmArray['cores'] = $environment->cores;
                $vmArray['memory'] = $environment->memory;
                $vmArray['created_by'] = $environment->user->name;

                $array2[] = $vmArray;
            }

            $totalArray = collect($array1)->merge($array2);
            dd($totalArray);

        }

        return $this->vms;
    }
}
