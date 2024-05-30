<?php

namespace App\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HttpService extends Http
{
    public static string $token;

    public static function get(string $url, array|string|null $query = null): PromiseInterface|Response
    {
        $response = parent::get($url, $query);
        // If unauthorized, collect a token and retry the request
        if ($response->status() === 401) {
            self::$token = self::getToken();
            $response = parent::withToken(self::$token)->get($url, $query);
        }

        return $response;
    }

    public static function getToken(): string
    {
        // TODO: Put in proper endpoint
        return parent::get(config('app.api.endpoint'))->body();
    }
}
