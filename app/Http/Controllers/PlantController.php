<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    public function index()
    {
        // Use the correct plant list endpoint with the required UUID
        $plantListUuid = '6a36660d-daae-48dd-a4fe-000b191b13d8';
        $url = "http://127.0.0.1:5001/plant_list/{$plantListUuid}";
        $token = 'f9c2f80e1c0e5b6a3f7f40e6f2e9c9d0af7eaabc6b37a4d9728e26452b81fc13';
        $plants = collect();
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
            $plants = collect($plantsArr)->map(function ($plant) {
                $plantObj = new \stdClass();
                $plantObj->id = $plant['uid'] ?? null;
                $plantObj->name = $plant['uid'] ?? '';
                $user = \App\Models\User::where('uuid', $plant['owner'] ?? '')->first();
                $plantObj->owner_email = $user ? $user->email : ($plant['owner'] ?? '');
                $plantObj->status = $plant['status'] ?? '';
                $plantObj->last_updated = $plant['updated_at'] ?? null;
                $plantObj->device_amount = $plant['device_amount'] ?? null;
                $plantObj->controllers = collect();
                return $plantObj;
            });
        } catch (\Exception $e) {
            \Log::error('Error fetching plants from API', ['error' => $e->getMessage()]);
            $plants = collect();
        }
        return view('plants.index', compact('plants'));
    }

    public function show(Plant $plant)
    {
        $id = $plant->id;
        
        // Directly fetch the owner with the specific UUID
        $specificOwnerUuid = '6a36660d-daae-48dd-a4fe-000b191b13d8';
        $specificOwner = \App\Models\User::where('uuid', $specificOwnerUuid)->first();
        if ($specificOwner) {
            \Log::debug('Found specific owner in show method', [
                'uuid' => $specificOwner->uuid,
                'name' => $specificOwner->name,
                'email' => $specificOwner->email
            ]);
        } else {
            \Log::warning('Specific owner not found in show method', ['uuid' => $specificOwnerUuid]);
        }
        
        $client = new \GuzzleHttp\Client();
        $url = "http://127.0.0.1:5001/plant_view/{$id}";
        $token = 'f9c2f80e1c0e5b6a3f7f40e6f2e9c9d0af7eaabc6b37a4d9728e26452b81fc13';
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'timeout' => 5,
            ]);
            $plant = json_decode($body = $response->getBody()->getContents(), false);
            
            // Add the specific owner info if plant was successfully loaded
            if ($plant && $specificOwner) {
                $plant->owner_name = $specificOwner->name;
                $plant->owner_email = $specificOwner->email;
                $plant->owner_uuid = $specificOwner->uuid;
            }
        } catch (\Exception $e) {
            $plant = null;
        }
        
        $user = auth()->user();
        return view('plants.show', compact('plant', 'user', 'id'));
    }

    public function showRemote($id, Request $request)
    {
        $specificOwnerUuid = '6a36660d-daae-48dd-a4fe-000b191b13d8';
        $specificOwner = \App\Models\User::where('uuid', $specificOwnerUuid)->first();
        if ($specificOwner) {
            \Log::debug('Found specific owner', [
                'uuid' => $specificOwner->uuid,
                'name' => $specificOwner->name,
                'email' => $specificOwner->email
            ]);
        } else {
            \Log::warning('Specific owner not found', ['uuid' => $specificOwnerUuid]);
        }

        $client = new \GuzzleHttp\Client();
        // Build API URL with optional start/end params
        $url = "http://127.0.0.1:5001/plant_view/{$id}";
        $query = [];
        if ($request->has('start')) {
            $query['start'] = $request->input('start');
        }
        if ($request->has('end')) {
            $query['end'] = $request->input('end');
        }
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }
        $token = 'f9c2f80e1c0e5b6a3f7f40e6f2e9c9d0af7eaabc6b37a4d9728e26452b81fc13';
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'timeout' => 10,
            ]);
            $plant = json_decode($body = $response->getBody()->getContents(), false);
            // Normalize plant object for view compatibility
            if ($plant) {
                // Always use the specific owner we fetched at the beginning
                if ($specificOwner) {
                    $plant->owner_name = $specificOwner->name;
                    $plant->owner_email = $specificOwner->email;
                    $plant->owner_uuid = $specificOwner->uuid;
                    \Log::debug('Applied specific owner to plant', [
                        'plant_uid' => $plant->uid ?? 'N/A',
                        'owner_name' => $specificOwner->name,
                        'owner_email' => $specificOwner->email,
                        'owner_uuid' => $specificOwner->uuid
                    ]);
                }
                
                $plant->uid = $plant->uid ?? null;
                $plant->uuid = $plant->uuid ?? $plant->uid ?? null;
                
                // Debug raw response to locate owner field
                $ownerValues = [];
                foreach (get_object_vars($plant) as $key => $value) {
                    if (strpos(strtolower($key), 'owner') !== false) {
                        $ownerValues[$key] = $value;
                    }
                }
                \Log::debug('All owner-related fields in API response', $ownerValues);
                
                // Find owner by UUID and set name and email - Debug this process
                \Log::debug('Plant owner data before processing', [
                    'owner_uuid' => $plant->owner ?? null,
                    'owner_email' => $plant->owner_email ?? null
                ]);
                
                $owner = null;
                
                // First try to find by UUID if available
                if (!empty($plant->owner)) {
                    $owner = \App\Models\User::where('uuid', $plant->owner)->first();
                    if ($owner) {
                        \Log::debug('Found owner by UUID', ['name' => $owner->name, 'email' => $owner->email]);
                    }
                }
                
                // If not found by UUID, try by email
                if (!$owner && !empty($plant->owner_email)) {
                    $owner = \App\Models\User::where('email', $plant->owner_email)->first();
                    if ($owner) {
                        \Log::debug('Found owner by email', ['name' => $owner->name, 'email' => $owner->email]);
                    }
                }
                
                // Set owner information regardless of source, but don't override the specific owner
                // Only set if owner_name isn't already set by the specific owner lookup
                if (!isset($plant->owner_name) || !$plant->owner_name) {
                    if ($owner) {
                        $plant->owner_name = $owner->name;
                        $plant->owner_email = $owner->email;
                    } else {
                        $plant->owner_name = null;
                        $plant->owner_email = $plant->owner_email ?? '';
                        \Log::warning('No owner found for plant', [
                            'plant_uid' => $plant->uid,
                            'owner_uuid' => $plant->owner ?? null,
                            'owner_email' => $plant->owner_email ?? null
                        ]);
                    }
                } else {
                    \Log::debug('Keeping existing owner info', [
                        'owner_name' => $plant->owner_name,
                        'owner_email' => $plant->owner_email
                    ]);
                }
                
                // --- Map all relevant API fields to $plant for the view ---
                $plant->status = $plant->status ?? ($plant->status ?? '');
                $plant->capacity = $plant->capacity ?? ($plant->capacity ?? null);
                $plant->latitude = $plant->latitude ?? ($plant->latitude ?? null);
                $plant->longitude = $plant->longitude ?? ($plant->longitude ?? null);
                $plant->last_updated = $plant->last_updated ?? ($plant->last_updated ?? null);
                $plant->uuid = $plant->uuid ?? ($plant->uuid ?? null);
                $plant->plant_metadata = $plant->plant_metadata ?? ($plant->plant_metadata ?? []);
                // Format last_updated for display
                if (isset($plant->last_updated)) {
                    try {
                        $plant->formatted_last_updated = \Carbon\Carbon::parse($plant->last_updated)->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        $plant->formatted_last_updated = $plant->last_updated;
                    }
                } else {
                    $plant->formatted_last_updated = 'N/A';
                }
                // --- Plant Metadata: flatten and prettify for display ---
                $plant->metadata_flat = [];
                if (isset($plant->plant_metadata) && !empty($plant->plant_metadata)) {
                    $flatten = function($data, $prefix = '') use (&$flatten) {
                        $result = [];
                        if (is_object($data)) $data = (array)$data;
                        foreach ($data as $key => $value) {
                            $label = $prefix ? $prefix . ' / ' . ucfirst(str_replace('_', ' ', $key)) : ucfirst(str_replace('_', ' ', $key));
                            if (is_array($value) || is_object($value)) {
                                $result += $flatten($value, $label);
                            } else {
                                $result[$label] = $value;
                            }
                        }
                        return $result;
                    };
                    $plant->metadata_flat = $flatten($plant->plant_metadata);
                }
                // Merge owner info from DB as the first entries
                if (!empty($plant->owner_name) || !empty($plant->owner_email)) {
                    $ownerArr = [];
                    if (!empty($plant->owner_name)) {
                        $ownerArr['Owner Name'] = $plant->owner_name;
                    }
                    if (!empty($plant->owner_email)) {
                        $ownerArr['Owner Email'] = $plant->owner_email;
                    }
                    // Prepend owner info to metadata_flat
                    $plant->metadata_flat = $ownerArr + $plant->metadata_flat;
                }
                // Only keep allowed keys for display
                $allowedKeys = [
                    'Owner Name',
                    'Owner Email',
                    'Capacity',
                    'Latitude',
                    'Longitude',
                    'Status',
                    'Last Updated',
                    'Updated At',
                    'Last Updated At'
                ];
                $plant->metadata_flat = array_filter(
                    $plant->metadata_flat,
                    function($key) use ($allowedKeys) {
                        foreach ($allowedKeys as $allowed) {
                            if (stripos($key, $allowed) !== false) return true;
                        }
                        return false;
                    },
                    ARRAY_FILTER_USE_KEY
                );
                
                // Format Updated At and Last Updated At to Y-m-d H:i:s
                foreach (['Updated At', 'Last Updated', 'Last Updated At'] as $key) {
                    foreach ($plant->metadata_flat as $metaKey => $metaValue) {
                        if (stripos($metaKey, $key) !== false && !empty($metaValue)) {
                            // Try to parse as timestamp or string date
                            try {
                                if (is_numeric($metaValue)) {
                                    $plant->metadata_flat[$metaKey] = \Carbon\Carbon::createFromTimestamp((int)$metaValue)->format('Y-m-d H:i:s');
                                } else {
                                    $plant->metadata_flat[$metaKey] = \Carbon\Carbon::parse($metaValue)->format('Y-m-d H:i:s');
                                }
                            } catch (\Exception $e) {
                                // Leave as is if parsing fails
                            }
                        }
                    }
                }
                
                // Normalize controllers/mainfeeds/devices for Blade compatibility
                $plant->controllers = collect($plant->controllers ?? [])->map(function ($controller) {
                    $controllerObj = (object) $controller;
                    // Use controller_main_feeds for main feeds
                    $mainFeeds = $controller->controller_main_feeds ?? [];
                    $controllerObj->mainFeeds = collect($mainFeeds)->map(function ($feed) {
                        $feedObj = (object) $feed;
                        // Use main_feed_devices for devices
                        $devices = $feed->main_feed_devices ?? [];
                        // Recursively normalize assigned_devices
                        $normalizeDevice = function ($device) use (&$normalizeDevice) {
                            $deviceObj = (object) $device;
                            $assigned = $device->assigned_devices ?? [];
                            $deviceObj->assigned_devices = collect($assigned)->map(fn($d) => $normalizeDevice($d));
                            return $deviceObj;
                        };
                        $feedObj->devices = collect($devices)->map(fn($d) => $normalizeDevice($d));
                        return $feedObj;
                    });
                    return $controllerObj;
                });
                // Process chart data from the API
                // Make sure all chart data is properly initialized
                $plant->energy_chart = $plant->energy_chart ?? [];
                $plant->battery_price = $plant->battery_price ?? [];
                $plant->battery_savings = $plant->battery_savings ?? [];
                
                // Make sure aggregated_data_snapshots is available as a fallback
                if (empty($plant->aggregated_data_snapshots) && !empty($plant->data_snapshots)) {
                    $plant->aggregated_data_snapshots = $plant->data_snapshots;
                }
                
                // Convert aggregated_data_snapshots to array if it's not already
                if (!empty($plant->aggregated_data_snapshots) && !is_array($plant->aggregated_data_snapshots)) {
                    $plant->aggregated_data_snapshots = [$plant->aggregated_data_snapshots];
                }
                
                // DEBUG: Log the structure of the first data snapshot
                if (!empty($plant->aggregated_data_snapshots)) {
                    \Log::debug('First data snapshot structure', [
                        'snapshot' => $plant->aggregated_data_snapshots[0] ?? null
                    ]);
                }
                
                // DEBUG: Log plant_metadata if it exists
                if (isset($plant->plant_metadata)) {
                    \Log::debug('Plant metadata structure', [
                        'type' => gettype($plant->plant_metadata),
                        'plant_metadata' => $plant->plant_metadata
                    ]);
                }
                
                // DEBUG: Log owner information
                \Log::debug('Plant owner information', [
                    'owner_uuid' => $plant->owner ?? null,
                    'owner_email' => $plant->owner_email ?? null,
                    'owner_name' => $plant->owner_name ?? null,
                    'last_updated' => $plant->last_updated ?? null,
                    'formatted_last_updated' => $plant->formatted_last_updated ?? null,
                    'updated_at' => $plant->updated_at ?? null,
                    'formatted_updated_at' => $plant->formatted_updated_at ?? null
                ]);
                
                // Check if we can find any users that might match
                $allUsers = \App\Models\User::all();
                $userUuids = $allUsers->pluck('uuid', 'email')->toArray();
                \Log::debug('All available users', [
                    'count' => $allUsers->count(),
                    'user_uuids' => $userUuids
                ]);
                
                // If we have snapshots but no chart data, pre-process it here
                if (empty($plant->energy_chart) && !empty($plant->aggregated_data_snapshots)) {
                    // Format energy chart data
                    $energyChart = [];
                    foreach ($plant->aggregated_data_snapshots as $snapshot) {
                        // Convert snapshot to object if it's an array
                        if (is_array($snapshot)) {
                            $snapshot = (object) $snapshot;
                        }
                        
                        // Use timestamp or dt (convert to ISO string)
                        $timestamp = null;
                        if (!empty($snapshot->timestamp)) {
                            $timestamp = $snapshot->timestamp;
                        } elseif (!empty($snapshot->dt)) {
                            // Convert Unix timestamp to ISO string
                            $timestamp = date('c', $snapshot->dt);
                        }

                        if ($timestamp) {
                            $energyChart[$timestamp] = [
                                'pv_p' => $snapshot->pv_p ?? 0,
                                'battery_p' => $snapshot->battery_p ?? 0,
                                'grid_p' => $snapshot->grid_p ?? 0
                            ];
                        }
                    }
                    $plant->energy_chart = $energyChart;
                }
                
                // Format battery price data if needed
                if (empty($plant->battery_price) && !empty($plant->aggregated_data_snapshots)) {
                    $batteryPrice = [];
                    foreach ($plant->aggregated_data_snapshots as $snapshot) {
                        // Convert snapshot to object if it's an array
                        if (is_array($snapshot)) {
                            $snapshot = (object) $snapshot;
                        }
                        
                        // Use timestamp or dt (convert to ISO string)
                        $timestamp = null;
                        if (!empty($snapshot->timestamp)) {
                            $timestamp = $snapshot->timestamp;
                        } elseif (!empty($snapshot->dt)) {
                            // Convert Unix timestamp to ISO string
                            $timestamp = date('c', $snapshot->dt);
                        }

                        if ($timestamp) {
                            $batteryPrice[$timestamp] = [
                                'battery_p' => $snapshot->battery_p ?? 0,
                                'tariff' => $snapshot->tariff ?? 0.15 // Default tariff if missing
                            ];
                        }
                    }
                    $plant->battery_price = $batteryPrice;
                }
                
                // Format battery savings data if needed
                if (empty($plant->battery_savings) && !empty($plant->aggregated_data_snapshots)) {
                    $batterySavings = [];
                    foreach ($plant->aggregated_data_snapshots as $snapshot) {
                        // Convert snapshot to object if it's an array
                        if (is_array($snapshot)) {
                            $snapshot = (object) $snapshot;
                        }
                        
                        // Use timestamp or dt (convert to ISO string)
                        $timestamp = null;
                        if (!empty($snapshot->timestamp)) {
                            $timestamp = $snapshot->timestamp;
                        } elseif (!empty($snapshot->dt)) {
                            // Convert Unix timestamp to ISO string
                            $timestamp = date('c', $snapshot->dt);
                        }

                        if ($timestamp) {
                            // Battery savings might be directly in the snapshot or calculated from other values
                            $savings = $snapshot->battery_savings ?? 0;
                            
                            // If no explicit savings, try to calculate from battery power and tariff if available
                            if (empty($savings) && isset($snapshot->battery_p)) {
                                $batteryPower = $snapshot->battery_p;
                                $tariff = $snapshot->tariff ?? 0.15; // Default tariff if missing
                                
                                // For this example, let's handle both positive and negative battery power
                                // Negative power (charging) also has economic value in some scenarios
                                $powerForSavings = abs($batteryPower); // Use absolute value for demonstration
                                if ($powerForSavings > 0) {
                                    $savings = ($powerForSavings / 1000) * $tariff * 0.2; // Assuming 20% efficiency gain
                                }
                            }
                            
                            $batterySavings[$timestamp] = [
                                'battery_savings' => $savings ?? 0
                            ];
                        }
                    }
                    $plant->battery_savings = $batterySavings;
                }
            }
        } catch (\Exception $e) {
            $plant = null;
        }
        $user = auth()->user();
        // dd($id);
        return view('plants.show', compact('plant', 'user', 'id'));
    }
    
    /**
     * Get plant data with date range filtering.
     * 
     * @param string $plant Plant ID
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData($plant, Request $request)
    {
        // Validate timestamp parameters
        $request->validate([
            'start' => 'required|numeric',
            'end' => 'nullable|numeric',
        ]);

        $start = $request->input('start');
        $end = $request->input('end');
        
        // Check if it's today based on whether end parameter is provided
        $isToday = !$request->has('end') || $end === null;
        
        \Log::info('Fetching plant data with date range', [
            'plant_id' => $plant,
            'start_timestamp' => $start,
            'end_timestamp' => $end,
            'is_today' => $isToday,
            'start_date' => date('Y-m-d H:i:s', $start),
            'end_date' => $end ? date('Y-m-d H:i:s', $end) : 'N/A (today - until now)',
        ]);

        // Use Guzzle to fetch data from the API
        $client = new \GuzzleHttp\Client();
        $url = "http://127.0.0.1:5001/plant_view/{$plant}";
        
        // Build query parameters - simple logic
        if ($isToday) {
            // For today: only start parameter
            $url .= "?start={$start}";
            \Log::info('API call for TODAY: using only start parameter', ['url' => $url]);
        } else {
            // For historical dates: both start and end parameters
            $url .= "?start={$start}&end={$end}";
            \Log::info('API call for HISTORICAL date: using both start and end parameters', ['url' => $url]);
        }
        
        $token = 'f9c2f80e1c0e5b6a3f7f40e6f2e9c9d0af7eaabc6b37a4d9728e26452b81fc13';
        
        try {
            \Log::info('Making API request', ['url' => $url]);
            
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'timeout' => 10,
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);
            
            \Log::info('API response received', [
                'data_keys' => array_keys($data ?? []),
                'snapshots_count' => count($data['aggregated_data_snapshots'] ?? [])
            ]);
            
            // Process the data and format for charts
            $result = $this->formatDataForCharts($data);
            
            \Log::info('Formatted result', [
                'result_keys' => array_keys($result),
                'energy_chart_count' => count($result['energy_chart'] ?? []),
                'battery_price_count' => count($result['battery_price'] ?? []),
                'battery_savings_count' => count($result['battery_savings'] ?? [])
            ]);
            
            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Error fetching plant data from API', [
                'plant_id' => $plant,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Failed to fetch plant data'], 500);
        }
    }
    
    /**
     * Get available data dates for a plant.
     * 
     * @param string $plant Plant ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableDates($plant)
    {
        try {
            // Use Guzzle to fetch data from the API for the last 60 days
            $client = new \GuzzleHttp\Client();
            $token = 'f9c2f80e1c0e5b6a3f7f40e6f2e9c9d0af7eaabc6b37a4d9728e26452b81fc13';
            
            $availableDates = [];
            $today = new \DateTime();
            $startDate = clone $today;
            $startDate->modify('-60 days'); // Check last 60 days
            
            // Check each day for data availability
            $current = clone $startDate;
            while ($current <= $today) {
                $dateStr = $current->format('Y-m-d');
                
                // Calculate start and end timestamps for the day in EEST
                $dayStart = clone $current;
                $dayStart->setTime(0, 0, 0);
                $utcMidnight = $dayStart->getTimestamp() - (3 * 60 * 60); // EEST offset
                $startOfDay = $utcMidnight;
                $endOfDay = $startOfDay + (24 * 60 * 60) - 1;
                
                // Make API request to check for data
                $url = "http://127.0.0.1:5001/plant_view/{$plant}?start={$startOfDay}&end={$endOfDay}";
                
                try {
                    $response = $client->request('GET', $url, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token,
                            'Accept' => 'application/json',
                        ],
                        'timeout' => 5,
                    ]);
                    
                    $data = json_decode($response->getBody()->getContents(), true);
                    
                    // Check if we have any meaningful data
                    $hasData = false;
                    if (!empty($data['aggregated_data_snapshots'])) {
                        $snapshots = $data['aggregated_data_snapshots'];
                        if (count($snapshots) > 0) {
                            $hasData = true;
                        }
                    }
                    
                    if ($hasData) {
                        $availableDates[] = $dateStr;
                    }
                    
                } catch (\Exception $e) {
                    // Skip this date if API call fails
                    \Log::debug("Failed to check data for date {$dateStr}: " . $e->getMessage());
                }
                
                $current->modify('+1 day');
            }
            
            \Log::info('Available dates calculated', [
                'plant_id' => $plant,
                'dates_count' => count($availableDates),
                'dates' => $availableDates
            ]);
            
            return response()->json([
                'success' => true,
                'dates' => $availableDates
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching available dates', [
                'plant_id' => $plant,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch available dates'
            ], 500);
        }
    }

    /**
     * Format data for chart display
     * 
     * @param array $data Raw data from API
     * @return array Formatted chart data
     */
    private function formatDataForCharts($data)
    {
        $result = [
            'energy_chart' => [],
            'battery_price' => [],
            'battery_savings' => []
        ];
        
        // Process raw data into chart format
        $aggregatedSnapshots = $data['aggregated_data_snapshots'] ?? [];
        
        foreach ($aggregatedSnapshots as $snapshot) {
            // Convert snapshot to object if it's an array
            if (is_array($snapshot)) {
                $snapshot = (object) $snapshot;
            }
            
            // Use timestamp or dt (convert to ISO string)
            $timestamp = null;
            if (!empty($snapshot->timestamp)) {
                $timestamp = $snapshot->timestamp;
            } elseif (!empty($snapshot->dt)) {
                // Convert Unix timestamp to ISO string
                $timestamp = date('c', $snapshot->dt);
            }

            if ($timestamp) {
                // Energy chart data
                $result['energy_chart'][$timestamp] = [
                    'pv_p' => $snapshot->pv_p ?? 0,
                    'battery_p' => $snapshot->battery_p ?? 0,
                    'grid_p' => $snapshot->grid_p ?? 0
                ];
                
                // Battery price data
                $result['battery_price'][$timestamp] = [
                    'battery_p' => $snapshot->battery_p ?? 0,
                    'tariff' => $snapshot->tariff ?? 0.15 // Default tariff if missing
                ];
                
                // Battery savings data
                $batterySavings = $snapshot->battery_savings ?? null;
                
                // Calculate savings if missing but battery power and tariff are available
                if ($batterySavings === null && isset($snapshot->battery_p)) {
                    $batteryPower = floatval($snapshot->battery_p);
                    $tariff = floatval($snapshot->tariff ?? 0.15);
                    
                    // Battery discharging (positive) means saving money
                    if ($batteryPower > 0) {
                        $batterySavings = ($batteryPower / 1000) * $tariff; // Convert W to kW
                    } else {
                        $batterySavings = 0; // No savings when charging
                    }
                }
                
                if ($batterySavings !== null) {
                    $result['battery_savings'][$timestamp] = [
                        'battery_savings' => $batterySavings
                    ];
                }
            }
        }
        
        return $result;
    }
}
