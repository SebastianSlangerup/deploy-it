<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\User;


class AdminController extends Controller
{

    public function index()
    {
        $users = User::where('is_active', 0)->get();

        return Inertia::render('Admin/index', [
            'users' => $users,
        ]);
    }
}


