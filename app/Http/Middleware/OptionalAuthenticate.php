<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        // This allows Auth::user() to work if a valid token is provided
        Log::info('🔐 OptionalAuthenticate - Authorization Header: ' . ($request->hasHeader('Authorization') ? 'YES' : 'NO'));
        
        if ($request->hasHeader('Authorization')) {
            try {
                // Try to get user from API token
                $user = Auth::guard('api')->user();
                if ($user) {
                    Log::info('🔐 OptionalAuthenticate - User authenticated: ID=' . $user->id . ', Name=' . $user->name);
                } else {
                    Log::info('🔐 OptionalAuthenticate - Token provided but no user found');
                }
            } catch (\Exception $e) {
                Log::warning('🔐 OptionalAuthenticate - Error during auth: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
