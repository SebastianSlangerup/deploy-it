<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use App\Notifications\UserSendOpenVpnConf;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function download(Request $request)
    {
        $path = $request->query('path');

        return Storage::download($path);
    }

    public function userSendOpenVpnConf(Request $request)
    {
        $filePath = $this->getOpenVpnConfig($request);
        if ($filePath) {
            $request->user()->notify(new UserSendOpenVpnConf($filePath));
            return redirect()->route('dashboard')->with(['message' => "Configuration file sent successfully."]);
        } else {
            return redirect()->route('dashboard')->with(['message' => "Failed to generate and send the configuration file."]);
        }
    }

    private function getOpenVpnConfig(Request $request)
    {
        try {
            $username = $request->user()->name;
            $url = "http://192.168.1.20:8000/openvpn/generate_config/";
    
            $response = Http::timeout(10)
                ->withHeaders([
                    'accept' => 'application/json',
                ])
                ->post("{$url}?username={$username}", []);
        
            if ($response->ok() && $response->header('Content-Type') != 'application/json') {
                $responseData = $response->body();
                $header = $response->getHeader('Content-Disposition');
                $filename = Str::after($header[0],'filename="');
                $filename = rtrim($filename,'"');

                $path = "openvpn_configs/".$filename;

                Storage::put($path, $response->body());

                return $path;
            }
    
            return null; 
        } catch (ConnectionException $e) {
            return null;
        }
    }
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
