<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Customers') }}
            </h2>
        </div>
    </x-slot>

    {{-- ✅ CSS CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <div id="page-content" class="fade-in py-12">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-4">Customer Accounts</h3>

                    <table id="customersTable" class="table table-striped table-hover table-bordered align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Registered</th>
                                <th>Last Logged In</th>
                                <th>Account Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John Smith</td>
                                <td>john@example.com</td>
                                <td>customer</td>
                                <td>2024-12-05</td>
                                <td>2025-04-15 10:12</td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                            <tr>
                                <td>Emily Johnson</td>
                                <td>emily@example.com</td>
                                <td>customer</td>
                                <td>2025-01-22</td>
                                <td>2025-04-13 15:50</td>
                                <td><span class="badge bg-danger">Suspended</span></td>
                            </tr>
                            <tr>
                                <td>Mark Davis</td>
                                <td>mark@example.com</td>
                                <td>installer</td>
                                <td>2023-11-01</td>
                                <td>2025-04-10 08:27</td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                            <tr>
                                <td>Linda Taylor</td>
                                <td>linda@example.com</td>
                                <td>manager</td>
                                <td>2022-06-17</td>
                                <td>2025-04-14 21:30</td>
                                <td><span class="badge bg-warning text-dark">Pending</span></td>
                            </tr>
                            <tr>
                                <td>Admin User</td>
                                <td>admin@admin.com</td>
                                <td>admin</td>
                                <td>2021-01-01</td>
                                <td>2025-04-15 09:00</td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
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
            $('#customersTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries"
                }
            });
        });
    </script>

    {{-- ✅ Transitions --}}
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
                requestAnimationFrame(() => {
                    page.classList.add("show");
                });
            }
        });
    </script>
</x-app-layout>
