<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status') === 'settings-updated')
                <div id="settings-success-alert" class="mb-6 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded relative flex items-center justify-between transition-all duration-1000 ease-in-out">
                    <span>You successfully changed time format.</span>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(function() {
                            const alert = document.getElementById('settings-success-alert');
                            if (alert) {
                                alert.style.transition = 'opacity 1.2s cubic-bezier(0.4,0,0.2,1), margin-bottom 1.2s cubic-bezier(0.4,0,0.2,1)';
                                alert.style.opacity = '0';
                                alert.style.marginBottom = '0px';
                                setTimeout(() => alert.remove(), 1300);
                            }
                        }, 2000);
                    });
                </script>
            @endif

            <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg w-full md:w-1/2">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg w-full md:w-1/2">
                    <div class="max-w-xl">
                        <form method="POST" action="{{ route('profile.update.settings') }}">
                            @csrf
                            @method('PUT')
                            <h3 class="text-lg font-semibold mb-2">Preferences</h3>
                            <div class="mb-4">
                                <label for="time_format" class="block font-medium mb-1">Time Format (Timeline/Charts)</label>
                                <select name="time_format" id="time_format" class="form-select rounded border-gray-300">
                                    <option value="24" {{ ($user->settings['time_format'] ?? '24') == '24' ? 'selected' : '' }}>24-hour</option>
                                    <option value="12" {{ ($user->settings['time_format'] ?? '24') == '12' ? 'selected' : '' }}>12-hour (AM/PM)</option>
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Save Preferences</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0 mt-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg w-full md:w-1/2">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
                <div class="p-4 sm:p-8 bg-yellow-50 shadow sm:rounded-lg w-full md:w-1/2">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
