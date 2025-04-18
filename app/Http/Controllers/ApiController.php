<?php

/** @noinspection PhpClassConstantAccessedViaChildClassInspection */

namespace App\Http\Controllers;

use App\Data\ConfigurationData;
use App\Data\InstanceData;
use App\Data\UserData;
use App\Enums\RolesEnum;
use App\Models\Configuration;
use App\Models\Instance;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;
use Illuminate\Validation\Rules;

class ApiController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => RolesEnum::User,
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('access-token', ['*'], now()->addWeek());

        return new JsonResponse(
            data: [
                'message' => 'User created successfully',
                'data' => UserData::from($user)->toArray(),
                'token' => $token->plainTextToken,
            ],
            status: JsonResponse::HTTP_CREATED,
        );
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required'],
        ]);

        $user = User::query()->where('email', '=', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
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

    }

    public function instances(Request $request): JsonResponse
    {
        $user = $request->user();

        $instances = Instance::query()
            ->where('created_by', '=', $user->id)
            ->get();

        return new JsonResponse(
            data: InstanceData::collect($instances)->toArray(),
            status: JsonResponse::HTTP_OK,
        );
    }

    public function configurations(): JsonResponse
    {
        $configurations = Configuration::all();

        return new JsonResponse(
            data: ConfigurationData::collect($configurations)->toArray(),
            status: JsonResponse::HTTP_OK,
        );
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $request->user();

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return new JsonResponse(
            data: [
                'message' => 'User updated successfully',
            ],
            status: JsonResponse::HTTP_OK,
        );
    }

    public function status(Instance $instance): JsonResponse
    {
        if (empty($instance->vm_id)) {
            return new JsonResponse(
                data: [
                    'message' => 'Instance does not have a vm_id',
                ],
                status: JsonResponse::HTTP_BAD_REQUEST,
            );
        }

        try {
            $response = Http::proxmox()
                ->withQueryParameters(['vmid' => $instance->vm_id])
                ->get('/get_vm_status');
        } catch (ConnectionException $exception) {
            Log::error('{controller}: Connection failed. Error message: {message}', [
                'controller' => self::class,
                'message' => $exception->getMessage(),
            ]);

            return new JsonResponse(
                data: [
                    'message' => 'Could not connect to proxmox api. Error message: '.$exception->getMessage(),
                ],
                status: JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if (! $response->successful()) {
            Log::warning('{controller}: Response unsuccessful. Message: {message}', [
                'controller' => self::class,
                'message' => $response->body(),
            ]);

            return new JsonResponse(
                data: [
                    'message' => 'Could not connect to proxmox api. Error message: '.$response->body(),
                ],
                status: JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        $data = $response->json();

        $cpuUsage = Number::percentage($data['cpu']);
        $totalMemory = Number::fileSize($data['maxmem']);
        $usedMemory = Number::fileSize($data['mem']);
        $totalStorage = Number::fileSize($data['maxdisk']);

        return new JsonResponse(
            data: [
                'message' => 'Status fetched successfully',
                'cpuUsage' => $cpuUsage,
                'totalMemory' => $totalMemory,
                'usedMemory' => $usedMemory,
                'totalStorage' => $totalStorage,
            ],
            status: JsonResponse::HTTP_OK,
        );
    }
}
