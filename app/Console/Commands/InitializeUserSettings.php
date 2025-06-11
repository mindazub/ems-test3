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
    protected $signature = 'users:init-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize settings for existing users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all users and check their settings
        $users = User::all();
        $updated = 0;
        
        $this->info("Checking {$users->count()} users for settings initialization...");
        
        foreach ($users as $user) {
            $settings = $user->settings ?? [];
            
            // Check if time_format is missing or null
            if (!isset($settings['time_format'])) {
                $settings['time_format'] = '24';
                $user->settings = $settings;
                $user->save();
                $updated++;
                $this->info("Initialized time_format for user: {$user->name} (ID: {$user->id})");
            } else {
                $this->line("User {$user->name} already has time_format: {$settings['time_format']}");
            }
        }
        
        $this->info("Settings initialization complete! Updated {$updated} users.");
    }
}
