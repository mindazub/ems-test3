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

    <div id="page-content" class="fade-in py-12">
        <div class="container">

            <div class="row mb-4">
                <div class="col-md-6">
                    <h2>Welcome to the Plant List!</h2>
                    <p>Here you can manage your plants.</p>
                </div>

                @auth
                    @if (auth()->user()->role === 'admin')
                        <div class="mb-4 text-right">
                            <a href="{{ route('projects.create') }}"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                + New Project
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="mb-0">Plants Table</h3>

                        @auth
                            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
                                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Create
                                </a>
                            @endif
                        @endauth
                    </div>

                    <table id="projectTable" class="table table-striped table-hover table-bordered align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Project</th>
                                <th>Company</th>
                                <th>Plants</th>
                                <th>Devices</th>

                                @if (in_array(auth()->user()->role, ['admin', 'manager']))
                                    <th>Start date</th>
                                @endif

                                @if (auth()->user()->role === 'admin')
                                    <th>Activity</th>
                                @endif

                                @if (in_array(auth()->user()->role, ['admin', 'manager']))
                                    <th>Actions</th>
                                @endif
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

                                <tr class="clickable-row" data-href="{{ route('projects.show', $project) }}">
                                    <td>{{ $project->id }}</td>
                                    <td>{{ $project->name }}</td>
                                    <td>{{ $project->companies_count }}</td>
                                    <td>{{ $project->plants_count }}</td>
                                    <td>{{ $project->devices_count }}</td>

                                    @if (in_array(auth()->user()->role, ['admin', 'manager']))
                                        <td>{{ $project->start_date?->format('Y-m-d') }}</td>
                                    @endif

                                    @if (auth()->user()->role === 'admin')
                                        <td>
                                            <div class="text-warning">


                                                {{-- Business icons with random badge numbers --}}
                                                @php
                                                    $businessIcons = [
                                                        'briefcase',
                                                        'bar-chart',
                                                        'clipboard-data',
                                                        'graph-up',
                                                        'people',
                                                        'building',
                                                        'gear',
                                                        'shield',
                                                        'check-circle',
                                                        'exclamation-circle',
                                                        'info-circle',
                                                        'question-circle',
                                                        'lightbulb',
                                                        'trophy',
                                                        'calendar',
                                                        'clock',
                                                        'file-earmark-text',
                                                        'file-earmark-check',
                                                        'file-earmark-x',
                                                        'file-earmark-lock',
                                                        'file-earmark-lock2',
                                                    ];
                                                @endphp

                                                @php
                                                    $businessIcons = [
                                                        'briefcase',
                                                        'building',
                                                        'bar-chart',
                                                        'people',
                                                        'graph-up',
                                                        'piggy-bank',
                                                    ];
                                                    $badgeIndexes = collect(range(0, 4))->shuffle()->take(2)->toArray(); // pick 2 unique random indexes
                                                @endphp

                                                @for ($i = 0; $i < 5; $i++)
                                                    @php
                                                        $icon = $businessIcons[array_rand($businessIcons)];
                                                        $randomCount = rand(1, 99);
                                                    @endphp
                                                    <div class="position-relative d-inline-block me-2">
                                                        <i class="bi bi-{{ $icon }} fs-5 text-primary"></i>
                                                        @if (in_array($i, $badgeIndexes))
                                                            <span
                                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                                style="font-size: 0.65rem;">
                                                                {{ $randomCount }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endfor


                                            </div>
                                        </td>
                                    @endif

                                    @if (in_array(auth()->user()->role, ['admin', 'manager']))
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
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No projects found.</td>
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

    <script>
        $(document).ready(function() {
            // Existing DataTable setup...

            // Handle row click
            $('#projectTable').on('click', '.clickable-row', function(e) {
                // Prevent click if the target is inside an action (like a link or button)
                if ($(e.target).is('a') || $(e.target).is('button') || $(e.target).closest('form').length) {
                    return;
                }
                window.location = $(this).data("href");
            });
        });
    </script>

    <style>
        .clickable-row {
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        .clickable-row:hover {
            background-color: #e7f3ff;
            color: #0c4a6e
        }
    </style>


    <style>
        .fade-in {
            opacity: 0;
            transition: opacity 0.4s ease-in;
        }

        .fade-in.show {
            opacity: 1;
        }

        .fade-out {
            opacity: 0;
            transition: opacity 0.4s ease-out;
        }
    </style>






    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const page = document.getElementById("page-content");

            if (page) {
                // Fade in on load
                requestAnimationFrame(() => {
                    page.classList.add("show");
                });

                // Intercept Back button and other links (if needed)
                const backButton = document.querySelector(".btn-back-transition");

                if (backButton) {
                    backButton.addEventListener("click", function(e) {
                        e.preventDefault();
                        page.classList.remove("show");
                        page.classList.add("fade-out");

                        setTimeout(() => {
                            window.location = this.href;
                        }, 400);
                    });
                }
            }
        });
    </script>



</x-app-layout>
