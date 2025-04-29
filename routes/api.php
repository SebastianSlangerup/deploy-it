<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\InstanceController;
use Illuminate\Support\Facades\Route;

Route::post('register', [ApiController::class, 'register']);
Route::post('login', [ApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Configuration
    Route::get('configurations', [ApiController::class, 'getConfigurations']);

    // Status
    Route::get('status/{instance:id}', [ApiController::class, 'getStatus']);

    // Instance
    Route::get('instances', [ApiController::class, 'getInstances']);
    Route::get('instances/{instance:id}', [ApiController::class, 'getInstance']);
    Route::post('instances/create', [InstanceController::class, 'store']);
    Route::patch('instances/{instance:id}/update', [InstanceController::class, 'update']);
    Route::delete('instances/{instance:id}', [InstanceController::class, 'destroy']);

    // User
    Route::get('user', [ApiController::class, 'getUser']);
    Route::get('user/paid-status', [ApiController::class, 'getPaidStatus']);
    Route::patch('user/update', [ApiController::class, 'updateUser']);
});
