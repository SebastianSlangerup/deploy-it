<?php

namespace App\Http\Controllers;

use App\Models\Node;
use Inertia\Inertia;
use App\Models\User;
use App\Notifications\UserActivated;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Carbon\CarbonInterval;

class AdminController extends Controller
{

    public function index()
    {
        $non_activated_users = User::where('is_active', 0)->get();
        $activated_users = User::where('is_active', 1)->get(); // This should be 1 for activated users

        $network_info = $this->GetNetwork();
        $node_info = $this->listNodes();
    
        return Inertia::render('Admin/index', [
            'non_activated_users' => $non_activated_users,
            'activated_users' => $activated_users,
            'network_info' => $network_info,
            'node_info' => $node_info,
        ]);
    }
    
    public function GetNetwork()
    {
        try {
            $res = Http::timeout(3)->get(config('app.api.endpoint')."/map_hostname_and_ip");
            return $res->json();

        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with(['message' => "Connection Failed..."]);
        }

    }
 
    public function listNodes()
    {
        try {
            $res = Http::timeout(3)->get(config('app.api.endpoint') . "/list_nodes");
    
            $nodes = $res->json();
            dd($nodes);
            // Node::query()->
            $nodes = array_map([$this, 'formatNodeData'], $nodes);
    
            return $nodes;
        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with(['message' => "Connection Failed..."]);
        }
    }

    
    private function bytesToGigabytes($bytes)
    {
        return round($bytes / 1024 / 1024 / 1024, 2) . ' GB';
    }

    private function formatCpuUsage($cpu)
    {
        return round($cpu * 100, 2) . '%';
    }

    private function secondsToHumanReadable($seconds)
    {
        return CarbonInterval::seconds($seconds)->cascade()->forHumans();
    }

    private function formatNodeData($node)
    {
        $node['disk'] = $this->bytesToGigabytes($node['disk']);
        $node['maxdisk'] = $this->bytesToGigabytes($node['maxdisk']);
        $node['mem'] = $this->bytesToGigabytes($node['mem']);
        $node['maxmem'] = $this->bytesToGigabytes($node['maxmem']);
        $node['uptime'] = $this->secondsToHumanReadable($node['uptime']);
        $node['cpu'] = $this->formatCpuUsage($node['cpu']);
        return $node;
    }

    public function activate(int $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'is_active' => true,
        ]);

        $user->notify(new UserActivated());

        return redirect()->back();
    }

    public function deactivate(int $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'is_active' => false,
        ]);

        return redirect()->back();
    }
}


