<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Device') }}
        </h2>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="py-6">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('devices.store') }}">
                        @csrf

                        {{-- Main Feed --}}
                        <div class="mb-3">
                            <label for="main_feed_id" class="form-label">Main Feed</label>
                            <select name="main_feed_id" id="main_feed_id"
                                class="form-select @error('main_feed_id') is-invalid @enderror" required>
                                <option value="">Select Main Feed</option>
                                @foreach ($mainFeeds as $feed)
                                    <option value="{{ $feed->id }}"
                                        {{ old('main_feed_id') == $feed->id ? 'selected' : '' }}>
                                        ID {{ $feed->id }} (Plant #{{ $feed->plant_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('main_feed_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Parent Device --}}
                        <div class="mb-3">
                            <label for="parent_device_id" class="form-label">Parent Device (optional)</label>
                            <select name="parent_device_id" id="parent_device_id"
                                class="form-select @error('parent_device_id') is-invalid @enderror">
                                <option value="">None</option>
                                @foreach ($parentDevices as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ old('parent_device_id') == $parent->id ? 'selected' : '' }}>
                                        ID {{ $parent->id }} - {{ $parent->device_type }}
                                        ({{ $parent->device_model }})
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_device_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Device Type --}}
                        <div class="mb-3">
                            <label for="device_type" class="form-label">Device Type</label>
                            <input type="text" name="device_type" id="device_type"
                                class="form-control @error('device_type') is-invalid @enderror"
                                value="{{ old('device_type') }}" required>
                            @error('device_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Manufacturer --}}
                        <div class="mb-3">
                            <label for="manufacturer" class="form-label">Manufacturer</label>
                            <input type="text" name="manufacturer" id="manufacturer"
                                class="form-control @error('manufacturer') is-invalid @enderror"
                                value="{{ old('manufacturer') }}" required>
                            @error('manufacturer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Device Model --}}
                        <div class="mb-3">
                            <label for="device_model" class="form-label">Device Model</label>
                            <input type="text" name="device_model" id="device_model"
                                class="form-control @error('device_model') is-invalid @enderror"
                                value="{{ old('device_model') }}" required>
                            @error('device_model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Device Status --}}
                        <div class="mb-3">
                            <label for="device_status" class="form-label">Device Status</label>
                            <input type="text" name="device_status" id="device_status"
                                class="form-control @error('device_status') is-invalid @enderror"
                                value="{{ old('device_status') }}" required>
                            @error('device_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Parent Device Toggle --}}
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="parent_device"
                                name="parent_device" {{ old('parent_device', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="parent_device">Is Parent Device?</label>
                        </div>





                        {{-- Parameters --}}
                        <div class="mb-3">
                            <label for="parameters" class="form-label">Parameters (JSON)</label>
                            <textarea name="parameters" id="parameters" class="form-control @error('parameters') is-invalid @enderror"
                                rows="4" placeholder='{"key": "value"}'>{{ old('parameters', '{}') }}</textarea>
                            @error('parameters')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('devices.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Device</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</x-app-layout>
