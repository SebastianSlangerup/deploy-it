<?php

namespace App\Providers;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
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
        // Model::shouldBeStrict(! $this->app->isProduction());

        Gate::define('interact-with-servers', function (User $user) {
            return $user->role === RolesEnum::Admin;
        });

        Http::macro('proxmox', function () {
            return Http::baseUrl(config('services.proxmox.endpoint'))->withQueryParameters(['node' => 'node1']);
        });
    }
}
