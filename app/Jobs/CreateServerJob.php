<?php

namespace App\Jobs;

use App\Data\ConfigurationData;
use App\Models\Instance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateServerJob implements ShouldQueue
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
        public readonly Instance $instance,
        public ConfigurationData $selectedConfiguration
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::proxmox()
                ->post('/clone-vm', [
                    'ciuser' => 'sysadmin',
                    'name' => $this->instance->name,
                    'vmid' => $this->selectedConfiguration->proxmox_configuration_id,
                    'sshkeys' => 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIIDpZc/0UAaCbvtfz1ckZRazVlvz/iqDmHuXFPPypMhc sebastian.slangerup1@gmail.com',
                ]);
        } catch (ConnectionException $exception) {
            Log::error('{job}: Connection failed. Retrying. Error message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}]",
                'message' => $exception->getMessage(),
            ]);

            $this->release();

            return;
        }

        if (! $response->successful()) {
            Log::warning('{job}: Response unsuccessful. Message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}]",
                'message' => $response->body(),
            ]);

            $this->release();

            return;
        }

        $json = $response->json();

        $this->instance->vm_id = $json['vm']['vmid'];
        $this->instance->vm_username = $json['vm']['user'];
        $this->instance->vm_password = Hash::make($json['vm']['password']);

        // Store upid for access in CheckOnTaskIdJob.php
        Cache::put("instance.{$this->instance->id}.upid", $json['tasks'][0]['upid']);
    }

    public function failed(?Throwable $exception): void
    {
        $this->instance->delete();

        Log::error('Job failed. Instance has been deleted. Message: {message}', [
            'message' => $exception?->getMessage(),
        ]);
    }
}
