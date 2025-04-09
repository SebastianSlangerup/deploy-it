<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserIsAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->role !== 'admin') {
            return redirect()->to('dashboard');
        }

        return $next($request);
    }
}
