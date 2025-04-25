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

class PerformServerActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Instance $instance,
        public string $action,
    ) {}

    public function handle(): void
    {
        try {
            // Perform action
            $response = Http::proxmox()->withQueryParameters([
                'node' => $this->instance->node,
                'vmid' => $this->instance->vm_id,
            ])->post("/vm/{$this->action}_vm");

            if (! $response->successful()) {
                Log::warning('{job}: Response unsuccessful. Message: {message}', [
                    'job' => "[ID: {$this->job->getJobId()}]",
                    'message' => $response->body(),
                ]);

                $this->fail();
            }

            // Give the job a second to let the API platform process the request before checking the task
            sleep(1);

            // Check return value
            $upid = $response->json()['task'];

            $response = Http::proxmox()->withQueryParameters([
                'node' => $this->instance->node,
                'upid' => $upid,
            ])->get('/task/get_task_status');

            if (! $response->successful()) {
                $this->fail();

                $notification = NotificationData::from([
                    'title' => "Action {$this->action} failed",
                    'description' => 'Could not complete the action. Please try again later.',
                    'notificationType' => NotificationTypeEnum::Error,
                ]);

                NotifyUserEvent::dispatch($this->user, $notification);
            }

            if ($response->json('exitstatus') === 'running') {
                // Retry the job if the process is still ongoing
                $this->release(1);
            }

            $notification = NotificationData::from([
                'title' => "Action {$this->action} completed",
                'description' => 'The action has been completed.',
                'notificationType' => NotificationTypeEnum::Success,
            ]);

            NotifyUserEvent::dispatch($this->user, $notification);

            // Force-refresh the frontend
            UpdateInstancesInformationJob::dispatch($this->instance->node);
        } catch (ConnectionException $exception) {
            Log::error('{job}: Connection failed. Error message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}]",
                'message' => $exception->getMessage(),
            ]);

            $this->fail();
        }
    }
}
