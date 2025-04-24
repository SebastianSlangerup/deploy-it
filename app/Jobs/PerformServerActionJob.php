<?php

namespace App\Jobs;

use App\Data\NotificationData;
use App\Enums\InstanceActionsEnum;
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

class PerformServerActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Instance $instance,
        public InstanceActionsEnum $action,
    ) {}

    public function handle(): void
    {
        try {
            // Perform action
            $response = Http::proxmox()->withQueryParameters([
                'node' => $this->instance->node,
                'vmid' => $this->instance->vm_id,
            ])->post("/vm/{$this->action->value}_vm");

            if (! $response->successful()) {
                Log::warning('{job}: Response unsuccessful. Message: {message}', [
                    'job' => "[ID: {$this->job->getJobId()}]",
                    'message' => $response->body(),
                ]);

                $this->fail();
            }

            // Check return value
            $upid = $response->json()['task'];

            $response = Http::proxmox()->withQueryParameters([
                'node' => $this->instance->node,
                'upid' => $upid,
            ])->get('/task/get_task_status');

            if (! $response->successful() || $response->json('exitstatus') !== 'OK') {
                $this->fail();

                $notification = NotificationData::from([
                    'title' => "Action {$this->action->value} failed",
                    'description' => 'Could not complete the action. Please try again later.',
                    'notificationType' => NotificationTypeEnum::Error,
                ]);

                NotifyUserEvent::dispatch($this->user, $notification);
            }

            $notification = NotificationData::from([
                'title' => "Action {$this->action->value} completed",
                'description' => 'The action has been completed.',
                'notificationType' => NotificationTypeEnum::Success,
            ]);

            NotifyUserEvent::dispatch($this->user, $notification);
        } catch (ConnectionException $exception) {
            Log::error('{job}: Connection failed. Error message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}]",
                'message' => $exception->getMessage(),
            ]);

            $this->fail();
        }
    }
}
