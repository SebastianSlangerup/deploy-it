<?php

namespace App\Services;

use App\Models\Token;
use Illuminate\Support\Facades\Http;

class TokenService
{
    /**
     * Get the current JSON Web Token
     */
    public static function get(): string
    {
        if (Token::exists()) {
            return Token::query()->first()->value;
        } else {
            return self::new();
        }
    }

    /**
     * Generate a new JSON Web Token
     */
    public static function new(): string
    {
        // Fetch a new token from the API
        $value = Http::post(config('app.api.endpoint').'/auth/login', [
            'hostname' => config('app.api.jwt.hostname'),
            'username' => config('app.api.jwt.username'),
            'password' => config('app.api.jwt.password'),
        ])->json('access_token');

        // Update the token stored in the database
        if (Token::query()->exists()) {
            $token = Token::query()->first();

            $token->update([
                'value' => $value,
            ]);

            return $token;
        }

        return Token::query()->create([
            'value' => $value,
        ])->value;
    }
}
