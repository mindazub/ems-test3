<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Projects') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">

                @if (session()->has('message'))
                    <div class="mb-4 text-green-600 font-medium">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="mb-4 text-right">
                    <a href="{{ route('projects.create') }}"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        + New Project
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border border-gray-200">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 border">Name</th>
                                <th class="px-4 py-2 border">Companies</th>
                                <th class="px-4 py-2 border">Plants</th>
                                <th class="px-4 py-2 border">Devices</th>
                                <th class="px-4 py-2 border">Start Date</th>
                                <th class="px-4 py-2 border">Activity</th>
                                <th class="px-4 py-2 border">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projects as $project)
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
                                <tr class="text-center">
                                    <td class="border px-4 py-2">{{ $project->name }}</td>
                                    <td class="border px-4 py-2">{{ $project->companies_count }}</td>
                                    <td class="border px-4 py-2">{{ $project->plants_count }}</td>
                                    <td class="border px-4 py-2">{{ $project->devices_count }}</td>
                                    <td class="border px-4 py-2">{{ $project->start_date?->format('Y-m-d') }}</td>
                                    <td class="border px-4 py-2 text-yellow-500">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $progress ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </td>
                                    <td class="border px-4 py-2">
                                        <a href="{{ route('projects.edit', $project) }}"
                                            class="text-sm text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                                        <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                            class="inline-block"
                                            onsubmit="return confirm('Are you sure you want to delete this project?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-sm text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="border px-4 py-4 text-center text-gray-500">
                                        No projects found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
