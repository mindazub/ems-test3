<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Device') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white shadow rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('devices.store') }}">
                        @csrf

                        {{-- Main Feed --}}
                        <div class="mb-4">
                            <label for="main_feed_id" class="block text-sm font-medium text-gray-700 mb-1">Main Feed</label>
                            <select name="main_feed_id" id="main_feed_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('main_feed_id') border-red-500 @enderror"
                                    required>
                                <option value="">Select Main Feed</option>
                                @foreach ($mainFeeds as $feed)
                                    <option value="{{ $feed->id }}"
                                        {{ old('main_feed_id') == $feed->id ? 'selected' : '' }}>
                                        ID {{ $feed->id }} (Plant #{{ $feed->plant_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('main_feed_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Parent Device --}}
                        <div class="mb-4">
                            <label for="parent_device_id" class="block text-sm font-medium text-gray-700 mb-1">Parent Device (optional)</label>
                            <select name="parent_device_id" id="parent_device_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('parent_device_id') border-red-500 @enderror">
                                <option value="">None</option>
                                @foreach ($parentDevices as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ old('parent_device_id') == $parent->id ? 'selected' : '' }}>
                                        ID {{ $parent->id }} - {{ $parent->device_type }} ({{ $parent->device_model }})
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_device_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Device Type --}}
                        <div class="mb-4">
                            <label for="device_type" class="block text-sm font-medium text-gray-700 mb-1">Device Type</label>
                            <input type="text" name="device_type" id="device_type"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('device_type') border-red-500 @enderror"
                                   value="{{ old('device_type') }}" required>
                            @error('device_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Manufacturer --}}
                        <div class="mb-4">
                            <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                            <input type="text" name="manufacturer" id="manufacturer"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('manufacturer') border-red-500 @enderror"
                                   value="{{ old('manufacturer') }}" required>
                            @error('manufacturer')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Device Model --}}
                        <div class="mb-4">
                            <label for="device_model" class="block text-sm font-medium text-gray-700 mb-1">Device Model</label>
                            <input type="text" name="device_model" id="device_model"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('device_model') border-red-500 @enderror"
                                   value="{{ old('device_model') }}" required>
                            @error('device_model')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Device Status --}}
                        <div class="mb-4">
                            <label for="device_status" class="block text-sm font-medium text-gray-700 mb-1">Device Status</label>
                            <input type="text" name="device_status" id="device_status"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('device_status') border-red-500 @enderror"
                                   value="{{ old('device_status') }}" required>
                            @error('device_status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Parent Device Toggle (Tailwind Switch) --}}
                        <div class="flex items-center mb-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" value="1" name="parent_device" id="parent_device"
                                       class="sr-only peer"
                                    {{ old('parent_device', true) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-checked:bg-indigo-600 rounded-full peer-focus:ring-4 peer-focus:ring-indigo-300 transition-all"></div>
                                <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>
                            </label>
                            <label for="parent_device" class="ml-3 text-gray-700 text-sm">Is Parent Device?</label>
                        </div>


                        {{-- Parameters --}}
                        <div class="mb-4">
                            <label for="parameters" class="block text-sm font-medium text-gray-700 mb-1">Parameters (JSON)</label>
                            <textarea name="parameters" id="parameters"
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('parameters') border-red-500 @enderror"
                                      rows="4" placeholder='{"key": "value"}'>{{ old('parameters', '{}') }}</textarea>
                            @error('parameters')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-between">
                            <a href="{{ route('devices.index') }}" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded transition">Cancel</a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded transition">Create Device</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
