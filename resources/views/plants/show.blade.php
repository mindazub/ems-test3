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
                <div class="mb-6 flex flex-wrap gap-6">
                    <div class="w-full lg:w-1/2 space-y-2">
                        <h1 class="text-4xl font-bold ">
                            <span class="text-gray-400 italic">#ID&nbsp;{{ Str::substr($id ?? 'N/A', 0, 8) }}</span>
                        </h1>
                        <h2 class="text-lg font-semibold mb-2">General Info</h2>
                        <div class="space-y-1">
                                                        
                            <p>
                            @if(!empty($plant->metadata_flat))
                                
                                    <div class="space-y-1">
                                        @foreach($plant->metadata_flat as $metaKey => $metaValue)
                                            <p><span class="font-semibold">{{ $metaKey }}:</span> {{ is_array($metaValue) ? json_encode($metaValue) : $metaValue }}</p>
                                        @endforeach
                                    </div>

                            @endif
</p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('plants.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-2 rounded transition">
                                Back to All Plants List
                            </a>
                        </div>
                    </div>

                    <div class="w-full lg:w-1/3">
                        <h3 class="text-lg font-semibold mb-2">Map Location</h3>
                        <div id="map" class="rounded shadow border" style="height: 200px;"></div>
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
