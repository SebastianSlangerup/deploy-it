<?php

namespace App\Jobs;

use App\Events\InstanceCreationFailedEvent;
use App\Events\InstanceStatusUpdatedEvent;
use App\Models\Instance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckOnTaskIdJob implements ShouldQueue
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
        $upid = Cache::get("instance.{$this->instance->id}.upid");

        try {
            $response = Http::proxmox()->withQueryParameters([
                'node' => $this->instance->node,
                'upid' => $upid,
            ])->get('/task/get_task_status');
        } catch (ConnectionException $exception) {
            Log::error('{job}: Connection failed. Retrying. Error message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}]",
                'message' => $exception->getMessage(),
            ]);

            $this->release($this->backoff);

            return;
        }

        if (! $response->successful() || $response->json('exitstatus') !== 'OK') {
            Log::warning('{job}: Response unsuccessful. Message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}, Name: {$this->job->getName()}]",
                'message' => $response->body(),
            ]);

            $this->release($this->backoff);

            return;
        }

        // Job completed. Dispatch an event to refresh the front-end with the next step
        $nextStep = 2;
        InstanceStatusUpdatedEvent::dispatch($nextStep, $this->instance);
    }

    public function failed(?Throwable $exception): void
    {
        $this->instance->delete();

        InstanceCreationFailedEvent::dispatch($this->instance);

        Log::error('Job failed. Instance has been deleted. Message: {message}', [
            'message' => $exception?->getMessage(),
        ]);
    }
}
