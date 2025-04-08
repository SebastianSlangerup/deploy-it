<?php

namespace App\Jobs;

use App\Events\IncrementInstanceFormStepEvent;
use App\Models\Instance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateServerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 60;

    /**
     * The number of seconds to wait before retrying the job
     */
    public int $backoff = 60;

    public function __construct(
        public readonly Instance $instance
    ) {}

    public function handle(): void
    {
        $response = Http::proxmox()->post('/clone-vm', [
            'instance' => $this->instance,
        ]);

        if (! $response->successful()) {
            Log::warning('{job}: Response unsuccessful. Message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}, Name: {$this->job->getName()}]",
                'message' => $response->body(),
            ]);

            $this->release(60);

            return;
        }

        $this->instance->vm_id = $response->json()['vm']['vmid'];
        $this->instance->vm_username = $response->json()['vm']['username'];
        $this->instance->vm_password = Hash::make($response->json()['vm']['password']);
        $this->instance->save();

        // Job completed. Dispatch an event to refresh the front-end
        IncrementInstanceFormStepEvent::dispatch(2, $this->instance);

        $this->prependToChain(new CheckOnTaskIdJob(
            $this->instance,
            $response->json()['task']['task_id']
        ));
    }
}
