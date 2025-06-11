@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plant Details') }} - {{ isset($id) ? Str::substr($id, 0, 8) :  'N/A' }}
        </h2>
    </x-slot>

    {{-- Only Leaflet CSS needed --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    
    <style>
        /* Ensure grid layout works properly */
        @media (min-width: 1024px) {
            .plant-grid {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 1.5rem !important;
            }
            .plant-grid > div {
                min-width: 0 !important;
                max-width: 100% !important;
            }
        }
        
        /* Prevent table from overflowing its container */
        .table-container {
            overflow: hidden;
            max-width: 100%;
        }
        
        .table-container table {
            table-layout: fixed;
            width: 100%;
        }
        
        .table-container td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        /* Map legend styling */
        .map-legend {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 12px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            font-size: 12px;
            border: 1px solid #e5e7eb;
        }
        
        .map-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 2px;
        }
        
        .map-legend-item:last-child {
            margin-bottom: 0;
        }
        
        .legend-icon {
            width: 12px;
            height: 12px;
            background: #3b82f6;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">

                <!-- PLANT INFO SECTION -->
                <div class="mb-6">
                    <!-- Plant ID Header -->
                    <div class="mb-6">
                        <h1 class="text-4xl font-bold">
                            <span class="text-gray-400 italic">#ID&nbsp;{{ Str::substr($id ?? 'N/A', 0, 8) }}</span>
                        </h1>
                    </div>

                    <!-- Main Content: Table + Map in 50/50 layout -->
                    <div class="plant-grid grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                        <!-- Left Side: General Info Table -->
                        <div class="min-w-0 w-full">
                            <h2 class="text-lg font-semibold mb-3">General Info</h2>
                            <div class="table-container bg-white rounded-lg overflow-hidden border border-gray-200 shadow-sm max-w-full">
                                @if(!empty($plant->metadata_flat))
                                    <div class="overflow-x-auto">
                                        <table class="w-full table-fixed">
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach($plant->metadata_flat as $metaKey => $metaValue)
                                                    <tr class="hover:bg-gray-50 transition-colors">
                                                        <td class="px-3 py-2 text-sm font-medium text-gray-900 bg-gray-50 border-r border-gray-200 w-2/5 break-words">
                                                            {{ $metaKey }}
                                                        </td>
                                                        <td class="px-3 py-2 text-sm text-gray-700 w-3/5 break-words">
                                                    @if(is_array($metaValue))
                                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                            {{ json_encode($metaValue) }}
                                                        </span>
                                                    @elseif($metaKey === 'Owner Email')
                                                        <a href="mailto:{{ $metaValue }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                            {{ $metaValue }}
                                                        </a>
                                                    @elseif(in_array($metaKey, ['Latitude', 'Longitude']))
                                                        <span class="font-mono text-green-700">{{ $metaValue }}</span>
                                                    @elseif($metaKey === 'Status')
                                                        @if($metaValue === 'Working')
                                                            <span class="bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-green-900 dark:text-green-300">{{ $metaValue }}</span>
                                                        @elseif($metaValue === 'Maintenance')
                                                            <span class="bg-yellow-100 text-yellow-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-yellow-900 dark:text-yellow-300">{{ $metaValue }}</span>
                                                        @elseif($metaValue === 'Offline')
                                                            <span class="bg-red-100 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-red-900 dark:text-red-300">{{ $metaValue }}</span>
                                                        @else
                                                            <span class="bg-gray-100 text-gray-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300">{{ $metaValue }}</span>
                                                        @endif
                                                    @elseif($metaKey === 'Capacity')
                                                        <span class="font-semibold text-indigo-700">
                                                            {{ number_format($metaValue / 1000) }} kWh
                                                        </span>
                                                    @elseif(str_contains($metaKey, 'Updated at') || str_contains($metaKey, 'Date'))
   
                                                    <span class="text-gray-600">

                                                            {{ $metaValue }}
                                                        </span>
                                                    @else
                                                        {{ $metaValue }}
                                                    @endif                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                            @else
                                <div class="px-4 py-6 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2">No plant information available</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('plants.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-2 rounded transition">
                                    Back to All Plants List
                                </a>
                            </div>
                        </div>

                        <!-- Right Side: Map -->
                        <div class="min-w-0 w-full">
                            <h3 class="text-lg font-semibold mb-3">Map Location</h3>
                            <div class="relative">
                                <div id="map" class="rounded-lg shadow border border-gray-200 w-full max-w-full"></div>
                                <div class="map-legend">
                                    <div class="map-legend-item">
                                        <div class="legend-icon"></div>
                                        <span class="text-gray-700 font-medium">Plant Location</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CHARTS -->
                @include('plants.partials.plant-chart', ['plant' => $plant, 'user' => $user])

                <!-- DEVICES LIST -->
                @include('plants.partials.devices-list')
            </div>
        </div>
    </div>

    {{-- JS Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        // --- LEAFLET MAP INIT ---
        document.addEventListener('DOMContentLoaded', function() {
            // Function to adjust map height to match table
            function adjustMapHeight() {
                const tableContainer = document.querySelector('.table-container');
                const mapContainer = document.getElementById('map');
                
                if (tableContainer && mapContainer) {
                    // Get just the table height (without the back button)
                    const tableHeight = tableContainer.offsetHeight;
                    
                    // Set map height to match table exactly
                    mapContainer.style.height = tableHeight + 'px';
                    
                    // Invalidate map size and recenter to ensure proper rendering
                    if (window.mapInstance) {
                        setTimeout(() => {
                            window.mapInstance.invalidateSize();
                            // Recenter the map to ensure marker is in the center
                            const currentCenter = window.mapInstance.getCenter();
                            window.mapInstance.setView(currentCenter, window.mapInstance.getZoom());
                        }, 150);
                    }
                }
            }
            
            // Prefer plant_metadata lat/lng if available, else fallback
            let lat = @json($plant->metadata_flat['Latitude'] ?? $plant->latitude ?? 0);
            let lng = @json($plant->metadata_flat['Longitude'] ?? $plant->longitude ?? 0);
            const map = L.map('map').setView([lat, lng], 13);
            
            // Store map instance globally for height adjustments
            window.mapInstance = map;

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([lat, lng])
                .addTo(map)
                .bindPopup(
                    "<strong>{{ $plant->name ?? $plant->uid }}</strong><br>" +
                    "Lat: " + lat + "<br>" +
                    "Long: " + lng
                )
                .openPopup();
            
            // Ensure the marker is centered in the map view
            map.setView([lat, lng], 13);
            
            // Adjust map height after map is loaded and center the marker
            setTimeout(() => {
                adjustMapHeight();
                // Re-center after height adjustment to ensure marker is visible and centered
                map.setView([lat, lng], 13);
            }, 200);
            
            // Adjust map height on window resize
            window.addEventListener('resize', adjustMapHeight);
        });
    </script>
</x-app-layout>
