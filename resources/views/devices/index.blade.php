<x-app-layout>


    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Devices') }}
            </h2>
            <a href="{{ route('devices.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Device
            </a>
        </div>
    </x-slot>

    {{-- ✅ Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/scroller/2.2.0/css/scroller.bootstrap5.min.css" rel="stylesheet">

    <div id="page-content" class="py-12">
        <div class="container">
            <div class="card">
                <div class="card-body">

                    {{-- ✅ Success Message --}}
                    @if (session('message'))
                        <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- ✅ Table --}}
                    <h3 class="mb-4">Devices Table</h3>

                    <table id="devicesTable" class="table table-striped table-hover table-bordered align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Manufacturer</th>
                                <th>Model</th>
                                <th>Status</th>
                                <th>Main Feed</th>
                                <th>Parent Device</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($devices as $device)
                                <tr class="clickable-row" data-href="{{ route('devices.show', $device) }}">
                                    <td>{{ $device->id }}</td>
                                    <td>{{ $device->device_type }}</td>
                                    <td>{{ $device->manufacturer }}</td>
                                    <td>{{ $device->device_model }}</td>
                                    <td>{{ $device->device_status }}</td>
                                    <td>{{ $device->mainFeed->id ?? 'N/A' }}</td>
                                    <td>{{ $device->parent?->id ?? '—' }}</td>
                                    <td>
                                        <a href="{{ route('devices.edit', $device) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('devices.destroy', $device) }}" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                                      
                </div>
            </div>
        </div>
    </div>

    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/scroller/2.2.0/js/dataTables.scroller.min.js"></script>

    
    <script>
        $(document).ready(function() {
            $(document).ready(function() {
                $('#devicesTable').DataTable({
                    paging: true,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    info: true,
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        zeroRecords: "No matching devices found",
                    },
                    ordering: true
                });
            });


            // Make rows clickable
            $('#devicesTable').on('click', '.clickable-row', function(e) {
                if ($(e.target).is('a') || $(e.target).is('button') || $(e.target).closest('form').length) {
                    return;
                }
                window.location = $(this).data("href");
            });
        });
    </script>

    {{-- ✅ Styling --}}
    <style>
        .clickable-row {
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }
        .clickable-row:hover {
            background-color: #e7f3ff;
            color: #0c4a6e;
        }
        #success-alert.fade-out {
            opacity: 0;
            transition: opacity 1s ease-out;
        }
    </style>

    {{-- ✅ Success Alert Fade Out --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(() => {
                    alert.classList.add('fade-out');
                    setTimeout(() => alert.remove(), 1000);
                }, 2300);
            }
        });
    </script>



</x-app-layout>


