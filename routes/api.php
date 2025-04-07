<?php

/** @noinspection PhpClassConstantAccessedViaChildClassInspection */

use App\Data\InstanceData;
use App\Enums\RolesEnum;
use App\Models\Instance;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

Route::post('register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
        'role' => ['required', Rule::enum(RolesEnum::class)],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::query()->create([
        'name' => $request->string('name'),
        'email' => $request->string('email'),
        'role' => RolesEnum::User,
        'password' => Hash::make($request->string('password')),
    ]);

    $token = $user->createToken('access-token', ['*'], now()->addWeek());

    return new JsonResponse(
        data: [
            'message' => 'User created successfully',
            'token' => $token->plainTextToken,
        ],
        status: JsonResponse::HTTP_OK
    );
});

Route::post('login', function (Request $request) {
    $request->validate([
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        'password' => ['required'],
    ]);

    $user = User::query()->where('email', '=', $request->string('email'))
        ->where('password', '=', Hash::make($request->string('password')))
        ->first();

    if (is_null($user)) {
        return new JsonResponse(
            data: [
                'message' => 'User not found',
                'description' => 'Double check the email and password, and make sure the user is created',
            ],
            status: JsonResponse::HTTP_UNAUTHORIZED,
        );
    }

    $token = $user->createToken('access-token', ['*'], now()->addWeek());

    return new JsonResponse(
        data: [
            'message' => 'User logged in',
            'token' => $token->plainTextToken,
        ],
        status: JsonResponse::HTTP_OK,
    );
});

Route::get('instances', function () {
    $instances = Instance::all();

    return new JsonResponse(
        data: InstanceData::collect($instances)->toArray(),
        status: JsonResponse::HTTP_OK,
    );
})->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
