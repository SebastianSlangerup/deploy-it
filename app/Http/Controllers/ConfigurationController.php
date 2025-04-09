<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Support\Facades\Http;

class ConfigurationController extends Controller
{
    public function get()
    {
        $response = Http::proxmox()->get('/get_all_configurations');

        $json = $response->json();

        foreach ($json as $configurationId => $configuration) {
            Configuration::query()->create([
                'name' => $configuration['name'],
                'description' => $configuration['desc'],
                'cores' => $configuration['hardware']['cores'],
                'memory' => $configuration['hardware']['memory'],
                'disk_space' => $configuration['hardware']['disksize'],
                'proxmox_configuration_id' => $configurationId,
            ]);
        }

        return Configuration::all();
    }
}
