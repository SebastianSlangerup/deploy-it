<?php

namespace App\Jobs;

use App\Models\Environment;
use App\Services\HttpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FinishEnvironmentSetupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Environment $environment,
        public string $yamlFile,
    ) {
    }

    public function handle(): void
    {
        try {
            HttpService::prepareRequest()
                ->withQueryParameters([
                    'node' => $this->environment->node->hostname,
                    'vmid' => $this->environment->vm_id,
                ])
                ->attach('file', $this->yamlFile, 'instructions.yml', ['Content-Type' => 'application/octet-stream'])
                ->post(config('app.api.endpoint').'/cnc/vm/execute-commands');

            // Fetch the IP address for the newly created environment
            $ip = HttpService::prepareRequest()
                ->withQueryParameters([
                    'node' => $this->environment->node->hostname,
                    'vmid' => $this->environment->vm_id,
                ])
                ->get(config('app.api.endpoint').'/cnc/vm/get_vm_ipv4')
                ->json('ipv4');
            // Update environment with new IP
            $this->environment->update(['ip' => $ip]);
        } catch (ConnectionException $e) {
            Log::error($e);
        }
    }
}
