<?php

namespace Database\Seeders;



use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Plant;
use App\Models\MainFeed;
use App\Models\Device;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;

use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // STEP 1: CLEAN DATABASE
        $this->cleanDatabase();

        // STEP 2: CREATE USERS
        $this->createUsers();

        // STEP 3: LOAD JSON DATA
        $jsonPath = database_path('seeders/generated_plant_data.json');
        if (!File::exists($jsonPath)) {
            $this->command->error("JSON file not found at: $jsonPath");
            return;
        }

        $json = File::get($jsonPath);
        $plantData = json_decode($json, true);

        // STEP 4: CREATE PLANTS, MAINFEEDS, DEVICES
        foreach ($plantData as $index => $entry) {

            // Create Plant
            $plant = Plant::create([
                'name' => 'Plant #' . ($index + 1),
                'owner_email' => $entry['plant_owner'],
                'status' => $entry['plant_status'],
                'capacity' => $entry['plant_capacity'],
                'latitude' => $entry['latitude'],
                'longitude' => $entry['longitude'],
                'last_updated' => $entry['last_updated'],
            ]);

            // For simplicity, we'll just create ONE main feed per plant here
            $mainFeed = $plant->mainFeeds()->create([
                'import_power' => Arr::get($entry, 'main_feeds.0.import_power', 100000),
                'export_power' => Arr::get($entry, 'main_feeds.0.export_power', 50000),
            ]);

            // Create Parent Device (main device for this feed)
            $parentDevice = $mainFeed->devices()->create([
                'device_type' => 'Meter',
                'manufacturer' => 'Generic Inc.',
                'device_model' => 'Model-XP',
                'device_status' => 'Working',
                'parent_device' => true,
                'parameters' => [
                    'communication_type' => 'Modbus TCP/IP',
                    'ip' => '192.168.0.' . rand(2, 254),
                    'port' => 502,
                ],
            ]);

            // Create random number (1 to 5) of slave devices
            $slaveCount = rand(1, 5);

            for ($i = 1; $i <= $slaveCount; $i++) {
                $mainFeed->devices()->create([
                    'device_type' => 'Inverter',
                    'manufacturer' => 'Huawei',
                    'device_model' => 'SUN2000-' . rand(20, 100) . 'KTL',
                    'device_status' => 'Working',
                    'parent_device' => false,
                    'parent_device_id' => $parentDevice->id,
                    'parameters' => [
                        'slave_id' => rand(10, 99),
                    ],
                ]);
            }
        }

        $this->command->info('✅ Database seeded successfully!');
    }

    private function cleanDatabase(): void
    {
        $connection = DB::getDriverName();

        if ($connection === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($connection === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        Device::truncate();
        MainFeed::truncate();
        Plant::truncate();
        User::truncate();

        if ($connection === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($connection === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }
    }


    private function createUsers(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin000'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Manager',
            'email' => 'manager@demo.com',
            'password' => bcrypt('manager000'),
            'role' => 'manager',
        ]);

        User::create([
            'name' => 'Installer',
            'email' => 'installer@demo.com',
            'password' => bcrypt('installer000'),
            'role' => 'installer',
        ]);

        User::create([
            'name' => 'Customer',
            'email' => 'customer@demo.com',
            'password' => bcrypt('customer000'),
            'role' => 'customer',
        ]);
    }
}
