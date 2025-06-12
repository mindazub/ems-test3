<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\MainFeed;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = collect();
        
        // Get all plants to search for devices across all of them
        $allPlants = $this->getAllPlants();
        
        // Helper to flatten device tree from API response
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
        
        // Search for devices in each plant
        foreach ($allPlants as $plantId) {
            $plantData = $this->getPlantData($plantId);
            if ($plantData) {
                $plantUid = $plantData['plant_metadata']['uid'] ?? null;
                $plantName = $plantUid; // Using UID as name for now
                
                // Walk controllers/main feeds/devices
                if (!empty($plantData['controllers'])) {
                    foreach ($plantData['controllers'] as $controller) {
                        foreach ($controller['controller_main_feeds'] ?? [] as $feed) {
                            $devicesArr = $feed['main_feed_devices'] ?? [];
                            $devices = $devices->merge($flattenDevices($devicesArr, $plantUid, $plantName, null, $controller, $feed));
                        }
                    }
                }
            }
        }
        
        return view('devices.index', ['devices' => $devices]);
    }

    public function show($id)
    {
        $deviceInfo = null;
        
        // Get all plants to search for the device
        $allPlants = $this->getAllPlants();
        
        // Helper to flatten device tree and find specific device
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

        // Search through all plants to find the device
        foreach ($allPlants as $plantId) {
            $plantData = $this->getPlantData($plantId);
            if ($plantData) {
                $plantUid = $plantData['plant_metadata']['uid'] ?? null;
                $plantName = $plantUid; // Using UID as name for now
                
                $allDevices = collect();
                if (!empty($plantData['controllers'])) {
                    foreach ($plantData['controllers'] as $controller) {
                        foreach ($controller['controller_main_feeds'] ?? [] as $feed) {
                            $devicesArr = $feed['main_feed_devices'] ?? [];
                            $allDevices = $allDevices->merge($flattenDevices($devicesArr, $plantUid, $plantName, null, $controller, $feed));
                        }
                    }
                }
                
                // Check if the device exists in this plant
                $deviceInfo = $allDevices->first(function($d) use ($id) {
                    return $d['full_uid'] === $id;
                });
                
                if ($deviceInfo) {
                    break; // Found the device, stop searching
                }
            }
        }
        
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

        return redirect()->route('devices.index')->with('message', 'Device updated successfully.');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index')->with('message', 'Device deleted successfully.');
    }
    
    /**
     * Show devices organized by main feed in a collapsible structure
     */
    public function devicesByFeed()
    {
        $controllerData = collect();
        
        // Get all plants to organize devices by controller and feed
        $allPlants = $this->getAllPlants();
        
        foreach ($allPlants as $plantId) {
            $plantData = $this->getPlantData($plantId);
            if ($plantData && !empty($plantData['controllers'])) {
                $plantUid = $plantData['plant_metadata']['uid'] ?? null;
                $plantName = $plantUid;
                
                foreach ($plantData['controllers'] as $controller) {
                    $controllerUid = $controller['uid'] ?? 'Unknown';
                    $controllerShortId = $controllerUid !== 'Unknown' ? substr($controllerUid, 0, 8) : 'Unknown';
                    
                    // Organize feeds for this controller
                    $feeds = collect();
                    
                    foreach ($controller['controller_main_feeds'] ?? [] as $feed) {
                        $feedUid = $feed['uid'] ?? null;
                        $feedId = $feedUid ? substr($feedUid, 0, 8) : 'Unknown';
                        
                        // Organize parent devices and their slaves
                        $parentDevices = collect();
                        $devicesData = $feed['main_feed_devices'] ?? [];
                        
                        foreach ($devicesData as $device) {
                            // Check device parameters to determine if it's a true parent
                            $hasAssignedDevices = count($device['assigned_devices'] ?? []) > 0;
                            $parameters = (array)($device['parameters'] ?? []);
                            $hasSlaveId = array_key_exists('slave_id', $parameters);
                            $isTrueParent = $hasAssignedDevices && !$hasSlaveId;
                            
                            // Process parent device
                            $parentDevice = [
                                'id' => $device['uid'] ?? null,
                                'short_id' => isset($device['uid']) ? substr($device['uid'], 0, 8) : null,
                                'type' => $device['device_type'] ?? 'Unknown',
                                'manufacturer' => $device['device_manufacturer'] ?? 'Unknown',
                                'model' => $device['device_model'] ?? 'Unknown',
                                'status' => $device['device_status'] ?? 'Unknown',
                                'assigned_to' => '—',
                                'is_parent' => $isTrueParent ? 'Yes' : 'No',
                                'has_slaves' => $isTrueParent,
                                'slaves' => collect()
                            ];
                            
                            // Process slave devices only if this is a true parent
                            if ($isTrueParent) {
                                foreach ($device['assigned_devices'] ?? [] as $assignedDevice) {
                                    $slaveDevice = [
                                        'id' => $assignedDevice['uid'] ?? null,
                                        'short_id' => isset($assignedDevice['uid']) ? substr($assignedDevice['uid'], 0, 8) : null,
                                        'type' => $assignedDevice['device_type'] ?? 'Unknown',
                                        'manufacturer' => $assignedDevice['device_manufacturer'] ?? 'Unknown',
                                        'model' => $assignedDevice['device_model'] ?? 'Unknown',
                                        'status' => $assignedDevice['device_status'] ?? 'Unknown',
                                        'assigned_to' => '—',
                                        'is_parent' => 'No'
                                    ];
                                    $parentDevice['slaves']->push($slaveDevice);
                                }
                            }
                            
                            $parentDevices->push($parentDevice);
                        }
                        
                        $feedInfo = [
                            'feed_id' => $feedId,
                            'feed_uid' => $feedUid,
                            'parent_devices' => $parentDevices
                        ];
                        
                        $feeds->push($feedInfo);
                    }
                    
                    $controllerInfo = [
                        'controller_uid' => $controllerUid,
                        'controller_short_id' => $controllerShortId,
                        'plant_name' => $plantName,
                        'feeds' => $feeds,
                        'serial_no' => 'SRNo230-890a1047' // Mock serial number
                    ];
                    
                    $controllerData->push($controllerInfo);
                }
            }
        }
        
        return view('devices.by-feed', ['controllerData' => $controllerData]);
    }
    
    /**
     * Get list of all plant IDs from the plant list API
     */
    private function getAllPlants()
    {
        $plantListUuid = '6a36660d-daae-48dd-a4fe-000b191b13d8';
        $url = "http://127.0.0.1:5001/plant_list/{$plantListUuid}";
        $token = 'f9c2f80e1c0e5b6a3f7f40e6f2e9c9d0af7eaabc6b37a4d9728e26452b81fc13';
        
        $client = new \GuzzleHttp\Client();
        
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'timeout' => 5,
            ]);
            
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            $plantsArr = $data['plants'] ?? [];
            
            // Extract plant UIDs for API calls
            return collect($plantsArr)->pluck('uid')->filter()->toArray();
            
        } catch (\Exception $e) {
            \Log::error('Error fetching plant list from API', ['error' => $e->getMessage()]);
            return [];
        }
    }
    
    /**
     * Get plant data from the plant view API
     */
    private function getPlantData($plantId)
    {
        $url = "http://127.0.0.1:5001/plant_view/{$plantId}";
        $token = 'f9c2f80e1c0e5b6a3f7f40e6f2e9c9d0af7eaabc6b37a4d9728e26452b81fc13';
        
        $client = new \GuzzleHttp\Client();
        
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'timeout' => 5,
            ]);
            
            $body = $response->getBody()->getContents();
            return json_decode($body, true);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching plant data from API', [
                'plant_id' => $plantId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
