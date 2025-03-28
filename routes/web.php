<?php

use App\Http\Controllers\InstanceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [InstanceController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('containers', [InstanceController::class, 'containers'])->name('containers.index');
    Route::get('servers', [InstanceController::class, 'servers'])->name('servers.index');

    Route::get('instances/{instance:id}', [InstanceController::class, 'view'])->name('instances.detail');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
