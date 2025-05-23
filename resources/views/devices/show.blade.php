<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Device Details') }} - ID #{{ $device->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">

                <!-- DEVICE INFO SECTION -->
                <div class="mb-6 flex flex-wrap gap-6">
                    <div class="w-full lg:w-1/2 space-y-2">
                        <h1>
                            <span class="text-gray-400 italic">#ID&nbsp;{{ $device->id }}&nbsp;&nbsp;</span>
                            <span class="font-semibold">{{ $device->device_model }}</span> Details
                        </h1>
                        <h2 class="text-lg font-semibold mb-2">General Info</h2>
                        <div class="space-y-1">
                            <p><span class="font-semibold">Type:</span> {{ $device->device_type }}</p>
                            <p><span class="font-semibold">Manufacturer:</span> {{ $device->manufacturer }}</p>
                            <p><span class="font-semibold">Model:</span> {{ $device->device_model }}</p>
                            <p><span class="font-semibold">Status:</span> {{ $device->device_status }}</p>
                            <p><span class="font-semibold">Main Feed ID:</span> {{ $device->mainFeed->id ?? 'N/A' }}</p>
                            <p><span class="font-semibold">Parent Device ID:</span> {{ $device->parent?->id ?? '—' }}</p>
                            <p><span class="font-semibold">Is Parent:</span> {{ $device->parent_device ? 'Yes' : 'No' }}</p>
                            <p><span class="font-semibold">Plant:</span> {{ $device->plant->name ?? '—' }}</p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ url()->previous() }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-2 rounded transition">
                                Back
                            </a>
                        </div>
                    </div>
                </div>

                @if (!empty($device->parameters))
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold mb-2">Parameters</h3>
                        <ul class="divide-y divide-gray-200 border border-gray-200 rounded">
                            @foreach ($device->parameters as $key => $value)
                                <li class="flex flex-col md:flex-row md:justify-between md:items-center px-4 py-2">
                                    <span class="font-semibold text-gray-800">{{ $key }}</span>
                                    <span class="mt-1 md:mt-0 text-gray-600">
                                        @if(is_array($value))
                                            <ul class="ml-3 list-disc list-inside text-sm">
                                                @foreach ($value as $subKey => $subValue)
                                                    <li>
                                                        <span class="font-semibold">{{ $subKey }}:</span>
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
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Manufacturer</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($device->assignedDevices as $child)
                                    <tr class="transition hover:bg-gray-100">
                                        <td class="px-4 py-2">{{ $child->id }}</td>
                                        <td class="px-4 py-2">{{ $child->device_type }}</td>
                                        <td class="px-4 py-2">{{ $child->manufacturer }}</td>
                                        <td class="px-4 py-2">{{ $child->device_model }}</td>
                                        <td class="px-4 py-2">{{ $child->device_status }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
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
</x-app-layout>
