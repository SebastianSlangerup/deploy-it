<?php

namespace App\Jobs;

use App\Events\InstanceStatusUpdatedEvent;
use App\Models\Instance;
use App\Models\Package;
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
        public Instance $instance,
        public Collection $selectedPackages,
    ) {}

    public function handle(): void
    {
        $file = Storage::disk('local')->get('package-installation-template.sh');

        # Create a fluent string to manipulate contents
        $installScript = Str::of($file);

        # Pass $installScript by reference
        $this->selectedPackages->each(function ($package) use (&$installScript) {
            $installScript = $installScript->newLine()->append($package->command);
        });

        // Time to send the instructions to our virtual machine!
        try {
            $response = Http::proxmox()
                ->withQueryParameters(['vmid' => $this->instance->vm_id])
                ->attach('config_file', $installScript, 'package-installation-script.sh')
                ->post('/configure_vm_custom');
        } catch (ConnectionException $exception) {
            Log::error('{job}: Connection failed. Retrying. Error message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}, Name: {$this->job->getName()}]",
                'message' => $exception->getMessage(),
            ]);

            $this->release();

            return;
        }

        if (! $response->successful()) {
            Log::warning('{job}: Response unsuccessful. Message: {message}', [
                'job' => "[ID: {$this->job->getJobId()}, Name: {$this->job->getName()}]",
                'message' => $response->body(),
            ]);

            $this->release();

            return;
        }

        // Final step in the chain. Instance is now ready to be used by end user
        $this->instance->is_ready;
        $this->instance->save();

        $nextStep = 5;
        InstanceStatusUpdatedEvent::dispatch($nextStep, $this->instance);
    }

    public function failed(?Throwable $exception): void
    {
        $this->instance->delete();

        Log::error('Job failed. Instance has been deleted. Message: {message}', [
            'message' => $exception?->getMessage(),
        ]);
    }
}
