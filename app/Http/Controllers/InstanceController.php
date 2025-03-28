<?php

namespace App\Http\Controllers;

use App\Data\InstanceData;
use App\Models\Container;
use App\Models\Instance;
use App\Models\Server;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InstanceController extends Controller
{
    public function index(Request $request): Response
    {
        // Get all instances by user
        $instances = Instance::query()
            ->with('created_by')
            ->where('created_by', '=', $request->user()->id)
            ->get();

        return Inertia::render('Dashboard', [
            'instances' => InstanceData::collect($instances),
        ]);
    }

    public function servers(Request $request): Response
    {
        // Get all instances that are of type 'server'
        $instances = Instance::query()
            ->with('created_by')
            ->where('created_by', '=', $request->user()->id)
            ->where('instanceable_type', '=', Server::class)
            ->get();

        return Inertia::render('Servers/IndexServers', [
            'instances' => InstanceData::collect($instances),
        ]);
    }

    public function containers(Request $request): Response
    {
        // Get all instances that are of type 'container'
        $instances = Instance::query()
            ->with('created_by')
            ->where('created_by', '=', $request->user()->id)
            ->where('instanceable_type', '=', Container::class)
            ->get();

        return Inertia::render('Containers/IndexContainers', [
            'instances' => InstanceData::collect($instances),
        ]);
    }
}
