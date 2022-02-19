<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTokenIsValid
{
    /**
     * Checks equals bearerToken and AUTH_TOKEN from .env
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed|void
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken() === env("AUTH_TOKEN")) {
            return $next($request);
        }

        abort(403, "Invalid token");
    }
}
