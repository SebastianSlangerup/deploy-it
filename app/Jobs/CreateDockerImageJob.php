<?php

namespace App\Jobs;

use App\Data\InstanceData;
use App\Data\NotificationData;
use App\Enums\NotificationTypeEnum;
use App\Events\NotifyUserEvent;
use App\Events\RefreshFrontendInstanceEvent;
use App\Models\Container;
use App\Models\Instance;
use App\Models\PortNumber;
use App\Models\Server;
use App\Models\User;
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

class CreateDockerImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 10;

    public function __construct(
        public User $user,
        public Instance $instance,
        public Server $server,
    ) {}

    public function handle(): void
    {
        // This is completely mad
        $availablePort = $this->instance->instanceable->getNextAvailablePort($this->server);

        try {
            $response = Http::proxmox()
                ->timeout(30)
                ->withQueryParameters([
                    'node' => $this->instance->node,
                    'vmid' => $this->server->instance->vm_id,
                    'image_name' => $this->instance->instanceable->docker_image,
                    'container_name' => $this->instance->hostname,
                    'port_mapping' => $availablePort.':80',
                ])
                ->post('/software/pull_docker_image');

            if (! $response->successful()) {
                Log::warning('{job}: Response unsuccessful. Message: {message}', [
                    'job' => "[ID: {$this->job->getJobId()}]",
                    'message' => $response->body(),
                ]);

                $this->release($this->backoff);
            }

            PortNumber::query()->create([
                'port' => $availablePort,
                'is_active' => true,
                'container_id' => $this->instance->instanceable->id,
                'allocated_on' => $this->server->id,
            ]);

            sleep(5);

            Http::proxmox()->withQueryParameters([
                'node' => 'node3',
                'hostname' => $this->instance->hostname,
                'ip' => $this->server->ip,
                'port' => $availablePort,
            ])->post('/software/create_proxy_conf');

            $this->instance->status->transitionTo(Started::class);

            $this->instance->is_ready = true;
            $this->instance->save();

            $toast = NotificationData::from([
                'title' => 'Docker image created',
                'description' => 'Your Docker image has been created successfully',
                'notificationType' => NotificationTypeEnum::Success,
            ]);

            $this->instance->loadMorph('instanceable', [
                Container::class => ['port'],
            ]);

            RefreshFrontendInstanceEvent::dispatch(InstanceData::from($this->instance));
            NotifyUserEvent::dispatch($this->user, $toast);
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

        $toast = NotificationData::from([
            'title' => 'Docker image creation failed',
            'description' => 'Could not create docker image. The image has been deleted',
            'notificationType' => NotificationTypeEnum::Error,
        ]);

        NotifyUserEvent::dispatch($this->user, $toast);

        Log::error('Job failed. Instance has been deleted. Message: {message}', [
            'message' => $exception?->getMessage(),
        ]);
    }
}
