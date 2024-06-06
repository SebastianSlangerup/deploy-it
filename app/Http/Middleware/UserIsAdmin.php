<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
