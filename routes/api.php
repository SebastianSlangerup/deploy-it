<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\InstanceController;
use Illuminate\Support\Facades\Route;

Route::post('register', [ApiController::class, 'register']);
Route::post('login', [ApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('configurations', [ApiController::class, 'configurations']);
    Route::get('instances', [ApiController::class, 'instances']);
    Route::get('status/{instance:id}', [ApiController::class, 'status']);
    Route::post('instances/create', [InstanceController::class, 'store']);
    Route::delete('instances/{instance:id}', [InstanceController::class, 'destroy']);
    Route::patch('user/update', [ApiController::class, 'update']);
});
