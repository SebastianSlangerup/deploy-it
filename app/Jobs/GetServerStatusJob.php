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
use Illuminate\Support\Str;
use Throwable;

class GetServerStatusJob implements ShouldQueue
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
        public User $user,
        public Instance $instance,
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::proxmox()
                ->withQueryParameters([
                    'node' => $this->instance->node,
                    'vmid' => $this->instance->vm_id,
                ])
                ->get('/qemu/check_apt_writable');

            if (! $response->successful() || Str::lower($response->json('status')) !== 'running') {
                $this->release($this->backoff);
            }
            Log::info('Running GetServerStatusJob. Response {response}', ['response' => $response->body()]);
        } catch (ConnectionException $exception) {
            Log::error('{job}: Connection failed. Retrying. Error message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}]",
                'message' => $exception->getMessage(),
            ]);

            $this->release($this->backoff);
        }
    }

    public function failed(?Throwable $exception): void
    {
        // Server can still be ready without packages.
        $this->instance->is_ready = true;
        $this->instance->save();

        $notification = NotificationData::from([
            'title' => 'Package installation failed',
            'description' => 'Could not install your desired packages. Your server is still ready to be used. Please contact support if you need help.',
            'notificationType' => NotificationTypeEnum::Warning,
        ]);

        NotifyUserEvent::dispatch($this->user, $notification);

        Log::error('Package installation failed. Stopping chain here. Message: {message}', [
            'message' => $exception?->getMessage(),
        ]);
    }
}
