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
                        <h1><span class="text-muted italic">#ID&nbsp{{ $device->id }}&nbsp&nbsp</span>{{ $device->device_model }} Details</h1>
                        <h2 class="text-lg font-semibold mb-2">General Info</h2>
                        <p><strong>Type:</strong> {{ $device->device_type }}</p>
                        <p><strong>Manufacturer:</strong> {{ $device->manufacturer }}</p>
                        <p><strong>Model:</strong> {{ $device->device_model }}</p>
                        <p><strong>Status:</strong> {{ $device->device_status }}</p>
                        <p><strong>Main Feed ID:</strong> {{ $device->mainFeed->id ?? 'N/A' }}</p>
                        <p><strong>Parent Device ID:</strong> {{ $device->parent?->id ?? '—' }}</p>
                        <p><strong>Is Parent:</strong> {{ $device->parent_device ? 'Yes' : 'No' }}</p>
                        <p><strong>Plant:</strong> {{ $device->plant->name ?? '—' }}</p>
                        <div class="mt-4">
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary">Back</a>
                        </div>
                    </div>
                </div>

                @if (!empty($device->parameters))
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold mb-2">Parameters</h3>
                        <ul class="list-group">
                            @foreach ($device->parameters as $key => $value)
                                <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">{{ $key }}</span>
                                    <span>
                                        @if(is_array($value))
                                            <ul class="mb-0 ps-3">
                                                @foreach ($value as $subKey => $subValue)
                                                    <li>
                                                        <span class="fw-semibold">{{ $subKey }}:</span>
                                                        <span>
                                                            {{ is_array($subValue) ? json_encode($subValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $subValue }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($device->assignedDevices->count())
                    <div class="mt-4">
                        <h3 class="text-lg font-semibold mb-2">Assigned Devices</h3>
                        <table class="table table-sm table-bordered table-hover">
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
                                    <tr class="align-middle">
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
