<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\User;
use App\Notifications\UserActivated;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class AdminController extends Controller
{

    public function index()
    {
        $non_activated_users = User::where('is_active', 0)->get();
        $activated_users = User::where('is_active', 1)->get(); // This should be 1 for activated users

        $network_info = $this->GetNetwork()->json();
        $node_info = $this->listNodes()->json();
    
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
            return $res;

        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with(['message' => "Connection Failed..."]);
        }

    }
    
    public function listNodes()
    {
        try {
            $res = Http::timeout(3)->get(config('app.api.endpoint')."/list_nodes");
            // dd($res->json());
            return $res;

        } catch (ConnectionException) {
            return redirect()->route('dashboard')->with(['message' => "Connection Failed..."]);
        }

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


