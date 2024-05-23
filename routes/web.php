<?php

use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
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


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [EnvironmentController::class, 'index'])->name('dashboard');

    Route::get('/environment/new', function () {
        return Inertia::render('EnvironmentForm');
    })->name('environment.new');
    Route::get('/environment/control/{vmid}/{option}', [EnvironmentController::class, 'controlEnvironment'])->name('environment.control');
    Route::get('/environment/delete/{vmid}', [EnvironmentController::class, 'deleteEnvironment'])->name('environment.delete');
    Route::get('/environment/details/{id}', function (int $id) {
        $vm = Http::get('http://192.168.1.20/cnc/');
    })->name('environment.details');
});


Route::post('/create', function (Request $request) {
    return dd($request);
})->middleware(['auth', 'verified'])->name('create');

Route::get('/dependencies/template/{template}', function (string $template) {
    // TODO
})->middleware(['auth']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
