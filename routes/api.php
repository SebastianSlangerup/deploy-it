<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\InstanceController;
use Illuminate\Support\Facades\Route;

Route::post('register', [ApiController::class, 'register']);
Route::post('login', [ApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('instances', [ApiController::class, 'instances']);
    Route::post('instances/create', [InstanceController::class, 'store']);
    Route::delete('instances/{instance}', [InstanceController::class, 'destroy']);
});
