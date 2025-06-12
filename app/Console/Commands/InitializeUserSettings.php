<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class InitializeUserSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:init-settings {--force : Force update time offset to 6 hours for all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize settings and time offset for existing users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all users and check their settings
        $users = User::all();
        $settingsUpdated = 0;
        $offsetUpdated = 0;
        
        $this->info("Checking {$users->count()} users for settings and time offset initialization...");
        
        foreach ($users as $user) {
            $userUpdated = false;
            
            // Initialize time_format setting
            $settings = $user->settings ?? [];
            
            if (!isset($settings['time_format'])) {
                $settings['time_format'] = '24';
                $user->settings = $settings;
                $userUpdated = true;
                $settingsUpdated++;
                $this->info("Initialized time_format for user: {$user->name} (ID: {$user->id})");
            } else {
                $this->line("User {$user->name} already has time_format: {$settings['time_format']}");
            }
            
            // Initialize time_offset (6 hours default)
            if ($user->time_offset === null || $this->option('force')) {
                $oldOffset = $user->time_offset;
                $user->time_offset = 6;
                $userUpdated = true;
                $offsetUpdated++;
                if ($this->option('force')) {
                    $this->info("Force updated time_offset from {$oldOffset} to 6 hours for user: {$user->name} (ID: {$user->id})");
                } else {
                    $this->info("Initialized time_offset to 6 hours for user: {$user->name} (ID: {$user->id})");
                }
            } else {
                $this->line("User {$user->name} already has time_offset: {$user->time_offset} hours");
            }
            
            // Save user if any changes were made
            if ($userUpdated) {
                $user->save();
            }
        }
        
        $this->info("Settings initialization complete!");
        $this->info("- Time format updated: {$settingsUpdated} users");
        $this->info("- Time offset updated: {$offsetUpdated} users");
        $this->info("- Total users processed: {$users->count()}");
    }
}
