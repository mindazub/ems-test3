<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\MainFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DeviceController extends Controller
{
    public function index()
    {
        // Fetch plant_view JSON (simulate API call for now)
        $jsonPath = public_path('plant_view_65f20fa1-047a-4379-8464-59f1d94be3c7_1748955255.json');
        $data = [];
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);
        }
        $plantUid = $data['plant_metadata']['uid'] ?? null;
        $plantName = $plantUid;
        $devices = collect();
        // Helper to flatten device tree
        $flattenDevices = function($devicesArr, $plantUid, $plantName, $parentDevice = null, $controller = null, $feed = null) use (&$flattenDevices) {
            $flat = collect();
            foreach ($devicesArr as $device) {
                $d = [
                    'id' => $device['uid'] ?? null, // always use full UID for routing
                    'short_id' => isset($device['uid']) ? substr($device['uid'], 0, 8) : null, // trimmed for display
                    'device_type' => $device['device_type'] ?? null,
                    'manufacturer' => $device['device_manufacturer'] ?? null,
                    'device_model' => $device['device_model'] ?? null,
                    'device_status' => $device['device_status'] ?? null,
                    'plant_uid' => $plantUid ? substr($plantUid, 0, 8) : null,
                    'plant_name' => $plantName,
                    'controller_uid' => isset($controller['uid']) ? substr($controller['uid'], 0, 8) : null,
                    'feed_uid' => isset($feed['uid']) ? substr($feed['uid'], 0, 8) : null,
                    'parent_device_id' => isset($parentDevice['uid']) ? $parentDevice['uid'] : null, // full UID for parent
                    'parent_device_short_id' => isset($parentDevice['uid']) ? substr($parentDevice['uid'], 0, 8) : null,
                ];
                $flat->push($d);
                if (!empty($device['assigned_devices'])) {
                    $flat = $flat->merge($flattenDevices($device['assigned_devices'], $plantUid, $plantName, $device, $controller, $feed));
                }
            }
            return $flat;
        };
        // Walk controllers/main feeds/devices
        if (!empty($data['controllers'])) {
            foreach ($data['controllers'] as $controller) {
                foreach ($controller['controller_main_feeds'] ?? [] as $feed) {
                    $devicesArr = $feed['main_feed_devices'] ?? [];
                    $devices = $devices->merge($flattenDevices($devicesArr, $plantUid, $plantName, null, $controller, $feed));
                }
            }
        }
        return view('devices.index', [ 'devices' => $devices ]);
    }

    public function show($id)
    {
        // Fetch plant_view JSON (simulate API call for now)
        $jsonPath = public_path('plant_view_65f20fa1-047a-4379-8464-59f1d94be3c7_1748955255.json');
        $data = [];
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);
        }
        $deviceInfo = null;
        $plantUid = $data['plant_metadata']['uid'] ?? null;
        $plantName = $plantUid;
        // Flatten all devices
        $flattenDevices = function($devicesArr, $plantUid, $plantName, $parentDevice = null, $controller = null, $feed = null) use (&$flattenDevices) {
            $flat = collect();
            foreach ($devicesArr as $device) {
                // Add plant_full_uid and plant_short_uid for plant links
                $d = [
                    'id' => $device['uid'] ?? null, // always use full UID for routing
                    'short_id' => isset($device['uid']) ? substr($device['uid'], 0, 8) : null, // trimmed for display
                    'full_uid' => $device['uid'] ?? null,
                    'device_type' => $device['device_type'] ?? null,
                    'manufacturer' => $device['device_manufacturer'] ?? null,
                    'device_model' => $device['device_model'] ?? null,
                    'device_status' => $device['device_status'] ?? null,
                    'plant_uid' => $plantUid ? substr($plantUid, 0, 8) : null,
                    'plant_full_uid' => $plantUid ?? null,
                    'plant_short_uid' => $plantUid ? substr($plantUid, 0, 8) : null,
                    'plant_name' => $plantName,
                    'controller_uid' => isset($controller['uid']) ? substr($controller['uid'], 0, 8) : null,
                    'controller_full_uid' => $controller['uid'] ?? null,
                    'feed_uid' => isset($feed['uid']) ? substr($feed['uid'], 0, 8) : null,
                    'feed_full_uid' => $feed['uid'] ?? null,
                    'parent_device_id' => isset($parentDevice['uid']) ? $parentDevice['uid'] : null, // full UID for parent
                    'parent_device_short_id' => isset($parentDevice['uid']) ? substr($parentDevice['uid'], 0, 8) : null,
                    'raw' => $device,
                ];
                $flat->push($d);
                if (!empty($device['assigned_devices'])) {
                    $flat = $flat->merge($flattenDevices($device['assigned_devices'], $plantUid, $plantName, $device, $controller, $feed));
                }
            }
            return $flat;
        };
        $allDevices = collect();
        if (!empty($data['controllers'])) {
            foreach ($data['controllers'] as $controller) {
                foreach ($controller['controller_main_feeds'] ?? [] as $feed) {
                    $devicesArr = $feed['main_feed_devices'] ?? [];
                    $allDevices = $allDevices->merge($flattenDevices($devicesArr, $plantUid, $plantName, null, $controller, $feed));
                }
            }
        }
        // Only match by full_uid
        $deviceInfo = $allDevices->first(function($d) use ($id) {
            return $d['full_uid'] === $id;
        });
        if (!$deviceInfo) {
            abort(404, 'Device not found');
        }
        return view('devices.show', ['device' => $deviceInfo]);
    }

    public function create()
    {
        return view('devices.create', [
            'mainFeeds' => MainFeed::all(),
            'parentDevices' => Device::where('parent_device', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'main_feed_id' => 'required|exists:main_feeds,id',
            'parent_device_id' => 'nullable|exists:devices,id',
            'device_type' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'device_model' => 'required|string|max:255',
            'device_status' => 'required|string|max:255',
            'parent_device' => 'nullable|boolean',
            'parameters' => 'nullable|json',
        ]);

        // Convert parameters string to array if filled
        if (!empty($validated['parameters'])) {
            $validated['parameters'] = json_decode($validated['parameters'], true);
        }

        Device::create($validated);

        Cache::forget('devices.with.mainFeed.parent');

        return redirect()->route('devices.index')->with('message', 'Device created successfully.');
    }



    public function edit(Device $device)
    {
        // All MainFeeds for the dropdown
        $mainFeeds = \App\Models\MainFeed::with('plant')->get();

        // All possible parent devices (excluding this device itself to prevent circular parenting)
        $parentDevices = \App\Models\Device::where('parent_device', true)
                            ->where('id', '!=', $device->id)
                            ->get();

        return view('devices.edit', compact('device', 'mainFeeds', 'parentDevices'));
    }


    public function update(Request $request, Device $device)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'main_feed_id' => 'required|exists:main_feeds,id',
            'parent_id' => 'nullable|exists:devices,id',
        ]);

        $device->update($validatedData);

        Cache::forget('devices.with.mainFeed.parent');

        return redirect()->route('devices.index')->with('message', 'Device updated successfully.');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        Cache::forget('devices.with.mainFeed.parent');

        return redirect()->route('devices.index')->with('message', 'Device deleted successfully.');
    }
}
