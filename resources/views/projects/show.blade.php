<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Project Details') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Back to Projects</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container">
            <div class="card">
                <div class="card-body">

                    <h3 class="mb-4">{{ $project->name }}</h3>

                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <td>{{ $project->id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $project->name }}</td>
                        </tr>
                        <tr>
                            <th>Start Date</th>
                            <td>{{ $project->start_date?->format('Y-m-d') ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Companies</th>
                            <td>{{ $project->companies_count ?? ($project->companies?->count() ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th>Plants</th>
                            <td>{{ $project->plants_count ?? ($project->plants?->count() ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th>Devices</th>
                            <td>{{ $project->devices_count ?? ($project->devices?->count() ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th>Progress</th>
                            <td>
                                @php
                                    $progress =
                                        (round(
                                            ($project->companies_count > 0 ? 1 : 0) +
                                                ($project->plants_count > 0 ? 1 : 0) +
                                                ($project->devices_count > 0 ? 1 : 0),
                                        ) /
                                            3) *
                                        5;
                                @endphp

                                <div class="text-warning">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <i class="bi {{ $s <= $progress ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div class="mt-4 d-flex">

                        <a href="{{ route('dashboard', $project) }}" class="btn btn-info me-2">
                            <i class="bi bi-arrow-left"></i> Back</a>

                        @auth
                            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary me-2">Edit
                                    Project</a>

                                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this project?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger">Delete Project</button>
                                </form>
                            @endif






                        @endauth


                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap CDN (optional if not already loaded) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</x-app-layout>
