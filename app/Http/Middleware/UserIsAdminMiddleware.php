<?php

namespace App\Http\Middleware;

use App\Enums\RolesEnum;
use Closure;
use Illuminate\Http\Request;

class UserIsAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->role !== RolesEnum::Admin) {
            return redirect()->to('dashboard');
        }

        return $next($request);
    }
}
