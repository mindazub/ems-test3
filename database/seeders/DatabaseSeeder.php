<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Company;
use App\Models\Plant;
use App\Models\Device;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Users
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin000'),
            'role' => 'admin',
        ]);

        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@demo.com',
            'password' => bcrypt('manager000'),
            'role' => 'manager',
        ]);

        $installer = User::create([
            'name' => 'Installer',
            'email' => 'installer@demo.com',
            'password' => bcrypt('installer000'),
            'role' => 'installer',
        ]);

        $customer = User::create([
            'name' => 'Customer',
            'email' => 'customer@demo.com',
            'password' => bcrypt('customer000'),
            'role' => 'customer',
        ]);

        // Define labels
        $companyNames = ['Solar Energy', 'Wind Power', 'Hydro Dynamics'];
        $plantTypes = ['Solar', 'Wind', 'Thermal', 'Gas Turbine', 'Hydro'];
        $deviceTypes = ['Raspberry Pi', 'Arduino', 'MODBUS', 'iPhone', 'Android'];

        // Create 50 projects for Manager
        for ($i = 1; $i <= 50; $i++) {
            $project = Project::create([
                'user_id' => $manager->id,
                'name' => "Project #$i",
                'start_date' => now()->subDays(rand(100, 1000)),
            ]);

            foreach ($companyNames as $companyName) {
                $company = $project->companies()->create([
                    'name' => $companyName,
                ]);

                for ($p = 0; $p < 2; $p++) {
                    $plant = $company->plants()->create([
                        'name' => fake()->randomElement($plantTypes),
                    ]);

                    for ($d = 0; $d < 3; $d++) {
                        $plant->devices()->create([
                            'name' => fake()->randomElement($deviceTypes),
                        ]);
                    }
                }
            }
        }
    }
}
