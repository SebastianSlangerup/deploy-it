<?php

namespace App\Services;

use App\Models\Environment;
use Carbon\CarbonInterval;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class EnvironmentService
{
    public array $environmentVms = [];
    public array $apiVms = [];

    /**
     * @throws ConnectionException
     */
    public function getEnvironments(): array
    {
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
            ->get(config('app.api.endpoint').'/cnc/vm/list_all_vm_ids');

        if (! $response->failed()) {
            $json = $response->json();

            $environments = Environment::all();

            $apiArray = [];
            foreach ($json as $node) {
                foreach ($node['vm_ids'] as $vm) {
                    // Convert the uptime in seconds into a more human-readable format, before saving
                    $apiArray['vmid'] = $vm['vmid'];
                    $apiArray['uptime'] = CarbonInterval::seconds($vm['uptime'])->cascade()->forHumans();
                    $apiArray['status'] = $vm['status'];

                    $this->apiVms[] = $apiArray;
                }
            }

            $vmArray = [];
            foreach ($environments as $environment) {
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

    public function mergeValues(array $to, array $from): array {
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
}
