<?php

namespace App\Http\Controllers;

use App\Models\Node;
use App\Models\User;
use App\Notifications\UserActivated;
use App\Services\HttpService;
use Carbon\CarbonInterval;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        $non_activated_users = User::query()->where('is_active', 0)->get();
        $activated_users = User::query()->where('is_active', 1)->get(); // This should be 1 for activated users

        $network_info = $this->GetNetwork();
        $node_info = $this->listNodes();

        return Inertia::render('Admin/index', [
            'non_activated_users' => $non_activated_users,
            'activated_users' => $activated_users,
            'network_info' => $network_info,
            'node_info' => $node_info,
        ]);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $user,
        ]);
    }

    public function GetNetwork()
    {
        try {
            $res = HttpService::prepareRequest()
                ->get(config('app.api.endpoint').'/cnc/map_hostname_and_ip');

            return $res->json();

        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with(['message' => 'Connection Failed...']);
        }

    }

    public function listNodes(): array|RedirectResponse
    {
        try {
            $res = HttpService::prepareRequest()
                ->get(config('app.api.endpoint').'/cnc/list_nodes');

            $nodes = $res->json();

            if (! $res->ok()) {
                return [];
            }

            $nodeArray = [];
            foreach ($nodes as $node) {
                $nodeArray[] = $node['node'];
            }

            // ['pve', 'node1', 'node2']
            $node_db = Node::query()->whereIn('hostname', $nodeArray)->get(['display_name', 'hostname']);

            foreach ($nodes as $key => $value) {
                foreach ($node_db as $db_node) {
                    if ($value['node'] === $db_node['hostname']) {
                        $nodes[$key]['display_name'] = $db_node['display_name'];
                    }
                }
            }

            return array_map([$this, 'formatNodeData'], $nodes);
        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with(['message' => 'Connection Failed...']);
        }
    }

    private function bytesToGigabytes($bytes): string
    {
        return round($bytes / 1024 / 1024 / 1024, 2).' GB';
    }

    private function formatCpuUsage($cpu): string
    {
        return round($cpu * 100, 2).'%';
    }

    private function secondsToHumanReadable($seconds): string|bool
    {
        try {
            return CarbonInterval::seconds($seconds)->cascade()->forHumans();
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
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

    public function activate(int $id): RedirectResponse
    {
        $user = User::query()->findOrFail($id);

        $user->update([
            'is_active' => true,
        ]);

        $user->notify(new UserActivated());

        return redirect()->back();
    }

    public function deactivate(int $id): RedirectResponse
    {
        $user = User::query()->findOrFail($id);

        $user->update([
            'is_active' => false,
        ]);

        return redirect()->back();
    }
}
