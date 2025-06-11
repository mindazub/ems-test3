<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminDebugbarMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Enable debugbar for admin users
            if (class_exists(\Barryvdh\Debugbar\Facade::class)) {
                \Barryvdh\Debugbar\Facade::enable();
            }
        } else {
            // Disable debugbar for non-admin users or guests
            if (class_exists(\Barryvdh\Debugbar\Facade::class)) {
                \Barryvdh\Debugbar\Facade::disable();
            }
        }

        return $next($request);
    }
}
