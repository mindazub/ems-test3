{{-- resources/views/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plant Details') }} - {{ $plant->name }}
        </h2>
    </x-slot>

    {{-- External Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

    {{-- Page Content --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">

                <!-- PLANT INFO SECTION -->
                <div class="mb-6 flex flex-wrap gap-6">
                    <div class="w-full lg:w-1/2 space-y-2">
                        <h2 class="text-lg font-semibold mb-2">General Info</h2>
                        <p><strong>Owner Email:</strong> {{ $plant->owner_email }}</p>
                        <p><strong>Status:</strong> {{ $plant->status }}</p>
                        <p><strong>Capacity:</strong> {{ $plant->capacity }} W</p>
                        <p><strong>Location:</strong> Lat {{ $plant->latitude }}, Lng {{ $plant->longitude }}</p>
                        <p><strong>Last Updated:</strong>
                            {{ $plant->last_updated ? \Carbon\Carbon::createFromTimestamp($plant->last_updated)->format('Y-m-d H:i') : 'N/A' }}
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('plants.index') }}" class="btn btn-sm btn-primary">Back</a>
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

    {{-- JavaScript Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const expandedRowsKey = 'expandedSlaveRows';

            function saveExpandedRows() {
                const expanded = Array.from(document.querySelectorAll('.slave-row.show'))
                    .map(row => row.id);
                localStorage.setItem(expandedRowsKey, JSON.stringify(expanded));
            }

            function restoreExpandedRows() {
                const expanded = JSON.parse(localStorage.getItem(expandedRowsKey) || '[]');
                expanded.forEach(id => {
                    const el = document.getElementById(id);
                    if (el && !el.classList.contains('show')) {
                        console.log(`Restoring expanded state for #${id}`);
                        const instance = new bootstrap.Collapse(el, {
                            toggle: false
                        });
                        instance.show(); // separate to avoid event overlap
                    }
                });
            }

            restoreExpandedRows();

            const collapseElements = document.querySelectorAll('.collapse.slave-row');

            collapseElements.forEach(collapseEl => {
                const toggleBtn = document.querySelector(`[data-bs-target="#${collapseEl.id}"]`);
                const icon = toggleBtn?.querySelector('.toggle-icon');

                collapseEl.addEventListener('show.bs.collapse', () => {
                    console.log(`▶️ Revealing: #${collapseEl.id}`);
                    if (icon) {
                        icon.classList.remove('bi-plus-circle-fill');
                        icon.classList.add('bi-dash-circle-fill');
                    }
                });

                collapseEl.addEventListener('shown.bs.collapse', () => {
                    console.log(`✅ Revealed: #${collapseEl.id}`);
                    saveExpandedRows();
                });

                collapseEl.addEventListener('hide.bs.collapse', () => {
                    console.log(`⏹️ Hiding: #${collapseEl.id}`);
                    if (icon) {
                        icon.classList.remove('bi-dash-circle-fill');
                        icon.classList.add('bi-plus-circle-fill');
                    }
                });

                collapseEl.addEventListener('hidden.bs.collapse', () => {
                    console.log(`❌ Hidden: #${collapseEl.id}`);
                    saveExpandedRows();
                });
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Existing collapse logic...

            // --- LEAFLET MAP INIT ---
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
