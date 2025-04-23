<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('My Plants') }}
            </h2>
        </div>
    </x-slot>

    {{-- ✅ CSS CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <div id="page-content" class="py-12">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="mb-0">Plants Table</h3>
                        <a href="{{ route('plants.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> New Plant
                        </a>
                    </div>

                    <table id="plantsTable" class="table table-striped table-hover table-bordered align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Plant ID</th>
                                <th>Name</th>
                                <th>Owner</th>
                                <th>Main Feeds</th>
                                <th>Devices</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($plants as $plant)
                                <tr class="clickable-row" data-href="{{ route('plants.show', $plant) }}">
                                    <td> {{ $plant->id }}</td>
                                    <td>{{ $plant->name }}</td>
                                    <td>{{ $plant->owner_email }}</td>
                                    <td>{{ $plant->mainFeeds->count() }}</td>
                                    <td>{{ $plant->mainFeeds->flatMap->devices->count() }}</td>
                                    <td>{{ $plant->last_updated ? \Carbon\Carbon::createFromTimestamp($plant->last_updated)->format('Y-m-d') : 'N/A' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('plants.edit', $plant) }}"
                                            class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('plants.destroy', $plant) }}"
                                            class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No plants found.</td>
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
            $('#plantsTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries"
                }
            });

            $('#plantsTable').on('click', '.clickable-row', function(e) {
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
            color: #0c4a6e;
        }

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
</x-app-layout>
