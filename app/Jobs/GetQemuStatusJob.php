<?php

namespace App\Jobs;

use App\Events\IncrementInstanceFormStepEvent;
use App\Models\Instance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GetQemuStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 60;

    /**
     * The number of seconds to wait before retrying the job
     */
    public int $backoff = 60;

    public function __construct(
        public int $qemuAgentId,
        public Instance $instance,
    ) {}

    public function handle(): void
    {
        $response = Http::proxmox()->post(config('services.proxmox.endpoint').'/qemu-status', [
            'qemuAgentId' => $this->qemuAgentId,
        ]);

        if (! $response->successful()) {
            $this->release(60);
        }

        // Job completed. Dispatch an event to refresh the front-end
        IncrementInstanceFormStepEvent::dispatch(3, $this->instance);
    }
}
