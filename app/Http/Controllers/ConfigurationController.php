<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ConfigurationController extends Controller
{
    public function get()
    {
        $response = Http::proxmox()->get('/v1/vm/get_all_configurations');

        $json = $response->json();

        dd($json);
    }
}
