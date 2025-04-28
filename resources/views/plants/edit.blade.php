<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Edit Plant') }}
        </h2>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="py-6">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    {{-- Success message --}}
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('plants.update', $plant) }}">
                        @csrf
                        @method('PUT')

                        {{-- Name --}}
                        <div class="mb-3">
                            <label class="form-label">Plant Name</label>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $plant->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Owner Email --}}
                        <div class="mb-3">
                            <label class="form-label">Owner Email</label>
                            <input type="email" name="owner_email"
                                class="form-control @error('owner_email') is-invalid @enderror"
                                value="{{ old('owner_email', $plant->owner_email) }}" required>
                            @error('owner_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <input type="text" name="status"
                                class="form-control @error('status') is-invalid @enderror"
                                value="{{ old('status', $plant->status) }}" required>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Capacity --}}
                        <div class="mb-3">
                            <label class="form-label">Capacity (W)</label>
                            <input type="number" name="capacity"
                                class="form-control @error('capacity') is-invalid @enderror"
                                value="{{ old('capacity', $plant->capacity) }}" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Latitude --}}
                        <div class="mb-3">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="number" name="latitude" id="latitude" step="0.00001" min="-90"
                                max="90" class="form-control @error('latitude') is-invalid @enderror"
                                value="{{ old('latitude') }}" required>
                            @error('latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Format: two digits before the dot, five digits after (e.g. 54.93381)
                            </div>
                        </div>

                        {{-- Longitude --}}
                        <div class="mb-3">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="number" name="longitude" id="longitude" step="0.00001" min="-180"
                                max="180" class="form-control @error('longitude') is-invalid @enderror"
                                value="{{ old('longitude') }}" required>
                            @error('longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Format: two digits before the dot, five digits after (e.g. 23.86181)
                            </div>
                        </div>

                        {{-- Last Updated --}}
                        <div class="mb-3">
                            <label class="form-label">Last Updated</label>
                            <input type="datetime-local" name="last_updated"
                                class="form-control @error('last_updated') is-invalid @enderror"
                                value="{{ old('last_updated', $plant->last_updated ? \Carbon\Carbon::createFromTimestamp($plant->last_updated)->format('Y-m-d\TH:i') : '') }}">
                            @error('last_updated')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('plants.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Plant</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
