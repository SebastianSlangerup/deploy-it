<?php

namespace App\Jobs;

use App\Models\Instance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateDockerImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $backoff = 10;

    public function __construct(
        public Instance $instance,
        public string $dockerImage,
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::proxmox()
                ->timeout(30)
                ->withQueryParameters([
                    'vmid' => $this->instance->vm_id,
                    'image_name' => $this->dockerImage,
                ])
                ->post('/pull_docker_image');
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

            $this->release($this->backoff);
        }

        $this->instance->is_ready = true;
        $this->instance->save();
    }

    public function failed(?Throwable $exception): void
    {
        $this->instance->delete();

        Log::error('Job failed. Instance has been deleted. Message: {message}', [
            'message' => $exception?->getMessage(),
        ]);
    }
}
