<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class EnsureChartsDirectoryExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $chartsDirectory = public_path('charts');

        if (!File::exists($chartsDirectory)) {
            File::makeDirectory($chartsDirectory, 0755, true);
        }

        return $next($request);
    }
}
