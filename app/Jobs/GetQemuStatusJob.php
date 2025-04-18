<?php

namespace App\Jobs;

use App\Events\InstanceStatusUpdatedEvent;
use App\Models\Instance;
use App\States\InstanceStatusState\Started;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GetQemuStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 5;

    /**
     * The number of seconds to wait before retrying the job
     */
    public int $backoff = 10;

    public function __construct(
        public Instance $instance,
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::proxmox()
                ->withQueryParameters(['vmid' => $this->instance->vm_id])
                ->get('/get_qemu_agent_status');
        } catch (ConnectionException $exception) {
            Log::error('{job}: Connection failed. Retrying. Error message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}]",
                'message' => $exception->getMessage(),
            ]);

            $this->release();

            return;
        }

        if (! $response->successful() || $response->json('status') !== 'Running') {
            $this->release();

            return;
        }

        $this->instance->status->transitionTo(Started::class);

        // Job completed. Dispatch an event to refresh the front-end with the next step
        $nextStep = 3;
        InstanceStatusUpdatedEvent::dispatch($nextStep, $this->instance);
    }

    public function failed(?Throwable $exception): void
    {
        $this->instance->delete();

        Log::error('Job failed. Instance has been deleted. Message: {message}', [
            'message' => $exception?->getMessage(),
        ]);
    }
}
