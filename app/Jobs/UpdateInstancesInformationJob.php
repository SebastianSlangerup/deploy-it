<?php

namespace App\Jobs;

use App\Data\InstanceData;
use App\Enums\InstanceStatusEnum;
use App\Events\RefreshFrontendInstanceEvent;
use App\Models\Instance;
use App\States\InstanceStatusState\Started;
use App\States\InstanceStatusState\Stopped;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateInstancesInformationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $node,
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::proxmox()->withQueryParameters([
                'node' => $this->node,
            ])->get('/vm/list_vms');

            if (! $response->successful()) {
                Log::warning('{job}: Response unsuccessful. Message: {message}', [
                    'job' => "[ID: {$this->job->getJobId()}]",
                    'message' => $response->body(),
                ]);
            }

            // Iterate over each instance received from the API platform
            $response->collect()->each(function (array $data) {
                $instance = Instance::query()
                    ->where('node', '=', $this->node)
                    ->where('vm_id', '=', $data['vmid'])
                    ->first();

                if (! $instance) {
                    return;
                }

                match ($data['status']) {
                    'running' => $instance->status->transitionTo(Started::class),
                    'stopped' => $instance->status->transitionTo(Stopped::class),
                };

                $instance->save();

                RefreshFrontendInstanceEvent::dispatch(InstanceData::from($instance));
            });

        } catch (ConnectionException $exception) {
            Log::error('{job}: Connection failed. Error message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}]",
                'message' => $exception->getMessage(),
            ]);

            $this->fail();
        }
    }
}
