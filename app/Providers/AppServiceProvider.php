<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Ensure charts directory exists
        $this->app->singleton('charts.directory', function () {
            $chartsDirectory = public_path('charts');

            if (!File::exists($chartsDirectory)) {
                try {
                    File::makeDirectory($chartsDirectory, 0755, true);
                    Log::info("Charts directory created at: {$chartsDirectory}");
                } catch (\Exception $e) {
                    Log::error("Failed to create charts directory: " . $e->getMessage());
                }
            }

            return $chartsDirectory;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
