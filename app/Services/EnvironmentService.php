<?php

namespace App\Services;

use App\Models\Environment;
use Carbon\CarbonInterval;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnvironmentService
{
    public array $environmentVms = [];

    public array $apiVms = [];

    /**
     * @throws ConnectionException
     */
    public function getEnvironments(): array
    {
        $response = HttpService::prepareRequest()
            ->get(config('app.api.endpoint').'/cnc/vm/list_all_vm_ids');

        if (! $response->failed()) {
            $json = $response->json();

            if (Auth::check()) {
                $user = Auth::user();
                $environments = Environment::where('user_id', $user->id)->get();
            } else {
                $environments = Environment::all();
            }

            $apiArray = [];
            foreach ($json as $node) {
                foreach ($node['vm_ids'] as $vm) {
                    // Convert the uptime in seconds into a more human-readable format, before saving
                    $apiArray['vmid'] = $vm['vmid'];
                    try {
                        $apiArray['uptime'] = CarbonInterval::seconds($vm['uptime'])->cascade()->forHumans();
                    } catch (Exception $e) {
                        Log::error($e);
                    }
                    $apiArray['status'] = $vm['status'];

                    $this->apiVms[] = $apiArray;
                }
            }

            $vmArray = [];
            foreach ($environments as $environment) {
                $vmArray['id'] = $environment->id;
                $vmArray['name'] = $environment->name;
                $vmArray['vmid'] = $environment->vm_id;
                $vmArray['node'] = $environment->node;
                $vmArray['cores'] = $environment->cores;
                $vmArray['memory'] = $environment->memory;
                $vmArray['created_by'] = $environment->user->name;

                $this->environmentVms[] = $vmArray;
            }
        }

        return $this->mergeValues($this->environmentVms, $this->apiVms);
    }

    public function mergeValues(array $to, array $from): array
    {
        for ($x = 0; $x < count($to); $x++) {
            for ($y = 0; $y < count($from); $y++) {
                if ($to[$x]['vmid'] === $from[$y]['vmid']) {
                    $to[$x]['status'] = $from[$y]['status'];
                    $to[$x]['uptime'] = $from[$y]['uptime'];
                }
            }
        }

        return $to;
    }

    /**
     * Call API to get status of given environment
     *
     * @throws ConnectionException
     */
    public static function getStatus(Environment $environment): string
    {
        $response = HttpService::prepareRequest()
            ->withQueryParameters([
                'node' => $environment->node->hostname,
                'vmid' => $environment->vm_id,
            ])
            ->get(config('app.api.endpoint').'/cnc/vm/get_vm_status');

        return $response->json('status');
    }

    /**
     * Call the API to get the IPv4 address for the given environment
     *
     * @throws ConnectionException
     */
    public static function getIpv4(Environment $environment): string
    {
        $response = HttpService::prepareRequest()
            ->withQueryParameters([
                'node' => $environment->node->hostname,
                'vmid' => $environment->vm_id,
            ])
            ->get(config('app.api.endpoint').'/cnc/vm/get_vm_ipv4');

        return $response->json('ipv4');
    }
}
