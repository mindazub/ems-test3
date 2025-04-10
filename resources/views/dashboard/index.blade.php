<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Dashboard') }}
            </h2>

        </div>
    </x-slot>

    {{-- ✅ CSS CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    {{-- ✅ Custom Theme Styles --}}
    <style>



    </style>

    <div class="py-12">
        <div class="container">

            <div class="row mb-4">
                <div class="col-md-6">
                    <h2>Welcome to the Dashboard!</h2>
                    <p>Here you can manage your projects and plants.</p>
                </div>
                <div class="mb-4 text-right">
                    <a href="{{ route('projects.create') }}"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        + New Project
                    </a>
                </div>
            </div>



            <div class="card">
                <div class="card-body">
                    <h3 class="mb-4">Projects Table</h3>



                    <table id="projectTable" class="table table-striped table-bordered align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Project</th>
                                <th>Company</th>
                                <th>Plants</th>
                                <th>Devices</th>
                                <th>Start date</th>
                                <th>Progress</th>
                                <th>Actions</th>
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
                                <tr>
                                    <td>{{ $project->id }}</td>
                                    <td>{{ $project->name }}</td>
                                    <td>{{ $project->companies_count }}</td>
                                    <td>{{ $project->plants_count }}</td>
                                    <td>{{ $project->devices_count }}</td>
                                    <td>{{ $project->start_date?->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="text-warning">
                                            @for ($s = 1; $s <= 5; $s++)
                                                <i class="bi {{ $s <= $progress ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            @endfor
                                        </div>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <a href="{{ route('projects.show', $project) }}"
                                            class="text-sm text-green-600 hover:text-green-900 mr-2">View</a>
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
                                    <td colspan="7" class="text-center text-muted">No projects found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ JS CDN --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    {{-- ✅ DataTables Init --}}
    <script>
        $(document).ready(function() {
            $('#projectTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries"
                }
            });

            // Dark mode toggle
            const toggle = document.getElementById('themeToggle');
            const isDark = localStorage.getItem('theme') === 'dark';

            if (isDark) {
                document.body.classList.add('dark-mode');
                toggle.checked = true;
            }

            toggle.addEventListener('change', () => {
                if (toggle.checked) {
                    document.body.classList.add('dark-mode');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.body.classList.remove('dark-mode');
                    localStorage.setItem('theme', 'light');
                }
            });
        });
    </script>
</x-app-layout>
