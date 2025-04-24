<?php

namespace App\Jobs;

use App\Data\InstanceData;
use App\Data\NotificationData;
use App\Enums\NotificationTypeEnum;
use App\Events\InstanceStatusUpdatedEvent;
use App\Events\NotifyUserEvent;
use App\Models\Instance;
use App\Models\Package;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class InstallPackagesJob implements ShouldQueue
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

    /**
     * @param  Collection<int, Package>  $selectedPackages
     */
    public function __construct(
        public User $user,
        public Instance $instance,
        public Collection $selectedPackages,
    ) {}

    public function handle(): void
    {
        // No need to process anything if no packages were selected
        if ($this->selectedPackages->isEmpty()) {
            return;
        }

        $file = Storage::disk('local')->get('package-installation-template.sh');

        // Create a fluent string to manipulate contents
        $installScript = Str::of($file);

        // Pass $installScript by reference
        $this->selectedPackages->each(function ($package) use (&$installScript) {
            $installScript = $installScript->newLine()->append($package->command);
        });

        // Time to send the instructions to our virtual machine!
        try {
            $response = Http::proxmox()
                ->withQueryParameters([
                    'node' => $this->instance->node,
                    'vmid' => $this->instance->vm_id,
                ])
                ->attach('config_file', $installScript, 'package-installation-script.sh')
                ->post('/software/configure_vm_custom');

            if (! $response->successful() || $response->json()['status'] !== 'completed') {
                Log::warning('{job}: Response unsuccessful. Message: {message}', [
                    'job' => "[ID: {$this->job->getJobId()}, Name: {$this->job->getName()}]",
                    'message' => $response->body(),
                ]);

                $this->release($this->backoff);
            }

            // Final step in the chain. Instance is now ready to be used by end user
            $this->instance->is_ready = true;
            $this->instance->save();

            $notification = NotificationData::from([
                'title' => 'Server setup completed',
                'description' => 'Your server is now ready to be used. You can now log in and start using it.',
                'notificationType' => NotificationTypeEnum::Success,
            ]);

            NotifyUserEvent::dispatch($this->user, $notification);

            $nextStep = 5;
            $data = InstanceData::from($this->instance);
            InstanceStatusUpdatedEvent::dispatch($nextStep, $data);
        } catch (ConnectionException $exception) {
            Log::error('{job}: Connection failed. Retrying. Error message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}, Name: {$this->job->getName()}]",
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

        Log::error('Package installation failed. Instance has been deployed without packages. Message: {message}', [
            'message' => $exception?->getMessage(),
        ]);
    }
}
