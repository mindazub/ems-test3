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
                    <span>You successfully updated your chart preferences.</span>
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
                            
                            @if ($errors->any())
                                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <div class="mb-4">
                                <label for="time_format" class="block font-medium mb-1">Time Format (Timeline/Charts)</label>
                                <select name="time_format" id="time_format" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="updateTimeFormatPreview()">
                                    @php
                                        $currentTimeFormat = $user->getTimeFormat();
                                    @endphp
                                    <option value="24" {{ $currentTimeFormat == '24' ? 'selected' : '' }}>24-hour (e.g., 14:30)</option>
                                    <option value="12" {{ $currentTimeFormat == '12' ? 'selected' : '' }}>12-hour (e.g., 2:30 PM)</option>
                                </select>
                                <p class="text-sm text-gray-600 mt-1">Current setting: <strong id="current-format">{{ $currentTimeFormat == '24' ? '24-hour format' : '12-hour format' }}</strong></p>
                                <p class="text-sm text-blue-600 mt-1" id="preview-text">Preview: <span id="time-preview">{{ $currentTimeFormat == '24' ? '14:30:45' : '2:30:45 PM' }}</span></p>
                            </div>
                            
                            <div class="mb-4 pt-4 border-t border-gray-200">
                                <label for="time_offset" class="block font-medium mb-1">Timeline Offset</label>
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1">
                                        <input type="number" 
                                               name="time_offset" 
                                               id="time_offset" 
                                               min="-23" 
                                               max="23" 
                                               value="{{ $user->getTimeOffset() }}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                               onchange="updateTimeOffsetPreview()">
                                    </div>
                                    <span class="text-sm text-gray-600">hours</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">Shift chart timeline by ± hours (data values stay the same)</p>
                                <p class="text-sm text-green-600 mt-1" id="offset-preview">
                                    @if($user->hasTimeOffset())
                                        Example: 18:00 → {{ (new \Carbon\Carbon('18:00'))->addHours($user->getTimeOffset())->format($currentTimeFormat == '24' ? 'H:i' : 'g:i A') }}
                                    @else
                                        No offset applied
                                    @endif
                                </p>
                            </div>
                            
                            <script>
                                function updateTimeFormatPreview() {
                                    const select = document.getElementById('time_format');
                                    const currentFormat = document.getElementById('current-format');
                                    const timePreview = document.getElementById('time-preview');
                                    const now = new Date();
                                    
                                    if (select.value === '24') {
                                        currentFormat.textContent = '24-hour format';
                                        timePreview.textContent = now.toLocaleTimeString('en-GB', { 
                                            hour12: false, 
                                            hour: '2-digit', 
                                            minute: '2-digit', 
                                            second: '2-digit' 
                                        });
                                    } else {
                                        currentFormat.textContent = '12-hour format';
                                        timePreview.textContent = now.toLocaleTimeString('en-US', { 
                                            hour12: true, 
                                            hour: '2-digit', 
                                            minute: '2-digit', 
                                            second: '2-digit' 
                                        });
                                    }
                                    
                                    // Also update offset preview when time format changes
                                    updateTimeOffsetPreview();
                                }
                                
                                function updateTimeOffsetPreview() {
                                    const offsetInput = document.getElementById('time_offset');
                                    const formatSelect = document.getElementById('time_format');
                                    const offsetPreview = document.getElementById('offset-preview');
                                    
                                    const offset = parseInt(offsetInput.value) || 0;
                                    const is24Hour = formatSelect.value === '24';
                                    
                                    if (offset === 0) {
                                        offsetPreview.textContent = 'No offset applied';
                                        offsetPreview.className = 'text-sm text-gray-600 mt-1';
                                    } else {
                                        // Create a sample time (18:00) and apply offset
                                        const sampleTime = new Date();
                                        sampleTime.setHours(18, 0, 0, 0);
                                        const offsetTime = new Date(sampleTime.getTime() + (offset * 60 * 60 * 1000));
                                        
                                        const originalTimeStr = is24Hour ? '18:00' : '6:00 PM';
                                        const offsetTimeStr = is24Hour ? 
                                            offsetTime.toLocaleTimeString('en-GB', { hour12: false, hour: '2-digit', minute: '2-digit' }) :
                                            offsetTime.toLocaleTimeString('en-US', { hour12: true, hour: 'numeric', minute: '2-digit' });
                                        
                                        offsetPreview.textContent = `Example: ${originalTimeStr} → ${offsetTimeStr}`;
                                        offsetPreview.className = 'text-sm text-green-600 mt-1';
                                    }
                                }
                                
                                // Update preview on page load
                                document.addEventListener('DOMContentLoaded', function() {
                                    updateTimeFormatPreview();
                                    updateTimeOffsetPreview();
                                });
                            </script>
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
                <div class="p-4 sm:p-8 bg-yellow-300 shadow sm:rounded-lg w-full md:w-1/2">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
