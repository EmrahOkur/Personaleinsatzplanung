<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (! Auth::user() || ! in_array(Auth::user()->role, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}
