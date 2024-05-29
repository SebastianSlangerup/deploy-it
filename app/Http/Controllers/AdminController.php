<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\User;
use App\Notifications\UserActivated;

class AdminController extends Controller
{

    public function index()
    {
        $users = User::where('is_active', 0)->get();

        return Inertia::render('Admin/index', [
            'users' => $users,
        ]);
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


