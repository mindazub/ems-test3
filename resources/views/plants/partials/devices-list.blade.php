<div class="mb-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Devices by Controller & Feed</h3>
        @foreach ($plant->controllers as $controller)
            <div class="mb-6 border-2 border-indigo-200 rounded-lg p-4 bg-indigo-50">
                <div class="mb-2 flex items-center justify-between">
                    <h4 class="text-xl font-bold text-indigo-800">Controller #{{ $controller->id }} <span class="text-xs text-gray-500">({{ $controller->name }})</span></h4>
                    <span class="text-xs text-gray-500">UUID: {{ $controller->uuid }}</span>
                </div>
                @foreach ($controller->mainFeeds as $feed)
                    <div class="mb-4 border rounded p-3 bg-white">
                        <div class="flex justify-between items-center mb-2">
                            <h5 class="font-semibold text-indigo-700 mb-0">
                                Main Feed ID: {{ $feed->id }}
                            </h5>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-fixed text-sm border rounded">
                                <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="w-10"></th>
                                    <th class="px-2 py-2 text-left font-semibold">ID</th>
                                    <th class="px-2 py-2 text-left font-semibold">Type</th>
                                    <th class="px-2 py-2 text-left font-semibold">Manufacturer</th>
                                    <th class="px-2 py-2 text-left font-semibold">Model</th>
                                    <th class="px-2 py-2 text-left font-semibold">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($feed->devices->where('parent_device', true) as $parent)
                                        <tbody x-data="{ open: false }" class="bg-gray-100 hover:bg-indigo-100 transition">
                                        <tr>
                                            <td class="text-center">
                                                <button
                                                    type="button"
                                                    class="focus:outline-none"
                                                    @click.stop="open = !open"
                                                    :aria-expanded="open"
                                                    aria-label="Toggle children"
                                                >
                                                    <template x-if="!open">
                                                        <x-heroicon-o-plus-circle class="w-5 h-5 text-indigo-600" />
                                                    </template>
                                                    <template x-if="open">
                                                        <x-heroicon-o-minus-circle class="w-5 h-5 text-indigo-600" />
                                                    </template>
                                                </button>
                                            </td>
                                            <td class="px-2 py-2 cursor-pointer" @click="window.location='{{ url('/devices/'.$parent->id) }}'">{{ $parent->id }}</td>
                                            <td class="px-2 py-2 cursor-pointer" @click="window.location='{{ url('/devices/'.$parent->id) }}'">{{ $parent->device_type }}</td>
                                            <td class="px-2 py-2 cursor-pointer" @click="window.location='{{ url('/devices/'.$parent->id) }}'">{{ $parent->manufacturer }}</td>
                                            <td class="px-2 py-2 cursor-pointer" @click="window.location='{{ url('/devices/'.$parent->id) }}'">{{ $parent->device_model }}</td>
                                            <td class="px-2 py-2 cursor-pointer" @click="window.location='{{ url('/devices/'.$parent->id) }}'">{{ $parent->device_status }}</td>
                                        </tr>
                                        @foreach ($feed->devices->where('parent_device', false)->where('parent_device_id', $parent->id) as $child)
                                            <tr x-show="open" class="bg-indigo-50">
                                                <td></td>
                                                <td class="px-2 py-2 pl-8 cursor-pointer" @click="window.location='{{ url('/devices/'.$child->id) }}'">{{ $child->id }}</td>
                                                <td class="px-2 py-2 cursor-pointer" @click="window.location='{{ url('/devices/'.$child->id) }}'">{{ $child->device_type }}</td>
                                                <td class="px-2 py-2 cursor-pointer" @click="window.location='{{ url('/devices/'.$child->id) }}'">{{ $child->manufacturer }}</td>
                                                <td class="px-2 py-2 cursor-pointer" @click="window.location='{{ url('/devices/'.$child->id) }}'">{{ $child->device_model }}</td>
                                                <td class="px-2 py-2 cursor-pointer" @click="window.location='{{ url('/devices/'.$child->id) }}'">{{ $child->device_status }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
