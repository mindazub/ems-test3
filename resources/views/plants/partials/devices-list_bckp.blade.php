<div class="mb-6">
    <div class="bg-white rounded-lg shadow mb-5">
        <div class="px-6 pt-6">
            <h3 class="text-lg font-semibold mb-4">Devices by Feed</h3>
        </div>

        <div class="px-6 pb-6">
            @foreach ($plant->mainFeeds as $feed)
                <div class="mb-8 border rounded p-4">
                    <div class="flex justify-between items-center mb-2">
                        <h5 class="font-semibold text-indigo-700 mb-0">
                            Main&nbsp;Feed&nbsp;ID:&nbsp;{{ $feed->id }}
                        </h5>
                        <button onclick="window.print()" class="inline-flex items-center text-xs bg-green-100 text-green-800 px-3 py-1 rounded hover:bg-green-200 transition">
                            <x-heroicon-o-printer class="w-4 h-4 mr-1" /> Export PDF
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-fixed text-sm border rounded">
                            <colgroup>
                                <col style="width:44px">
                                <col style="width:10%">
                                <col style="width:18%">
                                <col style="width:27%">
                                <col style="width:27%">
                                <col style="width:27%">
                            </colgroup>
                            <thead class="bg-gray-50 border-b">
                            <tr>
                                <th></th>
                                <th class="px-2 py-2 text-left font-semibold">ID</th>
                                <th class="px-2 py-2 text-left font-semibold">Type</th>
                                <th class="px-2 py-2 text-left font-semibold">Manufacturer</th>
                                <th class="px-2 py-2 text-left font-semibold">Model</th>
                                <th class="px-2 py-2 text-left font-semibold">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($feed->devices->where('parent_device', true) as $parent)
                                <tbody x-data="{ open: false }" class="bg-gray-100 hover:bg-indigo-50 transition">
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
                                    <td class="px-2 py-2 cursor-pointer hover:underline" @click="window.location='{{ url('/devices/'.$parent->id) }}'">{{ $parent->id }}</td>
                                    <td class="px-2 py-2 cursor-pointer hover:underline" @click="window.location='{{ url('/devices/'.$parent->id) }}'">{{ $parent->device_type }}</td>
                                    <td class="px-2 py-2 cursor-pointer hover:underline" @click="window.location='{{ url('/devices/'.$parent->id) }}'">{{ $parent->manufacturer }}</td>
                                    <td class="px-2 py-2 cursor-pointer hover:underline" @click="window.location='{{ url('/devices/'.$parent->id) }}'">{{ $parent->device_model }}</td>
                                    <td class="px-2 py-2 cursor-pointer hover:underline" @click="window.location='{{ url('/devices/'.$parent->id) }}'">{{ $parent->device_status }}</td>
                                </tr>
                                @foreach (
                                    $feed->devices
                                        ->where('parent_device', false)
                                        ->where('main_feed_id', $parent->main_feed_id) as $child
                                )
                                    <tr
                                        x-show="open"
                                        x-transition
                                        style="display: none;"
                                        class="bg-white hover:bg-gray-50 cursor-pointer"
                                        @click="window.location='{{ url('/devices/'.$child->id) }}'">
                                        <td class="text-center">
                                            <x-heroicon-o-arrow-right class="w-4 h-4 text-gray-400 ml-2" />
                                        </td>
                                        <td class="px-2 py-2">{{ $child->id }}</td>
                                        <td class="px-2 py-2">{{ $child->device_type }}</td>
                                        <td class="px-2 py-2">{{ $child->manufacturer }}</td>
                                        <td class="px-2 py-2">{{ $child->device_model }}</td>
                                        <td class="px-2 py-2">{{ $child->device_status }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                @endforeach
                                </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <div class="mt-4">
                <a href="{{ route('plants.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-medium rounded transition">
                    Back to Plants
                </a>
            </div>
        </div>
    </div>
</div>
