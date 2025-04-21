<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserIsSubscribedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()?->subscribed()) {
            return redirect(route('checkout'));
        }

        return $next($request);
    }
}
