<?php

namespace App\Jobs;

use App\Data\NotificationData;
use App\Enums\NotificationTypeEnum;
use App\Events\NotifyUserEvent;
use App\Models\Instance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class InstallDockerEngineJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $backoff = 10;

    public int $tries = 5;

    public function __construct(
        public User $user,
        public Instance $server,
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::proxmox()->withQueryParameters([
                'node' => $this->server->node,
                'vmid' => $this->server->vm_id,
            ])->post('/software/install_docker_engine');

            if (! $response->successful()) {
                Log::warning('{job}: Response unsuccessful. Message: {message}', [
                    'job' => "[ID: {$this->job->getJobId()}]",
                    'message' => $response->body(),
                ]);

                $this->release($this->backoff);
            }
        } catch (ConnectionException $exception) {
            Log::error('{job}: Connection failed. Error message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}]",
                'message' => $exception->getMessage(),
            ]);

            $this->release($this->backoff);
        }
    }

    public function failed(?Throwable $exception): void
    {
        $this->server->delete();

        $toast = NotificationData::from([
            'title' => 'Docker image creation failed',
            'description' => 'Could not create docker image. The image has been deleted',
            'notificationType' => NotificationTypeEnum::Error,
        ]);

        NotifyUserEvent::dispatch($this->user, $toast);

        Log::error('Docker Engine installation failed. Instance has been deleted. Message: {message}', [
            'message' => $exception?->getMessage(),
        ]);
    }
}
