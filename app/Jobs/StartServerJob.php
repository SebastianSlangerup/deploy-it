<?php

namespace App\Jobs;

use App\Data\NotificationData;
use App\Enums\NotificationTypeEnum;
use App\Events\NotifyUserEvent;
use App\Models\Instance;
use App\Models\User;
use App\States\InstanceStatusState\Started;
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
        public User $user,
        public Instance $instance
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::proxmox()->withQueryParameters([
                'node' => $this->instance->node,
                'vmid' => $this->instance->vm_id,
            ])->post('/vm/start_vm');

            if (! $response->successful()) {
                Log::warning('{job}: Response unsuccessful. Message: {message}', [
                    'job' => "[ID: {$this->job->getJobId()}]",
                    'message' => $response->body(),
                ]);

                $this->release($this->backoff);
            }

            $json = $response->json();

            $this->instance->status->transitionTo(Started::class);
            $this->instance->save();

            Cache::put("instance.{$this->instance->id}.upid", $json['task']);
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
        $this->instance->delete();

        $notification = NotificationData::from([
            'title' => 'Server creation failed',
            'description' => 'The server creation failed. The instance has been deleted. Please try creating the instance again.',
            'notificationType' => NotificationTypeEnum::Error,
        ]);

        NotifyUserEvent::dispatch($this->user, $notification);

        Log::error('Job failed. Instance has been deleted. Message: {message}', [
            'message' => $exception?->getMessage(),
        ]);
    }
}
