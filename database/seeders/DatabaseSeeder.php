<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin000'),
            'role' => 'admin',
        ]);

        // Test User
        User::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => bcrypt('test000'),
            'role' => 'customer',
        ]);
    }
}
