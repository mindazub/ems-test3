<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plant Details') }} - {{ $plant->name }}
        </h2>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <!-- PLANT INFO SECTION -->
                <div class="mb-6 flex flex-wrap gap-6">
                    <div class="w-full lg:w-1/2 space-y-2">
                        <h3 class="text-lg font-semibold mb-2">General Info</h3>
                        <p><strong>Owner Email:</strong> {{ $plant->owner_email }}</p>
                        <p><strong>Status:</strong> {{ $plant->status }}</p>
                        <p><strong>Capacity:</strong> {{ $plant->capacity }} W</p>
                        <p><strong>Location:</strong> Lat {{ $plant->latitude }}, Lng {{ $plant->longitude }}</p>
                        <p><strong>Last Updated:</strong>
                            {{ $plant->last_updated ? \Carbon\Carbon::createFromTimestamp($plant->last_updated)->format('Y-m-d H:i') : 'N/A' }}
                        </p>
                    </div>
                    <div class="w-full lg:w-1/3">
                        <h3 class="text-lg font-semibold mb-2">Map Location</h3>
                        <div id="map" class="rounded shadow border" style="height: 200px;"></div>
                    </div>
                </div>

                <!-- STACKED CHARTS EACH WITH OWN TABS -->
                @include('plants.partials.plant-chart')

                @include('plants.partials.devices-list')


            </div>


        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mapContainer = document.getElementById('map');
            if (mapContainer) {
                const lat = {{ $plant->latitude }};
                const lng = {{ $plant->longitude }};

                const map = L.map('map').setView([lat, lng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);
                L.marker([lat, lng]).addTo(map).bindPopup(`Plant: {{ $plant->name }}`).openPopup();
            }

            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            document.querySelectorAll('.toggle-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const rowId = button.getAttribute('data-target');
                    const row = document.querySelector(rowId);
                    const icon = button.querySelector('.toggle-icon');
                    if (row.style.display === 'none') {
                        row.style.display = 'table-row';
                        icon.classList.remove('bi-caret-right-fill');
                        icon.classList.add('bi-caret-down-fill');
                    } else {
                        row.style.display = 'none';
                        icon.classList.remove('bi-caret-down-fill');
                        icon.classList.add('bi-caret-right-fill');
                    }
                });
            });
        });
    </script>
</x-app-layout>
