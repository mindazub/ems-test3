<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Plant;
use App\Models\MainFeed;
use App\Models\Device;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;



class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->createUsers();
    }

    private function createUsers(): void
    {
        // Clear existing users first
        User::truncate();
        
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin000'),
            'role' => 'admin',
            'uuid' => Str::uuid()->toString(),
        ]);

        User::create([
            'name' => 'Manager',
            'email' => 'manager@demo.com',
            'password' => bcrypt('manager000'),
            'role' => 'manager',
            'uuid' => Str::uuid()->toString(),
        ]);

        User::create([
            'name' => 'Installer',
            'email' => 'installer@demo.com',
            'password' => bcrypt('installer000'),
            'role' => 'installer',
            'uuid' => Str::uuid()->toString(),
        ]);

        User::create([
            'name' => 'Customer',
            'email' => 'customer@demo.com',
            'password' => bcrypt('customer000'),
            'role' => 'customer',
            'uuid' => Str::uuid()->toString(),
        ]);

        User::create([
            'name' => 'Mantas Zelba',
            'email' => 'mantas@viasolis.eu',
            'password' => bcrypt('mantas000'),
            'role' => 'customer',
            'uuid' => '6a36660d-daae-48dd-a4fe-000b191b13d8',
        ]);

        User::create([
            'name' => 'javainis',
            'email' => 'jonas.vaicys@edislab.lt',
            'password' => bcrypt('Krepsinis1230'),
            'role' => 'customer',
            'uuid' => Str::uuid()->toString(),
        ]);
    }
}
