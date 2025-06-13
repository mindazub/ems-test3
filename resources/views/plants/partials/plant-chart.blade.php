<!-- Calendar controls with complete day selection -->
<div class="flex flex-col items-center justify-center mb-4 gap-2" 
    id="energy-calendar-controls" 
    data-plant-id="{{ $plant->uid ?? $plant->uuid ?? $plant->id ?? '' }}">
    <h3 class="text-lg font-semibold text-gray-700 mb-1">Select Date to View</h3>
    <div class="flex flex-col md:flex-row items-center gap-3">
        <div class="flex items-center gap-2 p-1 bg-gray-50 border rounded-lg">
            <button id="energy-prev" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" title="Previous day">&#8592;</button>
            <input type="date" id="energy-date" class="border rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <button id="energy-next" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" title="Next day">&#8594;</button>
        </div>
    </div>
    

    
    <div id="loading-indicator" class="hidden mt-2">
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700">Loading data...</span>
        </div>
    </div>
</div>



<div class="mb-6 space-y-8">
    <!-- Energy Chart Tabs -->
    <div x-data="{ tabEnergy: 'graph', open: false }" class="bg-white rounded-lg shadow">
        <div class="border-b px-4 pt-4 flex items-center">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button
                    :class="tabEnergy === 'graph' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tabEnergy = 'graph'">
                    Graph
                </button>
                <button
                    :class="tabEnergy === 'data' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tabEnergy = 'data'">
                    Data
                </button>
            </nav>
            <!-- Dropdown, aligned right -->
            <div class="ml-auto relative" x-data="{ openMenu: false }">
                <button @click="openMenu = !openMenu"
                        class="p-1 rounded hover:bg-gray-100 transition border border-gray-200"
                        aria-label="Download">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-gray-500" />
                </button>
                <div x-show="openMenu" @click.away="openMenu = false"
                     class="absolute right-0 mt-2 w-40 bg-white rounded shadow border z-50 text-sm"
                     style="display: none;">
                     <a id="downloadPNG-energy" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer">Download PNG</a>
                     @if(!empty($plant->uid))
                         <a id="downloadCSV-energy" class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->uid, 'energy', 'csv']) }}">Download CSV</a>
                     @else
                         <span class="block px-4 py-2 text-gray-400 cursor-not-allowed" title="Plant ID missing">Download CSV</span>
                     @endif
                     <a id="downloadPDF-energy" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer">Download PDF</a>

                </div>
            </div>
        </div>

        <div class="px-4 py-4 min-h-[550px]">
            <!-- Graph Tab -->
            <div x-show="tabEnergy === 'graph'">
                <h4 class="text-center mb-5 font-bold text-3xl">Energy Live Chart</h4>
                <div class="w-full" style="height: 600px;">
                    <canvas id="energyChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <!-- Data Tab -->
            <div x-show="tabEnergy === 'data'">
                <div class="overflow-x-auto" style="height: 650px;">
                    <table class="w-full text-sm border rounded">
                        <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-center">Time</th>
                            <th class="px-4 py-2 text-center">PV (kW)</th>
                            <th class="px-4 py-2 text-center">Battery (kW)</th>
                            <th class="px-4 py-2 text-center">Grid (kW)</th>
                        </tr>
                        </thead>
                        <tbody id="energyDataTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Battery Chart Tabs -->
    <div x-data="{ tabBattery: 'graph', open: false }" class="bg-white rounded-lg shadow">
        <div class="border-b px-4 pt-4 flex items-center">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button
                    :class="tabBattery === 'graph' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tabBattery = 'graph'">
                    Graph
                </button>
                <button
                    :class="tabBattery === 'data' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tabBattery = 'data'">
                    Data
                </button>
            </nav>
            <!-- Dropdown -->
            <div class="ml-auto relative" x-data="{ openMenu: false }">
                <button @click="openMenu = !openMenu"
                        class="p-1 rounded hover:bg-gray-100 transition border border-gray-200"
                        aria-label="Download">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-gray-500" />
                </button>
                <div x-show="openMenu" @click.away="openMenu = false"
                     class="absolute right-0 mt-2 w-40 bg-white rounded shadow border z-50 text-sm"
                     style="display: none;">
                     <a id="downloadPNG-battery" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer">Download PNG</a>
                     @if(!empty($plant->uid))
                         <a id="downloadCSV-battery" class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->uid, 'battery', 'csv']) }}">Download CSV</a>
                     @else
                         <span class="block px-4 py-2 text-gray-400 cursor-not-allowed" title="Plant ID missing">Download CSV</span>
                     @endif
                     <a id="downloadPDF-battery" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer">Download PDF</a>
                </div>
            </div>
        </div>

        <div class="px-4 py-4 min-h-[550px]">
            <!-- Battery Chart Tab -->
            <div x-show="tabBattery === 'graph'">
                <h4 class="text-center mb-5 font-bold text-3xl">Battery Power and Energy Price</h4>
                <div class="w-full" style="height: 600px;">
                    <canvas id="batteryChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <!-- Data Tab -->
            <div x-show="tabBattery === 'data'">
                <div class="overflow-x-auto" style="height: 650px;">
                    <table class="w-full text-sm border rounded">
                        <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-center">Time</th>
                            <th class="px-4 py-2 text-center">Battery Power (kW)</th>
                            <th class="px-4 py-2 text-center">Energy Price (€ / kWh)</th>
                            <th class="px-4 py-2 text-center">API Price (€ / kWh)</th>
                        </tr>
                        </thead>
                        <tbody id="batteryDataTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Battery Savings Chart Tabs -->
    <div x-data="{ tabSavings: 'graph', open: false }" class="bg-white rounded-lg shadow">
        <div class="border-b px-4 pt-4 flex items-center">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button
                    :class="tabSavings === 'graph' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tabSavings = 'graph'">
                    Graph
                </button>
                <button
                    :class="tabSavings === 'data' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-indigo-600'"
                    class="px-3 py-2 text-sm font-medium focus:outline-none"
                    @click="tabSavings = 'data'">
                    Data
                </button>
            </nav>
            <!-- Dropdown -->
            <div class="ml-auto relative" x-data="{ openMenu: false }">
                <button @click="openMenu = !openMenu"
                        class="p-1 rounded hover:bg-gray-100 transition border border-gray-200"
                        aria-label="Download">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-gray-500" />
                </button>
                <div x-show="openMenu" @click.away="openMenu = false"
                     class="absolute right-0 mt-2 w-40 bg-white rounded shadow border z-50 text-sm"
                     style="display: none;">
                     <a id="downloadPNG-savings" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer">Download PNG</a>
                     @if(!empty($plant->uid))
                         <a id="downloadCSV-savings" class="block px-4 py-2 hover:bg-gray-50" href="{{ route('plants.download', [$plant->uid, 'savings', 'csv']) }}">Download CSV</a>
                     @else
                         <span class="block px-4 py-2 text-gray-400 cursor-not-allowed" title="Plant ID missing">Download CSV</span>
                     @endif
                     <a id="downloadPDF-savings" class="block px-4 py-2 hover:bg-gray-50 cursor-pointer">Download PDF</a>
                </div>
            </div>
        </div>
        <div class="px-4 py-4 min-h-[550px]">
            <!-- Graph Tab -->
            <div x-show="tabSavings === 'graph'">
                <h4 class="text-center mb-5 font-bold text-3xl">Battery Savings</h4>
                <p id="batterySavingsTotal" class="text-center text-green-700 font-bold"></p>
                <div class="w-full" style="height: 600px;">
                    <canvas id="savingsChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <!-- Data Tab -->
            <div x-show="tabSavings === 'data'">
                <div class="overflow-x-auto" style="height: 670px;">
                    <table class="w-full text-sm border rounded">
                        <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-center">Time</th>
                            <th class="px-4 py-2 text-center">Battery Savings (€)</th>
                            <th class="px-4 py-2 text-center">API Price (€ / kWh)</th>
                        </tr>
                        </thead>
                        <tbody id="batterySavingsDataTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js loader -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Alpine.js loader (required for x-data, if not already loaded elsewhere) -->
<script src="//unpkg.com/alpinejs" defer></script>

<script>
// Vertical crosshair line plugin for Chart.js (applies to all charts)
const verticalLinePlugin = {
    id: 'verticalLine',
    afterDraw(chart) {
        if (chart.tooltip?._active && chart.tooltip._active.length && chart.chartArea) {
            const ctx = chart.ctx;
            const x = chart.tooltip._active[0].element.x;
            const topY = chart.chartArea.top;
            const bottomY = chart.chartArea.bottom;
            ctx.save();
            ctx.beginPath();
            ctx.moveTo(x, topY);
            ctx.lineTo(x, bottomY);
            ctx.lineWidth = 3;
            ctx.strokeStyle = 'rgba(90,90,220,0.4)';
            ctx.setLineDash([6, 6]);
            ctx.stroke();
            ctx.restore();
        }
    }
};
Chart.register(verticalLinePlugin);

// Show notification message to user
function showNotification(message, type = 'info') {
    let notificationContainer = document.getElementById('chart-notifications');
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'chart-notifications';
        notificationContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
        document.body.appendChild(notificationContainer);
    }
    
    const notification = document.createElement('div');
    notification.className = 'p-4 rounded-md shadow-md transform transition-all duration-300 max-w-sm ' + 
        (type === 'error' ? 'bg-red-50 border-l-4 border-red-500 text-red-700' : 
         type === 'success' ? 'bg-green-50 border-l-4 border-green-500 text-green-700' : 
         'bg-blue-50 border-l-4 border-blue-500 text-blue-700');
    
    notification.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex-1">${message}</div>
            <button class="ml-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    `;
    
    notification.querySelector('button').addEventListener('click', () => {
        notification.classList.add('opacity-0', 'scale-95');
        setTimeout(() => notification.remove(), 300);
    });
    
    notificationContainer.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('opacity-0', 'scale-95');
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
    
    console[type === 'error' ? 'error' : type === 'success' ? 'info' : 'log'](`[Notification] ${message}`);
}

// Format date for chart labels with time offset
function formatLabelDate(dateString) {
    try {
        let date;
        if (typeof dateString === 'number' || /^\d+$/.test(dateString)) {
            date = new Date(parseInt(dateString) * 1000);
        } else {
            date = new Date(dateString);
        }
        
        if (isNaN(date.getTime())) {
            console.log('Invalid date encountered:', dateString);
            return dateString;
        }
        
        // Apply user's time offset (visual shift only)
        const offsetHours = window.userTimeOffset || 0;
        if (offsetHours !== 0) {
            date = new Date(date.getTime() + (offsetHours * 60 * 60 * 1000));
        }
        
        // Use user's time format preference
        const use12Hour = window.userTimeFormat === '12';
        const options = { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: use12Hour
        };
        
        return date.toLocaleTimeString([], options);
    } catch (e) {
        console.log('Error formatting date:', e, dateString);
        return dateString;
    }
}

// Format time string (HH:MM) for chart axis labels according to user preference
function formatChartTimeLabel(timeString) {
    try {
        // Parse time string like "14:30" or "08:00"
        const [hours, minutes] = timeString.split(':').map(Number);
        
        if (isNaN(hours) || isNaN(minutes)) {
            return timeString;
        }
        
        // Create a date object for formatting (use today's date)
        const date = new Date();
        date.setHours(hours, minutes, 0, 0);
        
        // Use user's time format preference
        const use12Hour = window.userTimeFormat === '12';
        const options = { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: use12Hour
        };
        
        return date.toLocaleTimeString([], options);
    } catch (e) {
        console.log('Error formatting chart time label:', e, timeString);
        return timeString;
    }
}

// Global variables for chart data and instances
window.energyData = {};
window.batteryPriceData = {};
window.batterySavingsData = {};
window.chartInstances = {};
window.availableDates = new Set(); // Store available data dates

// Enhanced Plant ID detection with multiple fallback methods
function detectPlantId() {
    let plantId = null;
    
    // Method 1: Backend JSON data
    try {
        plantId = @json($plant->uid ?? $plant->uuid ?? $plant->id ?? null);
        if (plantId) {
            console.log('✓ Plant ID from backend JSON:', plantId);
            return plantId;
        }
    } catch (e) {
        console.log('Backend JSON method failed:', e);
    }
    
    // Method 2: URL path parsing
    try {
        const pathSegments = window.location.pathname.split('/');
        console.log('URL path segments:', pathSegments);
        
        // Look for /plants/{id} pattern
        const plantsIndex = pathSegments.indexOf('plants');
        if (plantsIndex !== -1 && plantsIndex + 1 < pathSegments.length) {
            plantId = pathSegments[plantsIndex + 1];
            if (plantId && /^[a-zA-Z0-9-_]+$/.test(plantId)) {
                console.log('✓ Plant ID from URL path:', plantId);
                return plantId;
            }
        }
    } catch (e) {
        console.log('URL parsing method failed:', e);
    }
    
    // Method 3: Data attribute
    try {
        const calendarControls = document.getElementById('energy-calendar-controls');
        if (calendarControls && calendarControls.dataset.plantId) {
            plantId = calendarControls.dataset.plantId;
            console.log('✓ Plant ID from data attribute:', plantId);
            return plantId;
        }
    } catch (e) {
        console.log('Data attribute method failed:', e);
    }
    
    // Method 4: Search all elements with data-plant-id
    try {
        const elementsWithPlantId = document.querySelectorAll('[data-plant-id]');
        for (const element of elementsWithPlantId) {
            if (element.dataset.plantId) {
                plantId = element.dataset.plantId;
                console.log('✓ Plant ID from element search:', plantId);
                return plantId;
            }
        }
    } catch (e) {
        console.log('Element search method failed:', e);
    }
    
    return null;
}

// Initialize Plant ID and user preferences
window.plantId = detectPlantId();
window.userTimeFormat = @json($user ? $user->getTimeFormat() : '24');
window.userTimeOffset = @json($user ? $user->getTimeOffset() : 0);

console.log('=== INITIALIZATION ===');
console.log('Plant ID detected:', window.plantId);
console.log('User time format:', window.userTimeFormat);
console.log('User time offset:', window.userTimeOffset, 'hours');

if (!window.plantId) {
    console.error('❌ CRITICAL: Plant ID detection failed completely!');
    showNotification('Error: Plant ID is missing. Charts may not work correctly.', 'error');
}

// Function to fetch available data dates from backend
async function fetchAvailableDates() {
    if (!window.plantId) {
        console.error('Cannot fetch available dates: Plant ID is missing');
        return;
    }
    
    try {
        console.log('Fetching available dates...');
        const response = await fetch(`/plants/${window.plantId}/available-dates`, {
            credentials: 'same-origin',
            cache: 'no-cache'
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.dates && Array.isArray(data.dates)) {
                window.availableDates = new Set(data.dates);
                console.log('Available dates loaded:', Array.from(window.availableDates));
                updateDateInputConstraints();
                updateNavigationButtons();
                return;
            }
        }
        
        // Fallback: if endpoint doesn't exist, check a range of recent dates
        console.log('Available dates endpoint not found, using fallback method...');
        await checkDateRangeFallback();
        
    } catch (error) {
        console.log('Error fetching available dates, using fallback:', error);
        await checkDateRangeFallback();
    }
}

// Fallback method: check recent dates for data availability
async function checkDateRangeFallback() {
    const today = new Date();
    const startDate = new Date(today);
    startDate.setDate(today.getDate() - 45); // Check last 45 days for better performance
    
    const availableDates = new Set();
    const dateList = [];
    
    // Build list of dates to check
    for (let d = new Date(startDate); d <= today; d.setDate(d.getDate() + 1)) {
        const dateStr = d.toISOString().split('T')[0];
        dateList.push(dateStr);
    }
    
    // Check dates in batches of 5 to avoid overwhelming the API
    const batchSize = 5;
    for (let i = 0; i < dateList.length; i += batchSize) {
        const batch = dateList.slice(i, i + batchSize);
        const batchPromises = batch.map(dateStr => checkDateHasData(dateStr));
        
        try {
            const results = await Promise.all(batchPromises);
            results.forEach((result, index) => {
                if (result.hasData && result.dataPoints >= 3) {
                    availableDates.add(batch[index]);
                }
            });
            
            // Small delay between batches to be nice to the API
            if (i + batchSize < dateList.length) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }
        } catch (error) {
            console.error('Error in batch checking:', error);
            // Continue with next batch even if this one fails
        }
    }
    
    window.availableDates = availableDates;
    console.log('Available dates (fallback):', Array.from(window.availableDates).slice(-10)); // Show last 10
    updateDateInputConstraints();
    updateNavigationButtons();
}

// Check if a specific date has data
async function checkDateHasData(dateStr) {
    try {
        const [year, month, day] = dateStr.split('-');
        const selectedDate = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
        
        const utcMidnight = Date.UTC(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate(), 0, 0, 0, 0);
        const eestMidnightUTC = utcMidnight - (3 * 60 * 60 * 1000);
        const startOfDay = Math.floor(eestMidnightUTC / 1000);
        const endOfDay = startOfDay + (24 * 60 * 60) - 1;
        
        const response = await fetch(`/plants/${window.plantId}/data?start=${startOfDay}&end=${endOfDay}&check_only=1`, {
            credentials: 'same-origin',
            cache: 'no-cache'
        });
        
        if (response.ok) {
            const data = await response.json();
            
            // Check if we have meaningful data (at least 3 data points)
            const energyDataCount = data.energy_chart ? Object.keys(data.energy_chart).length : 0;
            const batteryDataCount = data.battery_price ? Object.keys(data.battery_price).length : 0;
            const savingsDataCount = data.battery_savings ? Object.keys(data.battery_savings).length : 0;
            
            const totalDataPoints = energyDataCount + batteryDataCount + savingsDataCount;
            const hasData = totalDataPoints >= 3; // Require at least 3 data points
            
            return { hasData, dataPoints: totalDataPoints };
        }
        
        return { hasData: false, dataPoints: 0 };
    } catch (error) {
        console.log('Error checking date:', dateStr, error);
        return { hasData: false, dataPoints: 0 };
    }
}

// Update date input constraints based on available dates
function updateDateInputConstraints() {
    const dateInput = document.getElementById('energy-date');
    if (!dateInput) return;
    
    // Add custom validation styles for unavailable dates
    if (!document.getElementById('date-constraint-styles')) {
        const style = document.createElement('style');
        style.id = 'date-constraint-styles';
        style.textContent = `
            input[type="date"]::-webkit-calendar-picker-indicator {
                filter: opacity(0.8);
            }
            input[type="date"]:invalid {
                border-color: #ef4444;
                box-shadow: 0 0 0 1px #ef4444;
            }
            .date-input-disabled {
                opacity: 0.6;
                background-color: #f3f4f6;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Set min and max dates based on available data
    if (window.availableDates.size > 0) {
        const sortedDates = Array.from(window.availableDates).sort();
        const earliestDate = sortedDates[0];
        const latestDate = sortedDates[sortedDates.length - 1];
        
        // Set hard limits
        dateInput.min = earliestDate;
        dateInput.max = latestDate;
        
        console.log('Date constraints updated:', {
            min: earliestDate, 
            max: latestDate,
            availableCount: sortedDates.length
        });
        
        // Add custom validation
        dateInput.addEventListener('input', function() {
            const selectedDate = this.value;
            if (selectedDate && !window.availableDates.has(selectedDate)) {
                this.setCustomValidity('No data available for this date');
                this.classList.add('date-input-disabled');
            } else {
                this.setCustomValidity('');
                this.classList.remove('date-input-disabled');
            }
        });
    }
}

// Update navigation button states
function updateNavigationButtons() {
    const dateInput = document.getElementById('energy-date');
    const prevButton = document.getElementById('energy-prev');
    const nextButton = document.getElementById('energy-next');
    
    if (!dateInput || !prevButton || !nextButton) return;
    
    const currentDate = dateInput.value;
    if (!currentDate) return;
    
    // Check if previous date has data
    const prevDate = new Date(currentDate);
    prevDate.setDate(prevDate.getDate() - 1);
    const prevDateStr = prevDate.toISOString().split('T')[0];
    const hasPrevData = window.availableDates.has(prevDateStr);
    
    // Check if next date has data
    const nextDate = new Date(currentDate);
    nextDate.setDate(nextDate.getDate() + 1);
    const nextDateStr = nextDate.toISOString().split('T')[0];
    const hasNextData = window.availableDates.has(nextDateStr);
    
    // Update button states
    prevButton.disabled = !hasPrevData;
    nextButton.disabled = !hasNextData;
    
    // Update button appearance
    if (hasPrevData) {
        prevButton.classList.remove('opacity-50', 'cursor-not-allowed');
        prevButton.classList.add('hover:bg-gray-300');
        prevButton.title = 'Previous day';
    } else {
        prevButton.classList.add('opacity-50', 'cursor-not-allowed');
        prevButton.classList.remove('hover:bg-gray-300');
        prevButton.title = 'No data available for previous day';
    }
    
    if (hasNextData) {
        nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
        nextButton.classList.add('hover:bg-gray-300');
        nextButton.title = 'Next day';
    } else {
        nextButton.classList.add('opacity-50', 'cursor-not-allowed');
        nextButton.classList.remove('hover:bg-gray-300');
        nextButton.title = 'No data available for next day';
    }
    
    console.log('Navigation buttons updated:', {
        current: currentDate,
        prevAvailable: hasPrevData,
        nextAvailable: hasNextData
    });
}

// Enhanced fetch and update function with better error handling
function fetchAndUpdateCharts(dateStr) {
    console.log('=== FETCH AND UPDATE CHARTS START ===');
    console.log('Input dateStr:', dateStr);
    console.log('Current Plant ID:', window.plantId);
    
    // Validate Plant ID before proceeding
    if (!window.plantId) {
        console.error('❌ Cannot fetch data: Plant ID is missing');
        showNotification('Cannot load chart data: Plant ID is missing', 'error');
        
        // Still render empty charts to show timeline structure
        window.energyData = {};
        window.batteryPriceData = {};
        window.batterySavingsData = {};
        renderChartsAndTables();
        return;
    }
    
    const loadingIndicator = document.getElementById('loading-indicator');
    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
    
    // Parse dateStr as local date with better validation
    let selectedDate;
    let parts;
    
    try {
        if (dateStr && dateStr.includes('-')) {
            // YYYY-MM-DD format
            parts = dateStr.split('-');
        } else if (dateStr && dateStr.length === 8) {
            // YYYYMMDD format
            parts = [dateStr.slice(0,4), dateStr.slice(4,6), dateStr.slice(6,8)];
        } else if (dateStr) {
            console.warn('Unexpected date format:', dateStr);
            parts = [dateStr.slice(0,4), dateStr.slice(4,6), dateStr.slice(6,8)];
        } else {
            // Default to today
            const today = new Date();
            parts = [
                today.getFullYear().toString(), 
                (today.getMonth() + 1).toString().padStart(2, '0'), 
                today.getDate().toString().padStart(2, '0')
            ];
        }
        
        selectedDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
        
        if (isNaN(selectedDate.getTime())) {
            console.error('Invalid date parsed from:', dateStr, 'parts:', parts);
            selectedDate = new Date(); // Fallback to today
        }
    } catch (e) {
        console.error('Date parsing error:', e, 'dateStr:', dateStr);
        selectedDate = new Date(); // Fallback to today
    }
    
    console.log('Parsed selected date:', selectedDate.toDateString());
    
    // Check if selected date is today
    const today = new Date();
    const todayYear = today.getFullYear();
    const todayMonth = today.getMonth();
    const todayDate = today.getDate();
    
    const selectedYear = selectedDate.getFullYear();
    const selectedMonth = selectedDate.getMonth();
    const selectedDay = selectedDate.getDate();
    
    const isToday = (selectedYear === todayYear && selectedMonth === todayMonth && selectedDay === todayDate);
    
    console.log(`📅 Date comparison: Selected(${selectedYear}-${selectedMonth+1}-${selectedDay}) vs Today(${todayYear}-${todayMonth+1}-${todayDate}) = isToday: ${isToday}`);
    
    // Calculate start of day (00:00:00) in EEST timezone (UTC+3)
    const year = selectedDate.getFullYear();
    const month = selectedDate.getMonth();
    const date = selectedDate.getDate();
    
    // Create UTC midnight for the selected date
    const utcMidnight = Date.UTC(year, month, date, 0, 0, 0, 0);
    // Convert to EEST midnight (EEST is UTC+3, so subtract 3 hours from UTC to get the UTC time that represents EEST midnight)
    const eestMidnightUTC = utcMidnight - (3 * 60 * 60 * 1000);
    const startOfDay = Math.floor(eestMidnightUTC / 1000);
    
    console.log(`🕒 Time calculations:`);
    console.log(`  Selected date: ${selectedDate.toDateString()}`);
    console.log(`  UTC midnight: ${new Date(utcMidnight).toISOString()}`);
    console.log(`  EEST midnight UTC: ${new Date(eestMidnightUTC).toISOString()}`);
    console.log(`  Start of day timestamp: ${startOfDay}`);
    console.log(`  Start of day converts to: ${new Date(startOfDay * 1000).toString()}`);
    
    let url;
    if (isToday) {
        // For today: only use start parameter (from midnight until now)
        url = `/plants/${window.plantId}/data?start=${startOfDay}&_t=${Date.now()}`;
        console.log('🌅 Fetching TODAY data from:', url);
    } else {
        // For historical dates: use both start and end (full day 00:00-23:59)
        const eestEndOfDayUTC = utcMidnight + (24 * 60 * 60 * 1000) - 1 - (3 * 60 * 60 * 1000);
        const endOfDay = Math.floor(eestEndOfDayUTC / 1000);
        url = `/plants/${window.plantId}/data?start=${startOfDay}&end=${endOfDay}&_t=${Date.now()}`;
        console.log('📊 Fetching HISTORICAL data from:', url);
        console.log(`  End of day timestamp: ${endOfDay}, converts to: ${new Date(endOfDay * 1000).toString()}`);
    }
    
    // Clear existing chart data before fetching new data
    console.log('🧹 Clearing existing chart data...');
    window.energyData = {};
    window.batteryPriceData = {};
    window.batterySavingsData = {};
    
    fetch(url, { 
        credentials: 'same-origin',
        cache: 'no-cache',  // Force no cache
        headers: {
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        }
    })
        .then(resp => {
            console.log(`📡 API Response: ${resp.status} ${resp.statusText}`);
            if (!resp.ok) {
                throw new Error(`HTTP ${resp.status}: ${resp.statusText}`);
            }
            return resp.json();
        })
        .then(data => {
            console.log('✅ Received fresh data:', data);
            
            // Always use fresh data - no cache
            window.energyData = data.energy_chart || {};
            window.batteryPriceData = data.battery_price || {};
            window.batterySavingsData = data.battery_savings || {};
            
            // Check if we have any data
            const hasEnergyData = Object.keys(window.energyData).length > 0;
            const hasBatteryData = Object.keys(window.batteryPriceData).length > 0;
            const hasSavingsData = Object.keys(window.batterySavingsData).length > 0;
            
            console.log(`📈 Data summary: Energy(${Object.keys(window.energyData).length}), Battery(${Object.keys(window.batteryPriceData).length}), Savings(${Object.keys(window.batterySavingsData).length})`);
            
            // Always render charts (even with empty data to show full timeline)
            renderChartsAndTables();
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            
            // Show appropriate message based on data availability
            if (!hasEnergyData && !hasBatteryData && !hasSavingsData) {
                if (isToday) {
                    showNotification('No data available yet today. Charts will update as new data arrives.', 'info');
                } else {
                    showNotification('No chart data available for the selected date. Showing empty timeline.', 'info');
                }
            } else {
                console.log('✅ Charts updated successfully with data');
            }
        })
        .catch(err => {
            console.error('❌ Error fetching chart data:', err);
            
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            
            // Even on error, render empty charts with full timeline
            window.energyData = {};
            window.batteryPriceData = {};
            window.batterySavingsData = {};
            renderChartsAndTables();
            
            // Show appropriate error message
            if (err.message.includes('404')) {
                showNotification('Plant data endpoint not found. Please check the plant ID.', 'error');
            } else if (err.message.includes('500')) {
                showNotification('Server error while fetching data. Please try again later.', 'error');
            } else {
                showNotification('Network error fetching chart data. Showing empty timeline.', 'error');
            }
        });
}

// Generate full 24-hour timeline with 30-minute intervals
function generateFullTimeline() {
    const timeline = [];
    for (let hour = 0; hour < 24; hour++) {
        for (let minute = 0; minute < 60; minute += 30) {
            const timeStr = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
            timeline.push(timeStr);
        }
    }
    return timeline;
}

// Function to map data to full timeline
function mapDataToTimeline(energyData, timeline) {
    const mappedData = {
        pv: new Array(timeline.length).fill(null),
        battery: new Array(timeline.length).fill(null),
        grid: new Array(timeline.length).fill(null)
    };
    
    if (!energyData || Object.keys(energyData).length === 0) {
        return mappedData;
    }
    
    const entries = Object.entries(energyData).sort(([a], [b]) => new Date(a) - new Date(b));
    const processedTimestamps = new Set(); // Prevent duplicate processing due to offset
    
    entries.forEach(([timestamp, values]) => {
        // Skip if we've already processed this timestamp (prevents offset duplicates)
        if (processedTimestamps.has(timestamp)) {
            return;
        }
        processedTimestamps.add(timestamp);
        
        const date = new Date(timestamp);
        // Apply user's time offset for display
        const offsetHours = window.userTimeOffset || 0;
        if (offsetHours !== 0) {
            date.setTime(date.getTime() + (offsetHours * 60 * 60 * 1000));
        }
        
        const hour = date.getHours();
        const minute = date.getMinutes();
        
        // Round to nearest 30-minute interval
        const roundedMinute = minute < 15 ? 0 : (minute < 45 ? 30 : 60);
        const finalHour = roundedMinute === 60 ? (hour + 1) % 24 : hour;
        const finalMinute = roundedMinute === 60 ? 0 : roundedMinute;
        
        const timeStr = `${finalHour.toString().padStart(2, '0')}:${finalMinute.toString().padStart(2, '0')}`;
        const timeIndex = timeline.indexOf(timeStr);
        
        if (timeIndex !== -1) {
            mappedData.pv[timeIndex] = values.pv_p / 1000;
            mappedData.battery[timeIndex] = values.battery_p / 1000;
            mappedData.grid[timeIndex] = values.grid_p / 1000;
        }
    });
    
    return mappedData;
}

// Function to render all charts and tables
function renderChartsAndTables() {
    const fullTimeline = generateFullTimeline();
    
    // --- ENERGY CHART ---
    const energyChartElem = document.getElementById('energyChart');
    if (energyChartElem) {
        const mappedData = mapDataToTimeline(window.energyData, fullTimeline);
        
        if (window.chartInstances.energy) window.chartInstances.energy.destroy();
        
        window.chartInstances.energy = new Chart(energyChartElem, {
            type: 'line',
            data: {
                labels: fullTimeline,
                datasets: [
                    {
                        label: 'PV (kW)', 
                        data: mappedData.pv,
                        borderColor: 'rgba(0,123,255,1)', 
                        backgroundColor: 'rgba(0,123,255,0.15)', 
                        fill: true, 
                        pointRadius: 1, 
                        pointHoverRadius: 8,
                        spanGaps: false,
                        tension: 0.1
                    },
                    {
                        label: 'Battery (kW)', 
                        data: mappedData.battery,
                        borderColor: 'rgba(220,53,69,1)', 
                        backgroundColor: 'rgba(220,53,69,0.12)', 
                        fill: true, 
                        pointRadius: 1, 
                        pointHoverRadius: 8,
                        spanGaps: false,
                        tension: 0.1
                    },
                    {
                        label: 'Grid (kW)', 
                        data: mappedData.grid,
                        borderColor: 'rgba(40,167,69,1)', 
                        backgroundColor: 'rgba(40,167,69,0.12)', 
                        fill: true, 
                        pointRadius: 1, 
                        pointHoverRadius: 8,
                        spanGaps: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'top', padding: 30 },
                    tooltip: {
                        filter: function(tooltipItem) {
                            return tooltipItem.parsed.y !== null;
                        }
                    }
                },
                interaction: { mode: 'index', intersect: false },
                elements: { point: { radius: 2, hoverRadius: 8 } },
                scales: {
                    y: {
                        title: { display: true, text: 'Power (kW)' },
                        ticks: { font: { size: 12 } },
                        grid: { lineWidth: 1, color: context => context.tick && context.tick.value === 0 ? '#000' : '#e5e7eb' }
                    },
                    x: { 
                        ticks: { 
                            font: { size: 10 },
                            maxTicksLimit: 24,
                            callback: function(value, index) {
                                const time = this.getLabelForValue(value);
                                // Only show hourly labels for cleaner look
                                if (time.endsWith(':00')) {
                                    return formatChartTimeLabel(time);
                                }
                                return '';
                            }
                        }, 
                        border: { display: true, width: 2 },
                        grid: { color: '#f3f4f6' }
                    }
                }
            }
        });
        
        // Fill Energy Data Table with actual data only
        const energyTable = document.getElementById('energyDataTableBody');
        if (energyTable && window.energyData && Object.keys(window.energyData).length > 0) {
            energyTable.innerHTML = '';
            const entries = Object.entries(window.energyData).sort(([a], [b]) => new Date(a) - new Date(b));
            entries.forEach(([ts, val]) => {
                energyTable.innerHTML += `<tr><td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td><td class="px-4 py-2 text-center">${(val.pv_p / 1000).toFixed(2)}</td><td class="px-4 py-2 text-center">${(val.battery_p / 1000).toFixed(2)}</td><td class="px-4 py-2 text-center">${(val.grid_p / 1000).toFixed(2)}</td></tr>`;
            });
        } else if (energyTable) {
            energyTable.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No energy data available for the selected date</td></tr>';
        }
    }
    
    // --- BATTERY CHART ---
    const batteryChartElem = document.getElementById('batteryChart');
    if (batteryChartElem) {
        const batteryMappedData = {
            battery: new Array(fullTimeline.length).fill(null),
            tariff: new Array(fullTimeline.length).fill(null),
            price: new Array(fullTimeline.length).fill(null)
        };
        
        if (window.batteryPriceData && Object.keys(window.batteryPriceData).length > 0) {
            const entries = Object.entries(window.batteryPriceData).sort(([a], [b]) => new Date(a) - new Date(b));
            const processedTimestamps = new Set(); // Prevent duplicate processing due to offset
            
            entries.forEach(([timestamp, values]) => {
                // Skip if we've already processed this timestamp (prevents offset duplicates)
                if (processedTimestamps.has(timestamp)) {
                    return;
                }
                processedTimestamps.add(timestamp);
                
                const date = new Date(timestamp);
                // Apply user's time offset for display
                const offsetHours = window.userTimeOffset || 0;
                if (offsetHours !== 0) {
                    date.setTime(date.getTime() + (offsetHours * 60 * 60 * 1000));
                }
                
                const hour = date.getHours();
                const minute = date.getMinutes();
                
                // Round to nearest 30-minute interval
                const roundedMinute = minute < 15 ? 0 : (minute < 45 ? 30 : 60);
                const finalHour = roundedMinute === 60 ? (hour + 1) % 24 : hour;
                const finalMinute = roundedMinute === 60 ? 0 : roundedMinute;
                
                const timeStr = `${finalHour.toString().padStart(2, '0')}:${finalMinute.toString().padStart(2, '0')}`;
                const timeIndex = fullTimeline.indexOf(timeStr);
                
                if (timeIndex !== -1) {
                    batteryMappedData.battery[timeIndex] = values.battery_p / 1000;
                    batteryMappedData.tariff[timeIndex] = values.tariff;
                    
                    // Convert price from what appears to be €/MWh to €/kWh
                    // Your API prices like 39.62, 55.24 seem to be in €/MWh
                    // Convert to €/kWh by dividing by 1000
                    const priceInKWh = (values.price || 0) / 1000;
                    batteryMappedData.price[timeIndex] = priceInKWh;
                    
                    // Debug logging for the first few entries
                    if (timeIndex < 5) {
                        console.log(`=== BATTERY CHART MAPPING ${timeIndex} ===`);
                        console.log('Timestamp:', timestamp);
                        console.log('Time:', timeStr);
                        console.log('Battery power:', values.battery_p, 'W');
                        console.log('Tariff:', values.tariff, '€/kWh');
                        console.log('Price (raw):', values.price, '€/MWh');
                        console.log('Price (converted):', priceInKWh, '€/kWh');
                        console.log('Price type:', typeof values.price);
                        console.log('=== END MAPPING ===');
                    }
                }
            });
        }
        
        if (window.chartInstances.battery) window.chartInstances.battery.destroy();
        
        window.chartInstances.battery = new Chart(batteryChartElem, {
            type: 'line',
            data: {
                labels: fullTimeline,
                datasets: [
                    {
                        label: 'Battery Power (kW)',
                        data: batteryMappedData.battery,
                        borderColor: 'rgba(220,53,69,1)',
                        backgroundColor: 'rgba(220,53,69,0.1)',
                        fill: true,
                        yAxisID: 'y',
                        pointRadius: 1,
                        pointHoverRadius: 8,
                        spanGaps: false,
                        tension: 0.1
                    },
                    {
                        label: 'Energy Price (€/kWh)',
                        data: batteryMappedData.tariff,
                        borderColor: 'rgba(75,192,192,1)',
                        backgroundColor: 'rgba(75,192,192,0.1)',
                        fill: false,
                        yAxisID: 'y1',
                        pointRadius: 1,
                        pointHoverRadius: 8,
                        spanGaps: false,
                        tension: 0.1
                    },
                    {
                        label: 'API Price (€/kWh)',
                        data: batteryMappedData.price,
                        borderColor: 'rgba(255,159,64,1)',
                        backgroundColor: 'rgba(255,159,64,0.1)',
                        fill: false,
                        yAxisID: 'y1',
                        pointRadius: 1,
                        pointHoverRadius: 8,
                        spanGaps: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'top' },
                    tooltip: {
                        filter: function(tooltipItem) {
                            return tooltipItem.parsed.y !== null;
                        }
                    }
                },
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Battery Power (kW)' },
                        grid: { color: '#e5e7eb' },
                        ticks: { font: { size: 12 } }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { text: 'Energy Price (€/kWh)' },
                        grid: { display: false },
                        ticks: { font: { size: 12 } }
                    },
                    x: { 
                        ticks: { 
                            font: { size: 10 },
                            maxTicksLimit: 24,
                            callback: function(value, index) {
                                const time = this.getLabelForValue(value);
                                // Only show hourly labels for cleaner look
                                if (time.endsWith(':00')) {
                                    return formatChartTimeLabel(time);
                                }
                                return '';
                            }
                        },
                        grid: { color: '#f3f4f6' }
                    }
                }
            }
        });
        
        // Fill Battery Data Table with actual data only
        const batteryTable = document.getElementById('batteryDataTableBody');
        if (batteryTable && window.batteryPriceData && Object.keys(window.batteryPriceData).length > 0) {
            batteryTable.innerHTML = '';
            const entries = Object.entries(window.batteryPriceData).sort(([a], [b]) => new Date(a) - new Date(b));
            entries.forEach(([ts, val]) => {
                const priceConverted = (val.price || 0) / 1000; // Convert from €/MWh to €/kWh
                batteryTable.innerHTML += `<tr><td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td><td class="px-4 py-2 text-center">${(val.battery_p / 1000).toFixed(2)}</td><td class="px-4 py-2 text-center">${val.tariff.toFixed(4)}</td><td class="px-4 py-2 text-center">${priceConverted.toFixed(4)}</td></tr>`;
            });
        } else if (batteryTable) {
            batteryTable.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No battery data available for the selected date</td></tr>';
        }
    }
    
    // --- BATTERY SAVINGS CHART (HOURLY AVERAGES) ---
    const savingsChartElem = document.getElementById('savingsChart');
    if (savingsChartElem) {
        // Create hourly timeline (24 hours: 00:00, 01:00, ..., 23:00)
        const hourlyTimeline = Array.from({length: 24}, (_, i) => `${i.toString().padStart(2, '0')}:00`);
        const hourlySavingsData = new Array(24).fill(null);
        const hourlyPriceData = new Array(24).fill(null);
        
        // Group data by hour and calculate averages
        const hourlyGroups = {}; // hour -> {savings: [], prices: []}
        let totalCost = 0; // €
        let dataPointCount = 0;
        const processedTimestamps = new Set(); // Prevent duplicate processing due to offset
        let entries = []; // Initialize for debug logging
        let timeIntervalHours = 0.5; // Default 30 minutes - declare outside if block
        
        if (window.batterySavingsData && Object.keys(window.batterySavingsData).length > 0) {
            entries = Object.entries(window.batterySavingsData).sort(([a], [b]) => new Date(a) - new Date(b));
            
            console.log('=== RAW BATTERY SAVINGS DATA ===');
            console.log('Total entries found:', entries.length);
            entries.forEach(([timestamp, values], index) => {
                console.log(`Entry ${index + 1}: ${timestamp} -> ${values.battery_savings}€`);
            });
            console.log('=== END RAW DATA ===');
            
            // Calculate the time interval between data points
            if (entries.length >= 2) {
                const firstTime = new Date(entries[0][0]);
                const secondTime = new Date(entries[1][0]);
                const intervalMs = secondTime - firstTime;
                timeIntervalHours = intervalMs / (1000 * 60 * 60); // Convert to hours
                
                // Clamp to reasonable values (15 min to 2 hours)
                timeIntervalHours = Math.max(0.25, Math.min(2, timeIntervalHours));
            }
            
            console.log('Calculated time interval:', timeIntervalHours, 'hours');
            
            // Initialize hourly groups
            for (let h = 0; h < 24; h++) {
                hourlyGroups[h] = {savings: [], prices: []};
            }
            
            entries.forEach(([timestamp, values]) => {
                // Skip if we've already processed this timestamp (prevents offset duplicates)
                if (processedTimestamps.has(timestamp)) {
                    console.log('Skipping duplicate timestamp:', timestamp);
                    return;
                }
                processedTimestamps.add(timestamp);
                
                const date = new Date(timestamp);
                // Apply user's time offset for display
                const offsetHours = window.userTimeOffset || 0;
                if (offsetHours !== 0) {
                    date.setTime(date.getTime() + (offsetHours * 60 * 60 * 1000));
                }
                
                const hour = date.getHours();
                const savingsValue = parseFloat(values.battery_savings || 0);
                
                console.log(`Processing: ${timestamp} -> Hour ${hour}, Savings: ${savingsValue}€`);
                
                // Group savings by hour (we'll calculate averages later)
                hourlyGroups[hour].savings.push(savingsValue);
                
                // Get corresponding price data
                const priceData = window.batteryPriceData && window.batteryPriceData[timestamp];
                if (priceData && priceData.price !== undefined) {
                    const priceConverted = priceData.price / 1000; // Convert from €/MWh to €/kWh
                    hourlyGroups[hour].prices.push(priceConverted);
                }
                
                // Count total data points for debugging
                dataPointCount++;
            });
            
            // Calculate hourly averages and populate chart data
            console.log('=== HOURLY AVERAGES CALCULATION ===');
            for (let hour = 0; hour < 24; hour++) {
                const savingsArray = hourlyGroups[hour].savings;
                const pricesArray = hourlyGroups[hour].prices;
                
                if (savingsArray.length > 0) {
                    // Calculate average savings for this hour
                    const avgSavings = savingsArray.reduce((sum, val) => sum + val, 0) / savingsArray.length;
                    hourlySavingsData[hour] = avgSavings;
                    
                    console.log(`Hour ${hour}:00 - ${savingsArray.length} data points: [${savingsArray.map(v => v.toFixed(4)).join(', ')}] → Average: ${avgSavings.toFixed(6)}€`);
                }
                
                if (pricesArray.length > 0) {
                    // Calculate average price for this hour
                    const avgPrice = pricesArray.reduce((sum, val) => sum + val, 0) / pricesArray.length;
                    hourlyPriceData[hour] = avgPrice;
                    
                    console.log(`Hour ${hour}:00 - Average price: ${avgPrice.toFixed(6)}€/kWh`);
                }
            }
            console.log('=== END HOURLY AVERAGES ===');
        }
        
        // Calculate total savings from the hourly averages we already calculated
        let totalSavings = 0;
        let totalSavingsOldMethod = 0; // For comparison
        console.log('=== DETAILED HOURLY AVERAGES CALCULATION ===');
        console.log('hourlySavingsData array:', hourlySavingsData);
        
        hourlySavingsData.forEach((avgSavings, hour) => {
            if (avgSavings !== null) {
                console.log(`\n--- Hour ${hour}:00 ---`);
                console.log(`Average savings: ${avgSavings}€`);
                console.log(`Is positive? ${avgSavings > 0}`);
                
                // Only add positive averages to total (as per user requirement)
                if (avgSavings > 0) {
                    console.log(`Adding ${avgSavings.toFixed(6)}€ to total savings`);
                    console.log(`Total before: ${totalSavings.toFixed(6)}€`);
                    totalSavings += avgSavings;
                    console.log(`Total after: ${totalSavings.toFixed(6)}€`);
                } else {
                    console.log(`Skipping negative value: ${avgSavings.toFixed(6)}€`);
                }
                // For comparison, add all averages (positive and negative)
                totalSavingsOldMethod += avgSavings;
            } else {
                console.log(`Hour ${hour}:00 - No data (null)`);
            }
        });
        
        console.log('\n=== FINAL CALCULATION SUMMARY ===');
        console.log('Total positive hourly averages sum:', totalSavings.toFixed(6), '€');
        console.log('Total if including negative values:', totalSavingsOldMethod.toFixed(6), '€');
        console.log('Manual verification:');
        
        // Manual verification - let's calculate step by step
        let manualTotal = 0;
        const positiveHours = [];
        hourlySavingsData.forEach((avg, hour) => {
            if (avg !== null && avg > 0) {
                positiveHours.push({hour, value: avg});
                manualTotal += avg;
            }
        });
        
        console.log('Positive hours found:', positiveHours);
        console.log('Manual total calculation:', manualTotal.toFixed(6), '€');
        console.log('=== END TOTAL CALCULATION ===');
        
        const batterySavingsTotal = document.getElementById('batterySavingsTotal');
        
        // Debug logging for savings calculation
        console.log('=== BATTERY SAVINGS CALCULATION DEBUG ===');
        console.log('Total data points:', entries.length);
        console.log('Processed timestamps:', processedTimestamps.size);
        console.log('Time offset applied:', (window.userTimeOffset || 0), 'hours');
        console.log('Time interval between points:', timeIntervalHours, 'hours');
        console.log('=== SUMMARY OF CALCULATIONS ===');
        console.log('Total raw data points processed:', dataPointCount);
        console.log('Hours with savings data:', hourlySavingsData.filter(val => val !== null).length);
        console.log('Hourly averages:', hourlySavingsData.map((val, hour) => val !== null ? `${hour}:00=${val.toFixed(6)}€` : null).filter(v => v));
        console.log('FINAL TOTAL SAVINGS (sum of positive hourly averages):', totalSavings.toFixed(6), '€');
        console.log('=== CHECKING DATA SOURCES ===');
        console.log('Battery savings data available:', !!window.batterySavingsData);
        if (window.batterySavingsData) {
            console.log('Battery savings data keys count:', Object.keys(window.batterySavingsData).length);
            console.log('First few savings entries:', Object.entries(window.batterySavingsData).slice(0, 3));
        }
        if (window.batteryPriceData) {
            console.log('Battery price data keys count:', Object.keys(window.batteryPriceData).length);
            console.log('First few battery price entries:', Object.entries(window.batteryPriceData).slice(0, 3));
            console.log('=== PRICE VALUES DEBUG ===');
            Object.entries(window.batteryPriceData).slice(0, 5).forEach(([ts, data]) => {
                console.log(`Timestamp: ${ts}`);
                console.log(`  - battery_p: ${data.battery_p}`);
                console.log(`  - tariff: ${data.tariff}`);
                console.log(`  - price: ${data.price}`);
                console.log(`  - price type: ${typeof data.price}`);
            });
        }
        console.log('=== END DEBUG ===');
        
        console.log('🎯 FINAL DISPLAY VALUE CHECK:');
        console.log('totalSavings variable value:', totalSavings);
        console.log('totalSavings formatted:', totalSavings.toFixed(2));
        console.log('Display text will be: "Your savings today: € ' + totalSavings.toFixed(2) + '"');
        
        if (batterySavingsTotal) batterySavingsTotal.textContent = `Your savings today: € ${totalSavings.toFixed(2)}`;
        
        console.log('🎯 After setting, element text content is:', batterySavingsTotal ? batterySavingsTotal.textContent : 'Element not found');
        
        if (window.chartInstances.savings) window.chartInstances.savings.destroy();
        
        window.chartInstances.savings = new Chart(savingsChartElem, {
            type: 'bar',
            data: {
                labels: hourlyTimeline,
                datasets: [{
                    label: 'Battery Savings (€) - Hourly Average',
                    data: hourlySavingsData,
                    backgroundColor: hourlySavingsData.map(val => 
                        val === null ? 'transparent' : 
                        val >= 0 ? 'rgba(25,135,84,0.7)' : 'rgba(220,53,69,0.7)'
                    ),
                    hoverBackgroundColor: hourlySavingsData.map(val => 
                        val === null ? 'transparent' : 
                        val >= 0 ? 'rgba(25,135,84,1)' : 'rgba(220,53,69,1)'
                    ),
                    borderSkipped: false,
                    yAxisID: 'y'
                }, {
                    label: 'API Price (€/kWh) - Hourly Average',
                    type: 'line',
                    data: hourlyPriceData,
                    borderColor: 'rgba(255,159,64,1)',
                    backgroundColor: 'rgba(255,159,64,0.1)',
                    fill: false,
                    yAxisID: 'y1',
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    spanGaps: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        filter: function(tooltipItem) {
                            return tooltipItem.parsed.y !== null;
                        }
                    }
                },
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { 
                        beginAtZero: true,
                        title: { display: true, text: 'Savings (€)' },
                        ticks: { font: { size: 12 } },
                        grid: { color: '#e5e7eb' },
                        position: 'left'
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'API Price (€/kWh)' },
                        grid: { display: false },
                        ticks: { font: { size: 12 } }
                    },
                    x: { 
                        ticks: { 
                            font: { size: 10 },
                            maxTicksLimit: 24,
                            callback: function(value, index) {
                                const time = this.getLabelForValue(value);
                                // Show all hourly labels since we now have 24 hourly data points
                                return formatChartTimeLabel(time);
                            }
                        },
                        grid: { color: '#f3f4f6' }
                    }
                }
            }
        });
        
        // Fill Savings Data Table with actual data only
        const savingsTable = document.getElementById('batterySavingsDataTableBody');
        if (savingsTable && window.batterySavingsData && Object.keys(window.batterySavingsData).length > 0) {
            savingsTable.innerHTML = '';
            const entries = Object.entries(window.batterySavingsData).sort(([a], [b]) => new Date(a) - new Date(b));
            entries.forEach(([ts, val]) => {
                const priceData = window.batteryPriceData && window.batteryPriceData[ts];
                const priceConverted = priceData && priceData.price !== undefined ? (priceData.price / 1000).toFixed(4) : 'N/A';
                savingsTable.innerHTML += `<tr><td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td><td class="px-4 py-2 text-center">${val.battery_savings.toFixed(2)}</td><td class="px-4 py-2 text-center">${priceConverted}</td></tr>`;
            });
        } else if (savingsTable) {
            savingsTable.innerHTML = '<tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">No savings data available for the selected date</td></tr>';
        }
    }
}

// Helper function to find nearest available date
function findNearestAvailableDate(targetDate, direction = 'both') {
    if (window.availableDates.size === 0) return null;
    
    const sortedDates = Array.from(window.availableDates).sort();
    const target = new Date(targetDate);
    
    if (direction === 'backward') {
        // Find latest date before target
        for (let i = sortedDates.length - 1; i >= 0; i--) {
            const availableDate = new Date(sortedDates[i]);
            if (availableDate < target) {
                return sortedDates[i];
            }
        }
        return null;
    } else if (direction === 'forward') {
        // Find earliest date after target
        for (let i = 0; i < sortedDates.length; i++) {
            const availableDate = new Date(sortedDates[i]);
            if (availableDate > target) {
                return sortedDates[i];
            }
        }
        return null;
    } else {
        // Find nearest date (both directions)
        let nearestDate = null;
        let minDiff = Infinity;
        
        sortedDates.forEach(dateStr => {
            const availableDate = new Date(dateStr);
            const diff = Math.abs(availableDate - target);
            if (diff < minDiff) {
                minDiff = diff;
                nearestDate = dateStr;
            }
        });
        
        return nearestDate;
    }
}

// Helper function to find latest available date
function findLatestAvailableDate() {
    if (window.availableDates.size === 0) return null;
    const sortedDates = Array.from(window.availableDates).sort();
    return sortedDates[sortedDates.length - 1];
}

// Enhanced initialization with robust auto-loading
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DASHBOARD INITIALIZATION START ===');
    console.log('Plant ID available:', window.plantId);
    console.log('Current time:', new Date().toString());
    
    // Critical check: abort if Plant ID is still missing
    if (!window.plantId) {
        console.error('❌ CRITICAL: Cannot initialize dashboard without Plant ID');
        showNotification('Critical Error: Plant ID is missing. Dashboard cannot function properly.', 'error');
        
        // Still render empty charts as fallback
        window.energyData = {};
        window.batteryPriceData = {};
        window.batterySavingsData = {};
        renderChartsAndTables();
        return;
    }
    
    // Step 1: Initialize empty charts immediately to show timeline structure
    console.log('🎨 Rendering initial empty charts...');
    window.energyData = {};
    window.batteryPriceData = {};
    window.batterySavingsData = {};
    renderChartsAndTables();
    
    const dateInput = document.getElementById('energy-date');
    if (!dateInput) {
        console.error('❌ Date input element not found');
        showNotification('Error: Date picker not found', 'error');
        return;
    }
    
    // Step 2: Setup date picker with today's date
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const todayStr = `${year}-${month}-${day}`;
    
    console.log(`📅 Today detected as: ${todayStr}`);
    console.log(`📅 Current time: ${now.toString()}`);
    
    dateInput.value = todayStr;
    dateInput.max = todayStr;
    
    // Step 3: Setup event listeners before loading data
    setupDateInputEventListeners(dateInput, todayStr);
    setupNavigationButtons(dateInput, todayStr);
    
    // Step 4: IMMEDIATELY load today's data (highest priority)
    console.log('🚀 IMMEDIATE: Loading today\'s data...');
    const todayFormatted = todayStr.replace(/-/g, '');
    fetchAndUpdateCharts(todayFormatted);
    
    // Step 5: Fetch available dates in background (low priority, non-blocking)
    console.log('🔄 BACKGROUND: Starting available dates fetch...');
    setTimeout(async () => {
        try {
            console.log('📊 Fetching available dates in background...');
            await fetchAvailableDates();
            console.log('✅ Available dates loaded successfully');
            updateNavigationButtons();
            
            // Check if today actually has data, provide user guidance if not
            if (window.availableDates.size > 0 && !window.availableDates.has(todayStr)) {
                const latestAvailable = findLatestAvailableDate();
                if (latestAvailable && latestAvailable !== todayStr) {
                    console.log(`ℹ️ Today has no data, latest available: ${latestAvailable}`);
                    
                    // Gentle suggestion to user without being intrusive
                    setTimeout(() => {
                        const userChoice = confirm(
                            `No data available for today (${todayStr}). ` +
                            `Would you like to view the latest available data from ${latestAvailable}?`
                        );
                        
                        if (userChoice) {
                            dateInput.value = latestAvailable;
                            const latestFormatted = latestAvailable.replace(/-/g, '');
                            fetchAndUpdateCharts(latestFormatted);
                            showNotification(`Switched to latest available data: ${latestAvailable}`, 'success');
                        }
                    }, 3000); // Wait 3 seconds before suggesting
                }
            } else if (window.availableDates.size > 0 && window.availableDates.has(todayStr)) {
                console.log('✅ Today has data available');
            }
            
        } catch (error) {
            console.warn('⚠️ Available dates loading failed (non-critical):', error);
            // This is non-critical - today's data was already loaded
        }
    }, 200); // Small delay to avoid blocking initial page render
    
    console.log('✅ Dashboard initialization completed successfully');
});

// Setup date input event listeners
function setupDateInputEventListeners(dateInput, todayStr) {
    dateInput.addEventListener('change', function() {
        const selectedDateStr = this.value;
        console.log('📅 Date changed to:', selectedDateStr);
        
        if (!selectedDateStr) {
            console.log('Empty date selected, ignoring');
            return;
        }
        
        // Validate Plant ID before proceeding
        if (!window.plantId) {
            console.error('❌ Cannot change date: Plant ID missing');
            showNotification('Cannot change date: Plant ID is missing', 'error');
            return;
        }
        
        // Check if selected date has available data (if available dates are loaded)
        if (window.availableDates.size > 0 && !window.availableDates.has(selectedDateStr)) {
            console.log('Selected date has no data, finding nearest available date');
            const nearestDate = findNearestAvailableDate(selectedDateStr);
            if (nearestDate) {
                this.value = nearestDate;
                showNotification(`No data available for ${selectedDateStr}. Showing nearest available date: ${nearestDate}`, 'info');
            } else {
                // Fallback to latest available date
                const latestDate = findLatestAvailableDate();
                if (latestDate) {
                    this.value = latestDate;
                    showNotification(`No data available for ${selectedDateStr}. Showing latest available date: ${latestDate}`, 'info');
                } else {
                    showNotification('No data available for any date', 'error');
                    return;
                }
            }
        }
        
        // Create dates in local timezone for proper comparison
        const selectedDate = new Date(this.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        selectedDate.setHours(0, 0, 0, 0);
        
        console.log('Date comparison:');
        console.log('- Selected:', selectedDate.toDateString(), selectedDate.getTime());
        console.log('- Today:', today.toDateString(), today.getTime());
        console.log('- Is future?', selectedDate > today);
        
        // Only prevent dates that are actually in the future (tomorrow or later)
        if (selectedDate > today) {
            console.log('Future date blocked, resetting to today or latest available');
            const latestAvailable = findLatestAvailableDate();
            
            // Use today if it has data, otherwise use latest available date
            if (window.availableDates.has(todayStr)) {
                this.value = todayStr;
            } else if (latestAvailable) {
                this.value = latestAvailable;
            } else {
                this.value = todayStr; // Fallback to today even if no data
            }
            showNotification('Cannot select future dates', 'info');
            return;
        }
        
        // Valid date selected - fetch data
        const formattedDate = this.value.replace(/-/g, '');
        console.log('✅ Fetching data for formatted date:', formattedDate);
        fetchAndUpdateCharts(formattedDate);
        updateNavigationButtons();
    });
}

// Setup navigation button event listeners
function setupNavigationButtons(dateInput, todayStr) {
    const prevButton = document.getElementById('energy-prev');
    const nextButton = document.getElementById('energy-next');

    if (prevButton) {
        prevButton.addEventListener('click', function() {
            if (this.disabled) return;
            
            const currentDate = new Date(dateInput.value);
            if (isNaN(currentDate.getTime())) return;
            
            // Go to previous day
            currentDate.setDate(currentDate.getDate() - 1);
            
            const year = currentDate.getFullYear();
            const month = String(currentDate.getMonth() + 1).padStart(2, '0');
            const day = String(currentDate.getDate()).padStart(2, '0');
            const newDateStr = `${year}-${month}-${day}`;
            
            // Check if the date has data
            if (window.availableDates.size > 0 && !window.availableDates.has(newDateStr)) {
                console.log('Previous day has no data, finding nearest available date');
                const nearestDate = findNearestAvailableDate(newDateStr, 'backward');
                if (nearestDate) {
                    dateInput.value = nearestDate;
                    showNotification(`No data for ${newDateStr}. Showing nearest date: ${nearestDate}`, 'info');
                } else {
                    showNotification('No previous data available', 'info');
                    return;
                }
            } else {
                dateInput.value = newDateStr;
            }
            
            console.log('Previous day clicked, setting date to:', dateInput.value);
            
            // Trigger the change event to fetch data
            const changeEvent = new Event('change', { bubbles: true });
            dateInput.dispatchEvent(changeEvent);
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', function() {
            if (this.disabled) return;
            
            const currentDate = new Date(dateInput.value);
            if (isNaN(currentDate.getTime())) return;
            
            // Go to next day
            currentDate.setDate(currentDate.getDate() + 1);
            
            const year = currentDate.getFullYear();
            const month = String(currentDate.getMonth() + 1).padStart(2, '0');
            const day = String(currentDate.getDate()).padStart(2, '0');
            const newDateStr = `${year}-${month}-${day}`;
            
            console.log('Next day clicked, checking date:', newDateStr);
            
            // Check if new date is in the future
            const newDate = new Date(newDateStr);
            const todayDate = new Date(todayStr);
            
            if (newDate > todayDate) {
                console.log('Next day would be in future, blocking');
                showNotification('Cannot select future dates', 'info');
                return;
            }
            
            // Check if the date has data
            if (window.availableDates.size > 0 && !window.availableDates.has(newDateStr)) {
                console.log('Next day has no data, finding nearest available date');
                const nearestDate = findNearestAvailableDate(newDateStr, 'forward');
                if (nearestDate && new Date(nearestDate) <= todayDate) {
                    dateInput.value = nearestDate;
                    showNotification(`No data for ${newDateStr}. Showing nearest date: ${nearestDate}`, 'info');
                } else {
                    showNotification('No future data available', 'info');
                    return;
                }
            } else {
                dateInput.value = newDateStr;
            }
            
            // Trigger the change event to fetch data
            const changeEvent = new Event('change', { bubbles: true });
            dateInput.dispatchEvent(changeEvent);
        });
    }
}

</script>

<script>
    // Enhanced chart download functionality with better UX
    function sendChartToBackend(chartId, chartName, plantId, type) {
        console.log(`=== DOWNLOAD ${type.toUpperCase()} DEBUG START ===`);
        console.log('Chart ID:', chartId);
        console.log('Chart Name:', chartName);
        console.log('Plant ID:', plantId);
        console.log('Type:', type);
        
        // Show loading indicator
        const downloadButton = document.querySelector(`#download${type.toUpperCase()}-${chartName}`);
        console.log('Download button found:', !!downloadButton);
        
        if (!downloadButton) {
            console.error('Download button not found!');
            showNotification(`Download button not found for ${type.toUpperCase()}`, 'error');
            return;
        }
        
        const originalText = downloadButton.textContent;
        downloadButton.innerHTML = '<span>Downloading...</span>';
        downloadButton.disabled = true;

        let canvas = document.getElementById(chartId);
        console.log('Canvas found:', !!canvas);
        
        if (!canvas) {
            console.error('Canvas not found!');
            showNotification(`Chart canvas not found for ${type.toUpperCase()} download`, 'error');
            downloadButton.innerHTML = originalText;
            downloadButton.disabled = false;
            return;
        }
        
        let dataUrl = canvas.toDataURL('image/png');
        console.log('Data URL length:', dataUrl.length);
        console.log('Data URL preview:', dataUrl.substring(0, 100) + '...');
        
        // Get selected date from the date input
        const dateInput = document.getElementById('energy-date');
        const selectedDate = dateInput ? dateInput.value : new Date().toISOString().split('T')[0];
        console.log('Selected date:', selectedDate);

        console.log('About to send POST request to save chart image...');
        
        return fetch(`/plants/${plantId}/save-chart-image`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                chart: chartName,
                image: dataUrl
            })
        })
        .then(response => {
            console.log('Save chart response status:', response.status);
            console.log('Save chart response ok:', response.ok);
            return response.json();
        })
        .then(data => {
            console.log('Save chart response data:', data);
            
            if (data.success) {
                console.log(`Chart saved successfully for ${type} download`);
                
                // For PNG downloads, we can download directly with the image data
                if (type === 'png') {
                    console.log('Initiating direct PNG download...');
                    downloadImageDirectly(dataUrl, `${plantId}_${chartName}_${selectedDate}.png`);
                } else {
                    // For CSV and PDF, use a better download approach to avoid page reloads
                    const downloadUrl = `/plants/${plantId}/download/${chartName}/${type}?date=${selectedDate}`;
                    console.log(`Initiating ${type.toUpperCase()} download: ${downloadUrl}`);
                    
                    // Use a more robust download method that doesn't cause page navigation
                    downloadFileWithFetch(downloadUrl, `${plantId}_${chartName}_${selectedDate}.${type}`);
                }
                
                showNotification(`${type.toUpperCase()} download completed successfully!`, 'success');
            } else {
                console.error('Chart save failed:', data);
                showNotification(`Error preparing ${type.toUpperCase()} download: ${data.message || 'Unknown error'}`, 'error');
            }
        })
        .catch(error => {
            console.error('Download error:', error);
            console.error('Error stack:', error.stack);
            showNotification(`${type.toUpperCase()} download failed. Please try again.`, 'error');
        })
        .finally(() => {
            console.log('Download process completed, resetting button state');
            // Reset button state
            downloadButton.innerHTML = originalText;
            downloadButton.disabled = false;
            console.log(`=== DOWNLOAD ${type.toUpperCase()} DEBUG END ===`);
        });
    }

    // Direct image download for PNG files
    function downloadImageDirectly(dataUrl, filename) {
        const link = document.createElement('a');
        link.href = dataUrl;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Enhanced download function that doesn't cause page reloads
    async function downloadFileWithFetch(url, filename) {
        try {
            console.log(`Downloading file from: ${url}`);
            
            // For authenticated downloads, use a form submission instead of fetch
            // This ensures proper session handling and avoids CORS/authentication issues
            downloadWithForm(url, filename);
            
        } catch (error) {
            console.error('Download error:', error);
            showNotification(`Download failed: ${error.message}`, 'error');
        }
    }

    // Alternative download method using form submission for better authentication handling
    function downloadWithForm(url, filename) {
        try {
            console.log(`Downloading via form submission: ${url}`);
            
            // Create a temporary form for download
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = url;
            form.style.display = 'none';
            
            // Add CSRF token as hidden input
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
            }
            
            // Add the form to the page and submit it
            document.body.appendChild(form);
            form.submit();
            
            // Clean up - remove the form after a delay
            setTimeout(() => {
                if (form.parentNode) {
                    form.parentNode.removeChild(form);
                }
            }, 1000);
            
            console.log('Form download submitted successfully');
            showNotification('Download initiated successfully!', 'success');
            
        } catch (error) {
            console.error('Form download error:', error);
            showNotification(`Download failed: ${error.message}`, 'error');
        }
    }

    // Backup fetch-based download function for debugging
    async function downloadFileWithFetchDebug(url, filename) {
        try {
            console.log(`Downloading file from: ${url}`);
            
            // Add CSRF token to the request
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            console.log('CSRF Token:', csrfToken ? 'Present' : 'Missing');
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/pdf,application/octet-stream,*/*'
                },
                credentials: 'same-origin'
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', [...response.headers.entries()]);

            if (!response.ok) {
                if (response.status === 401) {
                    showNotification('Authentication required. Please refresh and try again.', 'error');
                    return;
                } else if (response.status === 404) {
                    showNotification('Download file not found. Please try again.', 'error');
                    return;
                } else {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            }

            // Check if the response is actually a file (PDF/CSV)
            const contentType = response.headers.get('content-type');
            console.log('Response content type:', contentType);

            // Log the first part of the response to see what we're getting
            const text = await response.text();
            console.log('Response preview (first 200 chars):', text.substring(0, 200));

            if (contentType && (contentType.includes('application/pdf') || contentType.includes('text/csv') || contentType.includes('application/octet-stream'))) {
                // Convert text back to blob for download
                const blob = new Blob([text], { type: contentType });
                console.log('Downloaded blob size:', blob.size);

                // Create a temporary URL for the blob
                const blobUrl = window.URL.createObjectURL(blob);

                // Create a temporary link and click it to download
                const link = document.createElement('a');
                link.href = blobUrl;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                
                // Clean up
                document.body.removeChild(link);
                window.URL.revokeObjectURL(blobUrl);
                
                console.log('File download completed successfully');
                showNotification('Download completed successfully!', 'success');
            } else {
                console.error('Unexpected response type. Response preview:', text.substring(0, 500));
                showNotification('Download failed: Unexpected response from server', 'error');
            }

        } catch (error) {
            console.error('Download error:', error);
            showNotification(`Download failed: ${error.message}`, 'error');
        }
    }

    // Enhanced CSV download that uses current chart data
    function downloadCSVDirect(chartName, plantId) {
        const downloadButton = document.querySelector(`#downloadCSV-${chartName}`);
        const originalText = downloadButton.textContent;
        downloadButton.innerHTML = 'Preparing CSV...';
        downloadButton.style.pointerEvents = 'none';

        // Get selected date from the date input
        const dateInput = document.getElementById('energy-date');
        const selectedDate = dateInput ? dateInput.value : new Date().toISOString().split('T')[0];

        // Use current chart data instead of static files
        const currentData = window[`${chartName === 'battery' ? 'batteryPrice' : chartName === 'savings' ? 'batterySavings' : 'energy'}Data`];
        
        if (!currentData || Object.keys(currentData).length === 0) {
            showNotification('No data available for CSV download', 'error');
            downloadButton.innerHTML = originalText;
            downloadButton.style.pointerEvents = 'auto';
            return;
        }

        // Download CSV using the new robust download method
        const downloadUrl = `/plants/${plantId}/download/${chartName}/csv?date=${selectedDate}`;
        downloadFileWithFetch(downloadUrl, `${plantId}_${chartName}_${selectedDate}.csv`);
        
        // Reset button after a delay
        setTimeout(() => {
            downloadButton.innerHTML = originalText;
            downloadButton.style.pointerEvents = 'auto';
            showNotification('CSV download initiated!', 'success');
        }, 1000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Use the global plantId to ensure it's available for chart downloads
        if (!window.plantId) {
            console.error('[Download] Plant ID missing for downloads');
            
            // One more attempt to get the plant ID from URL
            const pathSegments = window.location.pathname.split('/');
            for (let i = 0; i < pathSegments.length; i++) {
                if (pathSegments[i] === 'plants' && i + 1 < pathSegments.length) {
                    window.plantId = pathSegments[i + 1];
                    console.log('[Download] Recovered plant ID from URL:', window.plantId);
                    break;
                }
            }
        }
        
        // ENERGY CHART DOWNLOADS
        const energyPNGButton = document.getElementById('downloadPNG-energy');
        if (energyPNGButton) {
            energyPNGButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (!window.plantId) {
                    showNotification('Cannot download chart: Plant ID is missing', 'error');
                    return;
                }
                sendChartToBackend('energyChart', 'energy', window.plantId, 'png');
            });
        }

        const energyPDFButton = document.getElementById('downloadPDF-energy');
        if (energyPDFButton) {
            energyPDFButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (!window.plantId) {
                    showNotification('Cannot download chart: Plant ID is missing', 'error');
                    return;
                }
                sendChartToBackend('energyChart', 'energy', window.plantId, 'pdf');
            });
        }

        // Override CSV link behavior for energy (only if element exists)
        const energyCSVButton = document.getElementById('downloadCSV-energy');
        if (energyCSVButton) {
            energyCSVButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (!window.plantId) {
                    showNotification('Cannot download CSV: Plant ID is missing', 'error');
                    return;
                }
                downloadCSVDirect('energy', window.plantId);
            });
        }

        // BATTERY CHART DOWNLOADS
        const batteryPNGButton = document.getElementById('downloadPNG-battery');
        if (batteryPNGButton) {
            batteryPNGButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (!window.plantId) {
                    showNotification('Cannot download chart: Plant ID is missing', 'error');
                    return;
                }
                sendChartToBackend('batteryChart', 'battery', window.plantId, 'png');
            });
        }

        const batteryPDFButton = document.getElementById('downloadPDF-battery');
        if (batteryPDFButton) {
            batteryPDFButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (!window.plantId) {
                    showNotification('Cannot download chart: Plant ID is missing', 'error');
                    return;
                }
                sendChartToBackend('batteryChart', 'battery', window.plantId, 'pdf');
            });
        }

        // Override CSV link behavior for battery (only if element exists)
        const batteryCSVButton = document.getElementById('downloadCSV-battery');
        if (batteryCSVButton) {
            batteryCSVButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (!window.plantId) {
                    showNotification('Cannot download CSV: Plant ID is missing', 'error');
                    return;
                }
                downloadCSVDirect('battery', window.plantId);
            });
        }

        // SAVINGS CHART DOWNLOADS
        const savingsPNGButton = document.getElementById('downloadPNG-savings');
        if (savingsPNGButton) {
            savingsPNGButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (!window.plantId) {
                    showNotification('Cannot download chart: Plant ID is missing', 'error');
                    return;
                }
                sendChartToBackend('savingsChart', 'savings', window.plantId, 'png');
            });
        }

        const savingsPDFButton = document.getElementById('downloadPDF-savings');
        if (savingsPDFButton) {
            savingsPDFButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (!window.plantId) {
                    showNotification('Cannot download chart: Plant ID is missing', 'error');
                    return;
                }
                sendChartToBackend('savingsChart', 'savings', window.plantId, 'pdf');
            });
        }

        // Override CSV link behavior for savings (only if element exists)
        const savingsCSVButton = document.getElementById('downloadCSV-savings');
        if (savingsCSVButton) {
            savingsCSVButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (!window.plantId) {
                    showNotification('Cannot download CSV: Plant ID is missing', 'error');
                    return;
                }
                downloadCSVDirect('savings', window.plantId);
            });
        }
    });
</script>
