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
        body.dark-mode {
            background-color: #121212;
            color: #fff;
        }

        .dark-mode .card {
            background-color: #1e1e1e;
            color: #fff;
        }

        .dark-mode .form-check-label {
            color: #fff;
        }

        .dark-mode table.dataTable {
            color: #ccc;
        }

        .dark-mode .dataTables_wrapper .dataTables_filter input,
        .dark-mode .dataTables_wrapper .dataTables_length select {
            background-color: #2a2a2a;
            color: #fff;
            border: 1px solid #444;
        }

        .dark-mode .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #2c2c2c;
        }
    </style>

    <div class="py-12">
        <div class="container">

            <div class="row mb-4">
                <div class="col-md-6">
                    <h2>Welcome to the Dashboard!</h2>
                    <p>Here you can manage your projects and plants.</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="btn btn-primary">Create New Project</a>
                </div>
            </div>



            <div class="card">
                <div class="card-body">
                    <h3 class="mb-4">Projects Table</h3>


                                                        {{-- Theme Toggle Switch --}}
            {{-- <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="themeToggle">
                <label class="form-check-label" for="themeToggle">Dark Mode</label>
            </div> --}}

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
                            </tr>


                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 57; $i++)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>Project {{ $i }}</td>
                                    <td>Company {{ rand(1, 5) }}</td>
                                    <td>{{ ['New York', 'London', 'San Francisco', 'Tokyo', 'Edinburgh'][rand(0, 4)] }}</td>
                                    <td>{{ rand(22, 65) }}</td>
                                    <td>{{ \Carbon\Carbon::now()->subDays(rand(1000, 5000))->format('Y-m-d') }}</td>
                                    <td>${{ number_format(rand(60000, 250000), 0, '.', ',') }}</td>
                                </tr>
                            @endfor
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
        $(document).ready(function () {
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
