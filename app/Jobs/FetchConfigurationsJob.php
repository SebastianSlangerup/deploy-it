<?php

namespace App\Jobs;

use App\Models\Configuration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class FetchConfigurationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct() {}

    public function handle(): void
    {
        $response = Http::proxmox()->get('/get_all_configurations');

        $configurations = $response->json();

        // Get all configurationIds that already exist with the same proxmox_configuration_id as the one returned from our response
        $existingConfigurationIds = Configuration::query()
            ->whereIn('proxmox_configuration_id', array_keys($configurations))
            ->pluck('proxmox_configuration_id');

        // Reject configurations that already exist
        $configurations = collect($configurations)->reject(fn (array $value, int $key) => $existingConfigurationIds->contains($key));

        foreach ($configurations as $configurationId => $configuration) {
            Configuration::query()->create([
                'name' => $configuration['name'],
                'description' => $configuration['desc'],
                'cores' => $configuration['hardware']['cores'],
                'memory' => $configuration['hardware']['memory'],
                'disk_space' => $configuration['hardware']['disksize'],
                'proxmox_configuration_id' => $configurationId,
            ]);
        }
    }
}
