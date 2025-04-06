<?php

namespace App\Jobs;

use App\Events\IncrementInstanceFormStepEvent;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GetIpAddressWithQemuAgentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Server $server,
        public readonly int $qemuAgentId,
    ) {}

    public function handle(): void
    {
        $response = Http::proxmox()->post(config('services.proxmox.endpoint').'/ip-address', [
            'qemuAgentId' => $this->qemuAgentId,
        ]);

        if (! $response->successful()) {
            $this->release(60);
        }

        $this->server->ip = $response->json()['ip'];
        $this->server->save();

        // Job completed. Dispatch an event to refresh the front-end
        IncrementInstanceFormStepEvent::dispatch(4, $this->server->instance->created_by);
    }
}
