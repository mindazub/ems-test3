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
    
    entries.forEach(([timestamp, values]) => {
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
                                return time.endsWith(':00') ? time : '';
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
            tariff: new Array(fullTimeline.length).fill(null)
        };
        
        if (window.batteryPriceData && Object.keys(window.batteryPriceData).length > 0) {
            const entries = Object.entries(window.batteryPriceData).sort(([a], [b]) => new Date(a) - new Date(b));
            
            entries.forEach(([timestamp, values]) => {
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
                                return time.endsWith(':00') ? time : '';
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
                batteryTable.innerHTML += `<tr><td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td><td class="px-4 py-2 text-center">${(val.battery_p / 1000).toFixed(2)}</td><td class="px-4 py-2 text-center">${val.tariff.toFixed(4)}</td></tr>`;
            });
        } else if (batteryTable) {
            batteryTable.innerHTML = '<tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">No battery data available for the selected date</td></tr>';
        }
    }
    
    // --- BATTERY SAVINGS CHART ---
    const savingsChartElem = document.getElementById('savingsChart');
    if (savingsChartElem) {
        const savingsMappedData = new Array(fullTimeline.length).fill(null);
        let totalSavings = 0;
        
        if (window.batterySavingsData && Object.keys(window.batterySavingsData).length > 0) {
            const entries = Object.entries(window.batterySavingsData).sort(([a], [b]) => new Date(a) - new Date(b));
            
            entries.forEach(([timestamp, values]) => {
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
                    savingsMappedData[timeIndex] = values.battery_savings;
                    totalSavings += parseFloat(values.battery_savings || 0);
                }
            });
        }
        
        const batterySavingsTotal = document.getElementById('batterySavingsTotal');
        if (batterySavingsTotal) batterySavingsTotal.textContent = `Your savings today: € ${totalSavings.toFixed(2)}`;
        
        if (window.chartInstances.savings) window.chartInstances.savings.destroy();
        
        window.chartInstances.savings = new Chart(savingsChartElem, {
            type: 'bar',
            data: {
                labels: fullTimeline,
                datasets: [{
                    label: 'Battery Savings (€)',
                    data: savingsMappedData,
                    backgroundColor: savingsMappedData.map(val => 
                        val === null ? 'transparent' : 
                        val >= 0 ? 'rgba(25,135,84,0.7)' : 'rgba(220,53,69,0.7)'
                    ),
                    hoverBackgroundColor: savingsMappedData.map(val => 
                        val === null ? 'transparent' : 
                        val >= 0 ? 'rgba(25,135,84,1)' : 'rgba(220,53,69,1)'
                    ),
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
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
                        grid: { color: '#e5e7eb' }
                    },
                    x: { 
                        ticks: { 
                            font: { size: 10 },
                            maxTicksLimit: 24,
                            callback: function(value, index) {
                                const time = this.getLabelForValue(value);
                                return time.endsWith(':00') ? time : '';
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
                savingsTable.innerHTML += `<tr><td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td><td class="px-4 py-2 text-center">${val.battery_savings.toFixed(2)}</td></tr>`;
            });
        } else if (savingsTable) {
            savingsTable.innerHTML = '<tr><td colspan="2" class="px-4 py-8 text-center text-gray-400">No savings data available for the selected date</td></tr>';
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
    function sendChartToBackend(chartId, chartName, plantId, type) {
        let canvas = document.getElementById(chartId);
        let dataUrl = canvas.toDataURL('image/png');

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
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  window.location.href = `/plants/${plantId}/download/${chartName}/${type}`;
              } else {
                  alert('Error saving chart image!');
              }
          });
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
        
        // ENERGY
        document.getElementById('downloadPNG-energy').addEventListener('click', function(e) {
            e.preventDefault();
            if (!window.plantId) {
                showNotification('Cannot download chart: Plant ID is missing', 'error');
                return;
            }
            sendChartToBackend('energyChart', 'energy', window.plantId, 'png');
        });
        document.getElementById('downloadPDF-energy').addEventListener('click', function(e) {
            e.preventDefault();
            if (!window.plantId) {
                showNotification('Cannot download chart: Plant ID is missing', 'error');
                return;
            }
            sendChartToBackend('energyChart', 'energy', window.plantId, 'pdf');
        });
        // BATTERY
        document.getElementById('downloadPNG-battery').addEventListener('click', function(e) {
            e.preventDefault();
            if (!window.plantId) {
                showNotification('Cannot download chart: Plant ID is missing', 'error');
                return;
            }
            sendChartToBackend('batteryChart', 'battery', window.plantId, 'png');
        });
        document.getElementById('downloadPDF-battery').addEventListener('click', function(e) {
            e.preventDefault();
            if (!window.plantId) {
                showNotification('Cannot download chart: Plant ID is missing', 'error');
                return;
            }
            sendChartToBackend('batteryChart', 'battery', window.plantId, 'pdf');
        });
        // SAVINGS
        document.getElementById('downloadPNG-savings').addEventListener('click', function(e) {
            e.preventDefault();
            if (!window.plantId) {
                showNotification('Cannot download chart: Plant ID is missing', 'error');
                return;
            }
            sendChartToBackend('savingsChart', 'savings', window.plantId, 'png');
        });
        document.getElementById('downloadPDF-savings').addEventListener('click', function(e) {
            e.preventDefault();
            if (!window.plantId) {
                showNotification('Cannot download chart: Plant ID is missing', 'error');
                return;
            }
            sendChartToBackend('savingsChart', 'savings', window.plantId, 'pdf');
        });
    });
    </script>
