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
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h2 class="text-lg font-semibold mb-3">General Info</h2>
                            <div class="bg-white rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                @if(!empty($plant->metadata_flat))
                                    <table class="min-w-full">
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($plant->metadata_flat as $metaKey => $metaValue)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-3 py-2 text-sm font-medium text-gray-900 bg-gray-50 w-2/5 border-r border-gray-200">
                                                        {{ $metaKey }}
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-gray-700">
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
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                            @if($metaValue === 'Working') bg-green-100 text-green-800
                                                            @elseif($metaValue === 'Maintenance') bg-yellow-100 text-yellow-800
                                                            @elseif($metaValue === 'Offline') bg-red-100 text-red-800
                                                            @else bg-gray-100 text-gray-800
                                                            @endif">
                                                            @if($metaValue === 'Working')
                                                                <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                                    <circle cx="4" cy="4" r="3" />
                                                                </svg>
                                                            @endif
                                                            {{ $metaValue }}
                                                        </span>
                                                    @elseif($metaKey === 'Capacity')
                                                        <span class="font-semibold text-indigo-700">
                                                            {{ number_format($metaValue) }} kW
                                                        </span>
                                                    @elseif(str_contains($metaKey, 'Updated at') || str_contains($metaKey, 'Date'))
                                                        <span class="text-gray-600">
                                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            {{ $metaValue }}
                                                        </span>
                                                    @else
                                                        {{ $metaValue }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Map Location</h3>
                            <div id="map" class="rounded-lg shadow border border-gray-200" style="height: 400px;"></div>
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
            // Prefer plant_metadata lat/lng if available, else fallback
            let lat = @json($plant->metadata_flat['Latitude'] ?? $plant->latitude ?? 0);
            let lng = @json($plant->metadata_flat['Longitude'] ?? $plant->longitude ?? 0);
            const map = L.map('map').setView([lat, lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([lat, lng])
                .addTo(map)
                .bindPopup(
                    "<strong>{{ $plant->name ?? $plant->uid }}</strong><br>Lat: " + lat + "<br>Lng: " + lng
                )
                .openPopup();
        });
    </script>
</x-app-layout>
