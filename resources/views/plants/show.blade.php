<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plant Details') }} - {{ $plant->name }}
        </h2>
    </x-slot>

    {{-- Only Leaflet CSS needed --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">

                <!-- PLANT INFO SECTION -->
                <div class="mb-6 flex flex-wrap gap-6">
                    <div class="w-full lg:w-1/2 space-y-2">
                        <h1>
                            <span class="text-gray-400 italic">#ID&nbsp;{{ $plant->id }}&nbsp;&nbsp;</span>
                            <span class="font-semibold">{{ $plant->name }}</span> Details
                        </h1>
                        <h2 class="text-lg font-semibold mb-2">General Info</h2>
                        <div class="space-y-1">
                            <p><span class="font-semibold">Owner Email:</span> {{ $plant->owner_email }}</p>
                            <p><span class="font-semibold">Status:</span> {{ $plant->status }}</p>
                            <p><span class="font-semibold">Battery Capacity:</span> {{ number_format($plant->capacity / 1000, 0) }} kWh</p>
                            <p><span class="font-semibold">Location:</span> Lat {{ $plant->latitude }}, Lng {{ $plant->longitude }}</p>
                            <p><span class="font-semibold">Last Updated:</span>
                                {{ $plant->last_updated ? \Carbon\Carbon::createFromTimestamp($plant->last_updated)->format('Y-m-d H:i') : 'N/A' }}
                            </p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('plants.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-2 rounded transition">
                                Back
                            </a>
                        </div>
                    </div>

                    <div class="w-full lg:w-1/3">
                        <h3 class="text-lg font-semibold mb-2">Map Location</h3>
                        <div id="map" class="rounded shadow border" style="height: 200px;"></div>
                    </div>
                </div>

                <!-- CHARTS -->
                @include('plants.partials.plant-chart')

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
            const lat = {{ $plant->latitude }};
            const lng = {{ $plant->longitude }};
            const map = L.map('map').setView([lat, lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([lat, lng])
                .addTo(map)
                .bindPopup(
                    "<strong>{{ $plant->name }}</strong><br>Lat: {{ $plant->latitude }}<br>Lng: {{ $plant->longitude }}"
                )
                .openPopup();
        });
    </script>
</x-app-layout>
