<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Plant;
use App\Models\MainFeed;
use App\Models\Device;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === Seed Users ===
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

        // === Import JSON Plant Data ===
        $jsonPath = database_path('seeders/generated_plant_data.json');
        if (!File::exists($jsonPath)) {
            $this->command->error('JSON file not found: plant_view_data.json');
            return;
        }

        $json = File::get($jsonPath);
        $jsonData = json_decode($json, true);

        $plantCounter = 1;

        foreach ($jsonData as $entry) {
            if (!isset($entry['plant_id'])) continue;

            // Create Plant
            $plant = Plant::create([
                'name' => "Plant #$plantCounter",
                'owner_email' => $entry['plant_owner'],
                'status' => $entry['plant_status'],
                'capacity' => $entry['plant_capacity'],
                'latitude' => $entry['latitude'],
                'longitude' => $entry['longitude'],
                'last_updated' => $entry['last_updated'],
            ]);

            $plantCounter++;

            if (!isset($entry['main_feeds'])) continue;

            foreach ($entry['main_feeds'] as $feedData) {
                $mainFeed = $plant->mainFeeds()->create([
                    'import_power' => $feedData['import_power'],
                    'export_power' => $feedData['export_power'],
                ]);

                if (!isset($feedData['devices'])) continue;

                foreach ($feedData['devices'] as $deviceData) {
                    // Create parent device
                    $device = $mainFeed->devices()->create([
                        'device_type' => $deviceData['device_type'],
                        'manufacturer' => $deviceData['manufacturer'],
                        'device_model' => $deviceData['device_model'],
                        'device_status' => $deviceData['device_status'],
                        'parent_device' => true,
                        'parameters' => $deviceData['parameters'] ?? [],
                    ]);

                    // Handle assigned devices (children)
                    if (isset($deviceData['assigned_devices'])) {
                        foreach ($deviceData['assigned_devices'] as $assigned) {
                            $mainFeed->devices()->create([
                                'device_type' => $assigned['device_type'],
                                'manufacturer' => $assigned['manufacturer'],
                                'device_model' => $assigned['device_model'],
                                'device_status' => $assigned['device_status'],
                                'parent_device' => false,
                                'parent_device_id' => $device->id,
                                'parameters' => $assigned['parameters'] ?? [],
                            ]);
                        }
                    }
                }
            }
        }
    }
}
