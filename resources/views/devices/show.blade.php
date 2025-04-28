<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Device Details') }} - ID #{{ $device->id }}
        </h2>
    </x-slot>

    {{-- External Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Page Content --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">

                <!-- DEVICE INFO SECTION -->
                <div class="mb-6 flex flex-wrap gap-6">
                    <div class="w-full lg:w-1/2 space-y-2">
                        <h1>{{ $device->device_model }} Details</h1>
                        <h2 class="text-lg font-semibold mb-2">General Info</h2>
                        <p><strong>Type:</strong> {{ $device->device_type }}</p>
                        <p><strong>Manufacturer:</strong> {{ $device->manufacturer }}</p>
                        <p><strong>Model:</strong> {{ $device->device_model }}</p>
                        <p><strong>Status:</strong> {{ $device->device_status }}</p>
                        <p><strong>Main Feed ID:</strong> {{ $device->mainFeed->id ?? 'N/A' }}</p>
                        <p><strong>Parent Device ID:</strong> {{ $device->parent?->id ?? '—' }}</p>
                        <p><strong>Is Parent:</strong> {{ $device->parent_device ? 'Yes' : 'No' }}</p>
                        <div class="mt-4">
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary">Back</a>
                        </div>
                    </div>
                </div>

                @if (!empty($device->parameters))
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold mb-2">Parameters</h3>
                        <pre class="bg-light p-3 rounded">{{ json_encode($device->parameters, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif

                @if ($device->assignedDevices->count())
                    <div class="mt-4">
                        <h3 class="text-lg font-semibold mb-2">Assigned Devices</h3>
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Manufacturer</th>
                                    <th>Model</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($device->assignedDevices as $child)
                                    <tr>
                                        <td>{{ $child->id }}</td>
                                        <td>{{ $child->device_type }}</td>
                                        <td>{{ $child->manufacturer }}</td>
                                        <td>{{ $child->device_model }}</td>
                                        <td>{{ $child->device_status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>


    <script>
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                sessionStorage.setItem('scrollPosition', window.scrollY);
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollPos = sessionStorage.getItem('scrollPosition');
            if (scrollPos !== null) {
                window.scrollTo({
                    top: parseInt(scrollPos),
                    behavior: 'smooth'
                });
                sessionStorage.removeItem('scrollPosition');
            }
        });
    </script>


    {{-- JavaScript Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</x-app-layout>
