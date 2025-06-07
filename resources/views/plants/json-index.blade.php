<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Plant Data from JSON
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Plant List (from JSON)</h3>
                @php
                    $plants = $data['plants'] ?? (isset($data[0]) ? $data : (isset($data['name']) ? [$data] : []));
                @endphp
                @if (!empty($plants))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th></th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Plant Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Controllers</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">MainFeeds</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Devices</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($plants as $pIndex => $plant)
                                    <tr x-data="{ open: false }">
                                        <td class="text-center">
                                            <button @click="open = !open" class="focus:outline-none">
                                                <span x-show="!open">+</span>
                                                <span x-show="open">-</span>
                                            </button>
                                        </td>
                                        <td class="px-4 py-2 font-semibold">{{ $plant['name'] ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ isset($plant['controllers']) ? count($plant['controllers']) : 0 }}</td>
                                        <td class="px-4 py-2">
                                            {{ isset($plant['controllers']) ? collect($plant['controllers'])->flatMap(fn($c) => $c['mainfeeds'] ?? [])->count() : 0 }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ isset($plant['controllers']) ? collect($plant['controllers'])->flatMap(fn($c) => $c['mainfeeds'] ?? [])->flatMap(fn($f) => $f['devices'] ?? [])->count() : 0 }}
                                        </td>
                                    </tr>
                                    <tr x-show="open" x-transition>
                                        <td colspan="5" class="bg-gray-50">
                                            <div class="p-4">
                                                @if (!empty($plant['controllers']))
                                                    <div class="mb-2 font-semibold text-indigo-700">Controllers</div>
                                                    <ul class="mb-4">
                                                        @foreach ($plant['controllers'] as $cIndex => $controller)
                                                            <li class="mb-2">
                                                                <div class="font-bold">#{{ $cIndex+1 }}: {{ $controller['name'] ?? 'Controller' }}</div>
                                                                @if (!empty($controller['mainfeeds']))
                                                                    <div class="ml-4">
                                                                        <div class="font-semibold text-indigo-600">MainFeeds</div>
                                                                        <ul>
                                                                            @foreach ($controller['mainfeeds'] as $fIndex => $feed)
                                                                                <li class="mb-1">
                                                                                    <div class="font-medium">Feed #{{ $fIndex+1 }}</div>
                                                                                    @if (!empty($feed['devices']))
                                                                                        <div class="ml-4">
                                                                                            <div class="font-semibold text-indigo-500">Devices</div>
                                                                                            <ul class="list-disc ml-6">
                                                                                                @foreach ($feed['devices'] as $device)
                                                                                                    <li>
                                                                                                        <span class="font-mono text-xs">ID: {{ $device['id'] ?? '-' }}</span>
                                                                                                        <span class="ml-2">{{ $device['device_type'] ?? '' }} {{ $device['manufacturer'] ?? '' }} {{ $device['device_model'] ?? '' }} <span class="text-gray-500">{{ $device['device_status'] ?? '' }}</span></span>
                                                                                                    </li>
                                                                                                @endforeach
                                                                                            </ul>
                                                                                        </div>
                                                                                    @endif
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-gray-400">No controllers found for this plant.</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">No plant data found in JSON.</p>
                    <pre class="bg-gray-100 p-4 rounded text-xs overflow-x-auto mt-4">{{ json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
            </div>
        </div>
    </div>
    <script src="//unpkg.com/alpinejs" defer></script>
</x-app-layout>
