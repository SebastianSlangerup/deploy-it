<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\UserIsActivated;
use App\Http\Middleware\UserIsAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', [EnvironmentController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/awaiting-approval', function () {
    return Inertia::render('Guest');
})->middleware(['auth'])->name('awaiting_approval');

Route::middleware(['auth', 'verified', UserIsActivated::class])->group(function () {
    Route::get('/dashboard', [EnvironmentController::class, 'index'])->name('dashboard');

    Route::get('/userpath', function () {
        return response()->json([Auth::user()->public_key]);
    });

    Route::get('/environment/new', function () {
        return Inertia::render('Environment/Create');
    })->name('environment.new');
    Route::post('/environment/create', [EnvironmentController::class, 'create'])->name('environment.create');
    Route::get('/environment/control/{environment}/{option}', [EnvironmentController::class, 'control'])->name('environment.control');
    Route::get('/environment/delete/{environment}', [EnvironmentController::class, 'delete'])->name('environment.delete');
    Route::get('/environment/show/{environment}', [EnvironmentController::class, 'show'])->name('environment.show');

    Route::get('/dependencies/template/{templateId}', [EnvironmentController::class, 'getDependencies'])->name('dependencies.get');

    Route::middleware([UserIsAdmin::class])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('Admin');
        Route::get('/admin/users/detail/{user}', [AdminController::class, 'edit'])->name('Admin.users.detail');
        Route::get('/admin/users/activate/{id}', [AdminController::class, 'activate'])->name('admin.users.activate');
        Route::get('/admin/users/deactivate/{id}', [AdminController::class, 'deactivate'])->name('admin.users.deactivate');
        Route::get('/admin/server/network/', [AdminController::class, 'deactivate'])->name('admin.users.deactivate');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/public-key', [ProfileController::class, 'download'])->name('profile.public_key');
    Route::post('/openvpn/generate_config', [ProfileController::class, 'userSendOpenVpnConf'])
        ->middleware('throttle:6,1')
        ->name('openVpnConfig.send');
});

require __DIR__.'/auth.php';
