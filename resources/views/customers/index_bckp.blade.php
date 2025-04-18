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
                            @forelse ($customers as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->role }}</td>
                                    <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                                    <td>{{ optional($customer->last_login_at)->format('Y-m-d H:i') ?? 'Never' }}</td>
                                    <td>
                                        @php
                                            // Random status for demo (adjust to match real logic if needed)
                                            $statuses = [
                                                'Active' => 'success',
                                                'Suspended' => 'danger',
                                                'Pending' => 'warning text-dark',
                                            ];
                                            $status = collect($statuses)->keys()->random();
                                        @endphp
                                        <span class="badge bg-{{ $statuses[$status] }}">{{ $status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No customers found.</td>
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
