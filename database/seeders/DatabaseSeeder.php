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
        $this->cleanDatabase();
        $this->createUsers();

        // 1️⃣ PREPARE DEVICE DATA (DO NOT CREATE YET)

        $parentDevicesData = [];
        for ($i = 1; $i <= 40; $i++) {
            $parentDevicesData[] = [
                'device_type' => 'Meter',
                'manufacturer' => 'Generic Inc.',
                'device_model' => 'ParentModel-' . $i,
                'device_status' => 'Ready',
                'parent_device' => true,
                'parameters' => [
                    'communication_type' => 'Modbus TCP/IP',
                    'ip' => '192.168.1.' . $i,
                    'port' => 502,
                ],
            ];
        }

        $slaveDevicesData = [];
        for ($i = 1; $i <= 300; $i++) {
            $slaveDevicesData[] = [
                'device_type' => 'Inverter',
                'manufacturer' => 'Huawei',
                'device_model' => 'SlaveModel-' . $i,
                'device_status' => 'Ready',
                'parent_device' => false,
                'parameters' => [
                    'slave_id' => $i,
                ],
            ];
        }

        // Shuffle the arrays so selection is random
        shuffle($parentDevicesData);
        shuffle($slaveDevicesData);

        $slaveIndex = 0;
        $parentIndex = 0;

        // 2️⃣ CREATE PLANTS, MAINFEEDS, AND ASSIGN DEVICES

        $faker = Faker::create();

        for ($plantNum = 1; $plantNum <= 50; $plantNum++) {

            $plant = Plant::create([
                'name' => 'Plant ' . $faker->city . ' ' . ucfirst(collect(explode(' ', $faker->catchPhrase))->take(2)->implode(' ')),
                'owner_email' => strtolower($faker->firstName . '.' . $faker->lastName) . '@example.com',
                'status' => 'Working',
                'capacity' => rand(100000, 500000),
                'latitude' => round(50 + mt_rand(0, 1000000) / 100000, 5),
                'longitude' => round(20 + mt_rand(0, 1000000) / 100000, 5),
                'last_updated' => now()->timestamp,
                'uuid' => (string) Str::uuid(),
            ]);

            // Each plant gets 1 to 4 feeds
            $feedCount = rand(1, 4);

            for ($feedNum = 1; $feedNum <= $feedCount; $feedNum++) {

                // Stop if we run out of parent devices
                if ($parentIndex >= count($parentDevicesData)) {
                    break 2; // exit both loops if no more parents
                }

                $mainFeed = $plant->mainFeeds()->create([
                    'import_power' => rand(50000, 150000),
                    'export_power' => rand(20000, 100000),
                    'uuid' => (string) Str::uuid(),
                ]);

                // --- Create parent device assigned to this feed
                $parentData = $parentDevicesData[$parentIndex];
                $parentData['uuid'] = (string) Str::uuid();
                $parentDevice = $mainFeed->devices()->create($parentData);

                $parentIndex++;

                // Assign 2 to 5 slave devices
                $slaveCount = rand(2, 5);

                for ($i = 1; $i <= $slaveCount; $i++) {

                    // Stop if we run out of slaves
                    if ($slaveIndex >= count($slaveDevicesData)) {
                        break;
                    }

                    $slaveData = $slaveDevicesData[$slaveIndex];
                    // Add relation fields
                    $slaveData['main_feed_id'] = $mainFeed->id;
                    $slaveData['parent_device_id'] = $parentDevice->id;
                    $slaveData['uuid'] = (string) Str::uuid();

                    // Create slave device
                    Device::create($slaveData);

                    $slaveIndex++;
                }
            }
        }

        $this->command->info('✅ Plants, feeds, and pre-prepared devices assigned successfully!');
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
            'uuid' => (string) Str::uuid(),
        ]);

        User::create([
            'name' => 'Manager',
            'email' => 'manager@demo.com',
            'password' => bcrypt('manager000'),
            'role' => 'manager',
            'uuid' => (string) Str::uuid(),
        ]);

        User::create([
            'name' => 'Installer',
            'email' => 'installer@demo.com',
            'password' => bcrypt('installer000'),
            'role' => 'installer',
            'uuid' => (string) Str::uuid(),
        ]);

        User::create([
            'name' => 'Customer',
            'email' => 'customer@demo.com',
            'password' => bcrypt('customer000'),
            'role' => 'customer',
            'uuid' => (string) Str::uuid(),
        ]);
    }
}
