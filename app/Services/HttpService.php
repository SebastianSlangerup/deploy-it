<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class HttpService extends Http
{
    /**
     * Prepare a request with pre-defined parameters
     */
    public static function prepareRequest(): PendingRequest
    {
        return parent::timeout(15)
            ->withToken(TokenService::get())
            // Retry callback in case the request fails
            ->retry(2, 0, function (Exception $exception, PendingRequest $request) {
                // If we are not getting a Request Exception, or a 401 status code, dont bother retrying the request
                if (! $exception instanceof RequestException || $exception->response->status() !== 401) {
                    return false;
                }

                $request->withToken(TokenService::new());

                return true;
            });
    }
}
