<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Plants (from JSON)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Plants Table (JSON)</h3>
                @if (!empty($plants))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Plant Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Controllers</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">MainFeeds</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Devices</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($plants as $plant)
                                    <tr>
                                        <td class="px-4 py-2 font-semibold">{{ $plant['name'] ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ isset($plant['controllers']) ? count($plant['controllers']) : 0 }}</td>
                                        <td class="px-4 py-2">
                                            {{ isset($plant['controllers']) ? collect($plant['controllers'])->flatMap(fn($c) => $c['mainfeeds'] ?? [])->count() : 0 }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ isset($plant['controllers']) ? collect($plant['controllers'])->flatMap(fn($c) => $c['mainfeeds'] ?? [])->flatMap(fn($f) => $f['devices'] ?? [])->count() : 0 }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">No plant data found in JSON.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
