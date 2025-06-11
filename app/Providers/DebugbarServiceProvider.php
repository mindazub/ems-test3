<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class DebugbarServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only enable debugbar for admin users
        if ($this->app->environment('production')) {
            // In production, only show for authenticated admin users
            $this->app->booted(function () {
                if (Auth::check() && Auth::user()->role !== 'admin') {
                    // Disable debugbar for non-admin users
                    if (class_exists(\Barryvdh\Debugbar\Facade::class)) {
                        \Barryvdh\Debugbar\Facade::disable();
                    }
                } elseif (!Auth::check()) {
                    // Disable debugbar for non-authenticated users
                    if (class_exists(\Barryvdh\Debugbar\Facade::class)) {
                        \Barryvdh\Debugbar\Facade::disable();
                    }
                }
            });
        } else {
            // In development, show for all admin users
            $this->app->booted(function () {
                if (Auth::check() && Auth::user()->role !== 'admin') {
                    if (class_exists(\Barryvdh\Debugbar\Facade::class)) {
                        \Barryvdh\Debugbar\Facade::disable();
                    }
                } elseif (!Auth::check()) {
                    if (class_exists(\Barryvdh\Debugbar\Facade::class)) {
                        \Barryvdh\Debugbar\Facade::disable();
                    }
                }
            });
        }
    }
}
