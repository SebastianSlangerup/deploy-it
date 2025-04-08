<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ConfigurationController extends Controller
{
    public function get()
    {
        $response = Http::proxmox()->get('/get_all_configurations');

        $json = $response->json();

        dd($json);
    }
}
