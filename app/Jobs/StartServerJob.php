<?php

namespace App\Jobs;

use App\Events\InstanceCreationFailedEvent;
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

class StartServerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 10;

    public function __construct(
        public Instance $instance
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::proxmox()->withQueryParameters([
                'node' => $this->instance->node,
                'vmid' => $this->instance->vm_id,
            ])->post('/vm/start_vm');
        } catch (ConnectionException $exception) {
            Log::error('{job}: Connection failed. Retrying. Error message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}]",
                'message' => $exception->getMessage(),
            ]);

            $this->release($this->backoff);

            return;
        }

        if (! $response->successful()) {
            Log::warning('{job}: Response unsuccessful. Message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}]",
                'message' => $response->body(),
            ]);
        }

        $json = $response->json();

        Cache::put("instance.{$this->instance->id}.upid", $json['task']);
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
