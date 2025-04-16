<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('User Details') }}
            </h2>
            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-back-transition">
                <i class="bi bi-arrow-left"></i> Back to Customers
            </a>
        </div>
    </x-slot>

    <div id="page-content" class="fade-in py-12">
        <div class="container">
            <div class="card">
                <div class="card-body">

                    <h3 class="mb-4">{{ $user->name }}</h3>

                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <td>{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>{{ ucfirst($user->role) }}</td>
                        </tr>
                        <tr>
                            <th>Registered</th>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <th>Last Logged In</th>
                            <td>{{ $user->last_login_at?->format('Y-m-d H:i') ?? 'Never' }}</td>
                        </tr>
                        <tr>
                            <th>Account Status</th>
                            <td>
                                @php
                                    $status = $user->status ?? 'active'; // fallback
                                @endphp

                                @if ($status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif ($status === 'suspended')
                                    <span class="badge bg-danger">Suspended</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <div class="mt-4 d-flex">
                        @auth
                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary me-2">Edit User</a>

                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger">Delete User</button>
                                </form>
                            @endif
                        @endauth
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Styles and Transitions --}}
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
