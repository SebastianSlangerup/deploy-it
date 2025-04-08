<?php

namespace App\Jobs;

use App\Events\IncrementInstanceFormStepEvent;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetIpAddressWithQemuAgentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Server $server,
        public readonly int $qemuAgentId,
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::proxmox()->post('/ip-address', [
                'qemuAgentId' => $this->qemuAgentId,
            ]);
        } catch (ConnectionException $exception) {
            Log::error('{job}: Connection failed. Retrying in 60 seconds. Error message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}, Name: {$this->job->getName()}]",
                'message' => $exception->getMessage(),
            ]);

            $this->release(60);

            return;
        }

        if (! $response->successful()) {
            Log::warning('{job}: Response unsuccessful. Message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}, Name: {$this->job->getName()}]",
                'message' => $response->body(),
            ]);

            $this->release(60);

            return;
        }

        $this->server->ip = $response->json()['ip'];
        $this->server->save();

        // Job completed. Dispatch an event to refresh the front-end
        IncrementInstanceFormStepEvent::dispatch(4, $this->server->instance->created_by);
    }
}
