<?php

use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\InstanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [InstanceController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('tokens/create', function (Request $request) {
        $token = $request->user()?->createToken($request->token_name);

        return ['token' => $token->plainTextToken];
    });
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('containers', [InstanceController::class, 'containers'])->name('containers.index');
    Route::get('servers', [InstanceController::class, 'servers'])->name('servers.index');

    Route::get('instances/{instance:id}', [InstanceController::class, 'show'])->name('instances.show');
    Route::get('instances/create/{instance_type}', [InstanceController::class, 'create'])->name('instances.create');
    Route::post('instances/store', [InstanceController::class, 'store'])->name('instances.store');
    Route::delete('instances/{instance:id}', [InstanceController::class, 'destroy'])->name('instances.destroy');

    Route::get('configurations', [ConfigurationController::class, 'get'])->name('configurations.show');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
