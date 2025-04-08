<?php

namespace App\Providers;

use App\Data\NotificationData;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
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
            return Http::baseUrl(config('services.proxmox.endpoint'));
        });

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
