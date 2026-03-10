<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionalAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Try to authenticate with API guard if Bearer token is provided
        // But don't fail if no token is provided
        if ($request->hasHeader('Authorization')) {
            try {
                Auth::guard('api')->authenticate();
            } catch (\Exception $e) {
                // Silently fail - continue as unauthenticated
            }
        }

        return $next($request);
    }
}
