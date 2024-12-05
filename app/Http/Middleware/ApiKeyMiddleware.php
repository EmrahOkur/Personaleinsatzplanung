<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('app.apikey');

        $apiKeyIsValid = $request->header('x-api-key') === $apiKey;

        abort_if(! $apiKeyIsValid, 403, 'Access denied ');

        return $next($request);
    }
}
