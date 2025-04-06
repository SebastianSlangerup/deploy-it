<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());

        Gate::define('interact-with-servers', function (User $user) {
            return $user->role === 'admin';
        });

        Http::macro('proxmox', function () {
            $token = Cache::get('token');
            $tokenExpiration = Cache::get('token_expiration');

            if ($token && $tokenExpiration && $tokenExpiration > now()) {
                return Http::withToken(Cache::get('token'));
            }

            $response = Http::withHeaders([
                'accept' => 'application/json',
            ])->post(config('services.proxmox.endpoint'));

            if ($response->successful()) {
                $token = $response->json()['token'];
                $tokenExpiration = $response->json()['expires_at'];
                // TODO: Figure out TTL based on the tokenExpiration

                // TODO: Cache::put('token', $token, $ttl);
            }

            return Http::withToken($token);
        });
    }
}
