<?php

namespace App\Jobs;

use App\Models\Environment;
use App\Services\EnvironmentService;
use App\Services\TokenService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstallEnvironmentDependenciesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Environment $environment,
        public string $yamlFile,
    ) {
    }

    public function handle(): void
    {
        $response = Http::timeout(3)
            ->withToken(TokenService::get())
            // Retry callback in case the request fails
            ->retry(2, 10, function (Exception $exception, PendingRequest $request) {
                // If we are not getting a Request Exception, or a 401 status code, dont bother retrying the request
                if (! $exception instanceof RequestException || $exception->response->status() !== 401 || $exception->response->status() !== 422) {
                    return false;
                }

                $request->withToken(TokenService::new());

                return true;
            })
            ->withQueryParameters([
                'node' => $this->environment->node->hostname,
                'vmid' => $this->environment->vm_id,
            ])
            ->attach('file', $this->yamlFile, 'instructions.yml', ['Content-Type' => 'application/octet-stream'])
            ->post(config('app.api.endpoint').'/cnc/vm/execute-commands');
    }
}