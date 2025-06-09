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
        <div class="flex items-center gap-3">
            <button id="energy-today" class="px-4 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 shadow-sm font-medium transition duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500" title="Show today's data">Today</button>
            <button id="energy-yesterday" class="px-4 py-1.5 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 shadow-sm font-medium transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="Show yesterday's data">Yesterday</button>
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
            ctx.lineWidth = 3; // Make it bolder
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
    // Create notification element if it doesn't exist
    let notificationContainer = document.getElementById('chart-notifications');
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'chart-notifications';
        notificationContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
        document.body.appendChild(notificationContainer);
    }
    
    // Create notification
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
    
    // Add close button functionality
    notification.querySelector('button').addEventListener('click', () => {
        notification.classList.add('opacity-0', 'scale-95');
        setTimeout(() => notification.remove(), 300);
    });
    
    // Add to container and remove after timeout
    notificationContainer.appendChild(notification);
    
    // Automatically remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('opacity-0', 'scale-95');
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
    
    // Log to console as well
    console[type === 'error' ? 'error' : type === 'success' ? 'info' : 'log'](`[Notification] ${message}`);
}

// Format date for chart labels
function formatLabelDate(dateString) {
    const options = { hour: '2-digit', minute: '2-digit' };
    try {
        let date;
        // Enhanced timestamp detection and handling
        if (typeof dateString === 'number' || /^\d+$/.test(dateString)) {
            // Unix timestamp in seconds or string that looks like a number
            date = new Date(parseInt(dateString) * 1000);
        } else {
            // ISO string or other date format
            date = new Date(dateString);
        }
        
        if (isNaN(date.getTime())) {
            console.log('Invalid date encountered:', dateString);
            return dateString;
        }
        
        return date.toLocaleTimeString([], options);
    } catch (e) {
        console.log('Error formatting date:', e, dateString);
        return dateString;
    }
}

// Define chart data variables globally so they can be accessed by all functions
window.energyData = {};
window.batteryPriceData = {};
window.batterySavingsData = {};
// Define plant ID globally to avoid scope issues with more robust initialization
// Try multiple methods to get the plant ID

// Method 1: From the JSON-encoded PHP variable
try {
    window.plantId = @json($plant->uid ?? $plant->uuid ?? $plant->id ?? null);
    console.log('Method 1 - Initial plant ID from backend JSON:', window.plantId);
} catch (e) {
    console.error('Error getting plant ID from JSON:', e);
}

// Method 2: Fallback to extract plant ID from URL if not set
if (!window.plantId) {
    const pathSegments = window.location.pathname.split('/');
    const plantIdFromUrl = pathSegments.find((segment, index) => {
        return index > 0 && pathSegments[index-1] === 'plants' && /^[a-zA-Z0-9-]+$/.test(segment);
    });
    
    if (plantIdFromUrl) {
        console.log('Method 2 - Extracted plant ID from URL:', plantIdFromUrl);
        window.plantId = plantIdFromUrl;
    }
}

// Method 3: Get from data attribute on the calendar controls
if (!window.plantId) {
    const calendarControls = document.getElementById('energy-calendar-controls');
    if (calendarControls && calendarControls.dataset.plantId) {
        window.plantId = calendarControls.dataset.plantId;
        console.log('Method 3 - Extracted plant ID from data attribute:', window.plantId);
    }
}

// Final check and warning if plant ID is still missing
if (!window.plantId) {
    console.error('WARNING: Plant ID is missing! This will cause functionality issues.');
    // Add a visible error for the user
    document.addEventListener('DOMContentLoaded', function() {
        showNotification('Error: Plant ID is missing. Some features may not work correctly.', 'error');
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // --- ENERGY CHART ---
    // Try to use plant->energy_chart, fallback to aggregated_data_snapshots if empty
    window.energyData = @json($plant->energy_chart ?? []);
    window.batteryPriceData = @json($plant->battery_price ?? []);
    window.batterySavingsData = @json($plant->battery_savings ?? []);
    const aggregatedSnapshots = @json($plant->aggregated_data_snapshots ?? []);
    
    // Debug logging to help identify issues
    console.log('Energy Chart Data:', energyData);
    console.log('Battery Price Data:', batteryPriceData);  
    console.log('Battery Savings Data:', batterySavingsData);
    console.log('Aggregated Snapshots:', aggregatedSnapshots);
    
    // Additional debugging for timestamp format
    if (aggregatedSnapshots && aggregatedSnapshots.length > 0) {
        const firstSnapshot = aggregatedSnapshots[0];
        console.log('First snapshot timestamp details:', {
            hasTimestamp: !!firstSnapshot.timestamp,
            hasUnixDt: !!firstSnapshot.dt,
            hasTime: !!firstSnapshot.time,
            timestampValue: firstSnapshot.timestamp,
            dtValue: firstSnapshot.dt,
            timeValue: firstSnapshot.time
        });
    }
    
    // Additional debugging for timestamp format
    if (aggregatedSnapshots.length > 0) {
        const firstSnapshot = aggregatedSnapshots[0];
        console.log('First snapshot timestamp details:', {
            hasTimestamp: !!firstSnapshot.timestamp,
            hasUnixDt: !!firstSnapshot.dt,
            hasTime: !!firstSnapshot.time,
            timestampValue: firstSnapshot.timestamp,
            dtValue: firstSnapshot.dt,
            timeValue: firstSnapshot.time
        });
    }
    
    // Process the aggregated data from the API
    if (!energyData || Object.keys(energyData).length === 0) {
        console.log('Using aggregated_data_snapshots for energy chart');
        energyData = {};
        
        // Generate demo data if no real data exists (for testing purposes)
        if (aggregatedSnapshots.length === 0) {
            const now = new Date();
            for (let i = 0; i < 24; i++) {
                const timestamp = new Date(now.getTime() - (23-i) * 3600 * 1000).toISOString();
                energyData[timestamp] = {
                    pv_p: Math.random() * 5000,
                    battery_p: Math.random() * 3000 * (Math.random() > 0.5 ? 1 : -1),
                    grid_p: Math.random() * 4000 * (Math.random() > 0.3 ? 1 : -1)
                };
            }
        } else {
            // Process real data
            if (aggregatedSnapshots.forEach) {
                console.log('Processing', aggregatedSnapshots.length, 'aggregated data snapshots');
                // Process real data
                aggregatedSnapshots.forEach(row => {
                    try {
                        // Check for dt (unix timestamp) or timestamp or time fields
                        let timestamp;
                        if (row.timestamp) {
                            timestamp = row.timestamp;
                        } else if (row.dt) {
                            // Convert unix timestamp to ISO string
                            timestamp = new Date(row.dt * 1000).toISOString();
                        } else if (row.time) {
                            timestamp = new Date(row.time).toISOString();
                        }
                        
                        if (timestamp) {
                            // Calculate pv_p if it's missing but we have grid_p and battery_p
                            let pvValue = 0;
                            if (row.pv_p !== undefined) {
                                pvValue = parseFloat(row.pv_p);
                            } else if (row.grid_p !== undefined && row.battery_p !== undefined) {
                                // Approximate PV = Grid + Battery (when battery is negative/charging)
                                const gridP = parseFloat(row.grid_p);
                                const batteryP = parseFloat(row.battery_p);
                                // If battery is charging (negative), grid supplies both load and battery
                                // If battery is discharging (positive), battery helps grid supply load
                                pvValue = Math.max(0, gridP + (batteryP < 0 ? Math.abs(batteryP) : -batteryP));
                            }
                            
                            energyData[timestamp] = {
                                pv_p: pvValue,
                                battery_p: parseFloat(row.battery_p ?? 0),
                                grid_p: parseFloat(row.grid_p ?? 0)
                            };
                        }
                    } catch (e) {
                        console.error('Error processing data row:', e, row);
                    }
                });
            } else {
                console.error('aggregatedSnapshots is not iterable:', aggregatedSnapshots);
            }
        }
    }
    
    // Construct battery price data from aggregated snapshots if needed
    if (!batteryPriceData || Object.keys(batteryPriceData).length === 0) {
        console.log('Using aggregated_data_snapshots for battery price/power chart');
        batteryPriceData = {};
        
        // Generate demo data if no real data exists (for testing purposes)
        if (aggregatedSnapshots.length === 0) {
            const now = new Date();
            for (let i = 0; i < 24; i++) {
                const timestamp = new Date(now.getTime() - (23-i) * 3600 * 1000).toISOString();
                batteryPriceData[timestamp] = {
                    battery_p: Math.random() * 3000 * (Math.random() > 0.5 ? 1 : -1),
                    tariff: 0.15 + Math.random() * 0.1
                };
            }
        } else {
            // Process real data
            if (aggregatedSnapshots.forEach) {
                console.log('Processing', aggregatedSnapshots.length, 'aggregated data snapshots for battery price');
                // Process real data
                aggregatedSnapshots.forEach(row => {
                    try {
                        // Check for dt (unix timestamp) or timestamp or time fields
                        let timestamp;
                        if (row.timestamp) {
                            timestamp = row.timestamp;
                        } else if (row.dt) {
                            // Convert unix timestamp to ISO string
                            timestamp = new Date(row.dt * 1000).toISOString();
                        } else if (row.time) {
                            timestamp = new Date(row.time).toISOString();
                        }
                        
                        if (timestamp) {
                            batteryPriceData[timestamp] = {
                                battery_p: parseFloat(row.battery_p ?? 0),
                                tariff: parseFloat(row.tariff ?? 0.15) // Default tariff if missing
                            };
                        }
                    } catch (e) {
                        console.error('Error processing battery price data row:', e, row);
                    }
                });
            } else {
                console.error('aggregatedSnapshots is not iterable for battery price data:', aggregatedSnapshots);
            }
        }
    }
    
    // Construct battery savings data from aggregated snapshots if needed
    if (!batterySavingsData || Object.keys(batterySavingsData).length === 0) {
        console.log('Using aggregated_data_snapshots for battery savings chart');
        batterySavingsData = {};
        
        // Generate demo data if no real data exists (for testing purposes)
        if (aggregatedSnapshots.length === 0) {
            const now = new Date();
            for (let i = 0; i < 24; i++) {
                const timestamp = new Date(now.getTime() - (23-i) * 3600 * 1000).toISOString();
                batterySavingsData[timestamp] = {
                    battery_savings: Math.random() * 0.5 * (Math.random() > 0.3 ? 1 : -0.2)
                };
            }
        } else {
            // Process real data
            if (aggregatedSnapshots.forEach) {
                console.log('Processing', aggregatedSnapshots.length, 'aggregated data snapshots for battery savings');
                // Process real data
                aggregatedSnapshots.forEach(row => {
                    try {
                        // Check for dt (unix timestamp) or timestamp or time fields
                        let timestamp;
                        if (row.timestamp) {
                            timestamp = row.timestamp;
                        } else if (row.dt) {
                            // Convert unix timestamp to ISO string
                            timestamp = new Date(row.dt * 1000).toISOString();
                        } else if (row.time) {
                            timestamp = new Date(row.time).toISOString();
                        }
                        
                        if (timestamp) {
                            let savings = row.battery_savings !== undefined ? parseFloat(row.battery_savings) : null;
                            
                            // Calculate savings if missing but battery power and tariff are available
                            if (savings === null && row.battery_p !== undefined) {
                                const batteryPower = parseFloat(row.battery_p);
                                // Use tariff from row or default to 0.15 if not available
                                const tariff = row.tariff !== undefined ? parseFloat(row.tariff) : 0.15;
                                
                                // For this chart, we want to show savings for both charging and discharging
                                // negative battery power (charging) can also generate value in time-of-use tariff scenarios
                                const powerForSavings = Math.abs(batteryPower); // Use absolute value
                                if (powerForSavings > 0) {
                                    savings = (powerForSavings / 1000) * tariff * 0.2; // Assuming 20% efficiency gain
                                } else {
                                    savings = 0;
                                }
                            }
                            
                            batterySavingsData[timestamp] = {
                                battery_savings: savings || 0
                            };
                        }
                    } catch (e) {
                        console.error('Error processing battery savings data row:', e, row);
                    }
                });
            } else {
                console.error('aggregatedSnapshots is not iterable for battery savings data:', aggregatedSnapshots);
            }
        }
    }
    
    // --- RENDER ENERGY CHART ---
    if (Object.keys(energyData).length > 0) {
        console.log('Rendering energy chart with data:', Object.keys(energyData).length, 'entries');
        try {
            const entries = Object.entries(energyData).sort(([a], [b]) => {
                // Handle different timestamp formats for sorting
                const dateA = typeof a === 'number' || /^\d+$/.test(a) ? 
                    new Date(parseInt(a) * 1000) : new Date(a);
                const dateB = typeof b === 'number' || /^\d+$/.test(b) ? 
                    new Date(parseInt(b) * 1000) : new Date(b);
                return dateA - dateB;
            });
            
            const labels = entries.map(([ts]) => formatLabelDate(ts));
            const pvData = entries.map(([, v]) => v.pv_p / 1000);
            const batteryData = entries.map(([, v]) => v.battery_p / 1000);
            const gridData = entries.map(([, v]) => v.grid_p / 1000);
        
        new Chart(document.getElementById('energyChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'PV (kW)', 
                        data: pvData,
                        borderColor: 'rgba(0,123,255,1)', 
                        backgroundColor: 'rgba(0,123,255,0.15)', 
                        fill: true, 
                        pointRadius: 2, 
                        pointHoverRadius: 12
                    },
                    {
                        label: 'Battery (kW)', 
                        data: batteryData,
                        borderColor: 'rgba(220,53,69,1)', 
                        backgroundColor: 'rgba(220,53,69,0.12)', 
                        fill: true, 
                        pointRadius: 2, 
                        pointHoverRadius: 12
                    },
                    {
                        label: 'Grid (kW)', 
                        data: gridData,
                        borderColor: 'rgba(40,167,69,1)', 
                        backgroundColor: 'rgba(40,167,69,0.12)', 
                        fill: true, 
                        pointRadius: 2, 
                        pointHoverRadius: 12
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top', padding: 30 } },
                interaction: { mode: 'index', intersect: false },
                elements: { point: { radius: 4, hoverRadius: 12 } },
                scales: {
                    y: {
                        title: {
                            display: true,
                            text: 'Power (kW)'
                        },
                        ticks: { font: { size: 14 } },
                        grid: {
                            lineWidth: 1,
                            color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                        }
                    },
                    x: { ticks: { font: { size: 14 } }, border: { display: true, width: 4 } }
                }
            }
        });
        
        // Fill Energy Data Table
        const energyTable = document.getElementById('energyDataTableBody');
        if (energyTable) {
            energyTable.innerHTML = '';
            entries.forEach(([ts, val]) => {
                energyTable.innerHTML += `
                <tr>
                    <td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td>
                    <td class="px-4 py-2 text-center">${(val.pv_p / 1000).toFixed(2)}</td>
                    <td class="px-4 py-2 text-center">${(val.battery_p / 1000).toFixed(2)}</td>
                    <td class="px-4 py-2 text-center">${(val.grid_p / 1000).toFixed(2)}</td>
                </tr>`;
            });
        }
        } catch (e) {
            console.error('Error rendering energy chart:', e);
            document.getElementById('energyChart').parentElement.innerHTML = 
                '<div class="text-center text-gray-400 py-12">Error rendering energy chart. Check console for details.</div>';
        }
    } else {
        console.log('No energy chart data to render');
        document.getElementById('energyChart').parentElement.innerHTML = '<div class="text-center text-gray-400 py-12">No energy chart data available.</div>';
    }
    
    // --- RENDER BATTERY POWER/PRICE CHART ---
    if (Object.keys(batteryPriceData).length > 0) {
        console.log('Rendering battery price chart with data:', Object.keys(batteryPriceData).length, 'entries');
        try {
            const entries = Object.entries(batteryPriceData).sort(([a], [b]) => {
                // Handle different timestamp formats for sorting
                const dateA = typeof a === 'number' || /^\d+$/.test(a) ? 
                    new Date(parseInt(a) * 1000) : new Date(a);
                const dateB = typeof b === 'number' || /^\d+$/.test(b) ? 
                    new Date(parseInt(b) * 1000) : new Date(b);
                return dateA - dateB;
            });
            
            const labels = entries.map(([ts]) => formatLabelDate(ts));
            const batteryData = entries.map(([, v]) => v.battery_p / 1000);
            const tariffData = entries.map(([, v]) => v.tariff);
        
        const batteryCtx = document.getElementById('batteryChart');
        if (batteryCtx) {
            new Chart(batteryCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Battery Power (kW)',
                            data: batteryData,
                            borderColor: 'rgba(220,53,69,1)',
                            backgroundColor: 'rgba(220,53,69,0.1)',
                            fill: true,
                            yAxisID: 'y',
                            pointRadius: 2,
                            pointHoverRadius: 12
                        },
                        {
                            label: 'Energy Price (€/kWh)',
                            data: tariffData,
                            borderColor: 'rgba(75,192,192,1)',
                            backgroundColor: 'rgba(75,192,192,0.1)',
                            fill: false,
                            yAxisID: 'y1',
                            pointRadius: 2,
                            pointHoverRadius: 12
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Battery Power (kW)'
                            },
                            grid: {
                                lineWidth: 1,
                                color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Energy Price (€/kWh)'
                            },
                            grid: { display: false }
                        }
                    }
                }
            });
                
                // Fill Battery Data Table
                const batteryTable = document.getElementById('batteryDataTableBody');
                if (batteryTable) {
                    batteryTable.innerHTML = '';
                    entries.forEach(([ts, val]) => {
                        batteryTable.innerHTML += `
                        <tr>
                            <td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td>
                            <td class="px-4 py-2 text-center">${(val.battery_p / 1000).toFixed(2)}</td>
                            <td class="px-4 py-2 text-center">${val.tariff.toFixed(4)}</td>
                        </tr>`;
                    });
                }
            } else {
                console.error("Battery chart canvas element not found");
            }
        } catch (e) {
            console.error('Error rendering battery chart:', e);
            const batteryChartElement = document.getElementById('batteryChart');
            if (batteryChartElement) {
                batteryChartElement.parentElement.innerHTML = 
                    '<div class="text-center text-gray-400 py-12">Error rendering battery chart. Check console for details.</div>';
            }
        }
    } else {
        console.log('No battery price data to render');
        const batteryChartElement = document.getElementById('batteryChart');
        if (batteryChartElement) {
            batteryChartElement.parentElement.innerHTML = '<div class="text-center text-gray-400 py-12">No battery power data available.</div>';
        }
    }
    
    // --- RENDER BATTERY SAVINGS CHART ---
    if (Object.keys(batterySavingsData).length > 0) {
        console.log('Rendering battery savings chart with data:', Object.keys(batterySavingsData).length, 'entries');
        try {
            // BATTERY SAVINGS CHART
            const savingsEntries = Object.entries(batterySavingsData).sort(([a], [b]) => {
                // Handle different timestamp formats for sorting
                const dateA = typeof a === 'number' || /^\d+$/.test(a) ? 
                    new Date(parseInt(a) * 1000) : new Date(a);
                const dateB = typeof b === 'number' || /^\d+$/.test(b) ? 
                    new Date(parseInt(b) * 1000) : new Date(b);
                return dateA - dateB;
            });
            
            const savingsLabels = savingsEntries.map(([ts]) => formatLabelDate(ts));
            const savingsData = savingsEntries.map(([, v]) => v.battery_savings);
            const totalSavings = savingsData.reduce((acc, val) => acc + parseFloat(val || 0), 0);
        
        const batterySavingsTotal = document.getElementById('batterySavingsTotal');
        if (batterySavingsTotal) {
            batterySavingsTotal.textContent = `Your savings today: € ${totalSavings.toFixed(2)}`;
        }

        const savingsChart = document.getElementById('savingsChart');
        if (savingsChart) {
            new Chart(savingsChart, {
                type: 'bar',
                data: {
                    labels: savingsLabels,
                    datasets: [{
                        label: 'Battery Savings (€)',
                        data: savingsData,
                        backgroundColor: savingsData.map(val => val >= 0 ? 'rgba(25,135,84,0.7)' : 'rgba(220,53,69,0.7)'),
                        hoverBackgroundColor: savingsData.map(val => val >= 0 ? 'rgba(25,135,84,1)' : 'rgba(220,53,69,1)')
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false} },
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: { 
                            title: {
                                display: true,
                                text: 'Battery Savings (€)'
                            },
                            type: 'linear', 
                            position: 'left', 
                            ticks: { font: { size: 14 } },
                            grid: {
                                lineWidth: 1,
                                color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc'
                            },
                        },
                        x: { ticks: { font: { size: 14 } } }
                    }
                }
            });

            // Fill Savings Data Table
            const savingsTable = document.getElementById('batterySavingsDataTableBody');
            if (savingsTable) {
                savingsTable.innerHTML = '';
                savingsEntries.forEach(([ts, val]) => {
                    savingsTable.innerHTML += `
                    <tr>
                        <td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td>
                        <td class="px-4 py-2 text-center">${parseFloat(val.battery_savings).toFixed(2)}</td>
                    </tr>`;
                });
            }
        } else {
            console.error("Savings chart canvas element not found");
        }
        } catch (e) {
            console.error('Error rendering savings chart:', e);
            const savingsChartElement = document.getElementById('savingsChart');
            if (savingsChartElement) {
                savingsChartElement.parentElement.innerHTML = 
                    '<div class="text-center text-gray-400 py-12">Error rendering battery savings chart. Check console for details.</div>';
            }
        }
    } else {
        console.log('No battery savings data to render');
        const savingsChartElement = document.getElementById('savingsChart');
        if (savingsChartElement) {
            savingsChartElement.parentElement.innerHTML = '<div class="text-center text-gray-400 py-12">No battery savings data available.</div>';
        }
    }
});

// --- CALENDAR WIDGET LOGIC ---
document.addEventListener('DOMContentLoaded', function() {
    // Use the global plantId variable instead of redefining it
    console.log('[Calendar] Plant ID:', window.plantId);
    
    // Ensure plant ID is available, try to extract from URL if needed
    if (!window.plantId) {
        const pathSegments = window.location.pathname.split('/');
        for (let i = 0; i < pathSegments.length; i++) {
            if (pathSegments[i] === 'plants' && i + 1 < pathSegments.length) {
                window.plantId = pathSegments[i + 1];
                console.log('[Calendar] Recovered plant ID from URL:', window.plantId);
                break;
            }
        }
    }
    
    const dateInput = document.getElementById('energy-date');
    const prevBtn = document.getElementById('energy-prev');
    const nextBtn = document.getElementById('energy-next');
    const todayBtn = document.getElementById('energy-today');
    const yesterdayBtn = document.getElementById('energy-yesterday');

    // Set default date to today if empty
    if (dateInput && !dateInput.value) {
        const today = new Date();
        dateInput.value = today.toISOString().slice(0, 10);
    }

    // Store chart instances for proper destruction
    let chartInstances = {};

    function fetchAndUpdateCharts(dateStr) {
        // Check for plant ID and handle the error case
        if (!window.plantId) {
            console.error('[Calendar] Cannot fetch data: Plant ID is missing');
            showNotification('Cannot fetch data: Plant ID is missing', 'error');
            
            // One last attempt to get the plant ID from the URL
            const pathSegments = window.location.pathname.split('/');
            for (let i = 0; i < pathSegments.length; i++) {
                if (pathSegments[i] === 'plants' && i + 1 < pathSegments.length) {
                    window.plantId = pathSegments[i + 1];
                    console.log('[Calendar] Recovered plant ID from URL:', window.plantId);
                    break;
                }
            }
            
            // If still no plant ID, we can't continue
            if (!window.plantId) {
                return;
            }
        }
        
        // Show loading indicator
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.classList.remove('hidden');
            console.log('[Calendar] Showing loading indicator');
        }
        
        // Convert selected date to Unix timestamps (start/end of day)
        let selectedDate;
        try {
            console.log(`[Calendar] Processing date input: "${dateStr}"`);
            
            // Handle different date string formats
            if (dateStr.includes('-')) {
                selectedDate = new Date(dateStr);
                console.log(`[Calendar] Date parsed from ISO format: ${selectedDate}`);
            } else {
                // Handle YYYYMMDD format
                selectedDate = new Date(dateStr.substring(0, 4) + '-' + dateStr.substring(4, 6) + '-' + dateStr.substring(6, 8));
                console.log(`[Calendar] Date parsed from YYYYMMDD format: ${selectedDate}`);
            }
            
            // If invalid date, default to today
            if (isNaN(selectedDate.getTime())) {
                console.error(`[Calendar] Invalid date: ${dateStr}, defaulting to today`);
                selectedDate = new Date();
            }
        } catch (e) {
            console.error(`[Calendar] Error parsing date: ${dateStr}`, e);
            selectedDate = new Date();
        }
        
        // Calculate start/end timestamps (Unix seconds)
        const startOfDay = Math.floor(new Date(selectedDate).setHours(0, 0, 0, 0) / 1000);
        const endOfDay = Math.floor(new Date(selectedDate).setHours(23, 59, 59, 999) / 1000);
        
        console.log(`[Calendar] === FETCHING DATA FOR ${selectedDate.toDateString()} ===`);
        console.log(`[Calendar] Date parameters:`, {
            rawInputDate: dateStr,
            parsedDate: selectedDate.toString(),
            startTimestamp: startOfDay,
            endTimestamp: endOfDay,
            startFormatted: new Date(startOfDay * 1000).toISOString(),
            endFormatted: new Date(endOfDay * 1000).toISOString(),
            plantId: window.plantId
        });
        
        // Start the timer for measuring fetch duration
        console.time('[Calendar] Data fetch duration');
        
        // Double-check plantId before making API call
        if (!window.plantId) {
            console.error('[Calendar] Fatal: Plant ID is still missing before API call');
            showNotification('Error: Unable to determine plant ID. Please reload the page.', 'error');
            
            // Hide loading indicator
            if (loadingIndicator) {
                loadingIndicator.classList.add('hidden');
            }
            return;
        }

        const url = `/plants/${window.plantId}/data?start=${startOfDay}&end=${endOfDay}`;
        console.log(`[Calendar] Fetching data from: ${url}`);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(res => {
                console.log(`[Calendar] API response status: ${res.status} ${res.statusText}`);
                if (!res.ok) {
                    // Handle specific error cases
                    if (res.status === 404) {
                        throw new Error(`Plant not found (ID: ${window.plantId}). Check that the plant ID is correct.`);
                    } else if (res.status === 403) {
                        throw new Error('Permission denied: You do not have access to this plant data.');
                    } else {
                        throw new Error(`Network response error: ${res.status} ${res.statusText}`);
                    }
                }
                return res.json().catch(err => {
                    console.error('[Calendar] Error parsing JSON response:', err);
                    throw new Error('Invalid JSON response from server');
                });
            })
            .then(data => {
                console.log(`[Calendar] Data received successfully for ${selectedDate.toDateString()}`);
                
                // Validate response format
                if (!data) {
                    throw new Error('Empty response from server');
                }
                
                // Log data statistics
                console.log('[Calendar] Data statistics:', {
                    energyDataPoints: Object.keys(data.energy_chart || {}).length,
                    batteryDataPoints: Object.keys(data.battery_price || {}).length,
                    savingsDataPoints: Object.keys(data.battery_savings || {}).length,
                    responseSize: JSON.stringify(data).length + ' bytes'
                });
                
                // Log detailed data for debugging
                console.log(`[Calendar] === DETAILED FETCHED DATA (${selectedDate.toLocaleDateString()}) ===`);
                console.log('[Calendar] Energy Chart Data:', data.energy_chart);
                console.log('[Calendar] Battery Price Data:', data.battery_price);
                console.log('[Calendar] Battery Savings Data:', data.battery_savings);
                
                // Log sample data points for each chart type
                const logSampleData = (dataObj, label) => {
                    const keys = Object.keys(dataObj || {});
                    if (keys.length > 0) {
                        const sampleKey = keys[0];
                        console.log(`[Calendar] Sample ${label} data point:`, {
                            timestamp: sampleKey,
                            formattedTime: formatLabelDate(sampleKey),
                            values: dataObj[sampleKey]
                        });
                    } else {
                        console.warn(`[Calendar] No data points found for ${label} chart`);
                    }
                };
                
                logSampleData(data.energy_chart, 'Energy');
                logSampleData(data.battery_price, 'Battery Price');
                logSampleData(data.battery_savings, 'Battery Savings');
                
                // Update chart data variables and log the update
                console.log('[Calendar] Updating chart data variables with fetched data');
                
                const previousEnergyCount = Object.keys(window.energyData || {}).length;
                const previousBatteryCount = Object.keys(window.batteryPriceData || {}).length;
                const previousSavingsCount = Object.keys(window.batterySavingsData || {}).length;
                
                // Make sure we're updating the global variables
                window.energyData = data.energy_chart || {};
                window.batteryPriceData = data.battery_price || {};
                window.batterySavingsData = data.battery_savings || {};
                
                // Log the changes in data points
                console.log('[Calendar] Data points changed:', {
                    energy: {
                        before: previousEnergyCount,
                        after: Object.keys(window.energyData).length,
                        difference: Object.keys(window.energyData).length - previousEnergyCount
                    },
                    batteryPrice: {
                        before: previousBatteryCount,
                        after: Object.keys(window.batteryPriceData).length,
                        difference: Object.keys(window.batteryPriceData).length - previousBatteryCount
                    },
                    batterySavings: {
                        before: previousSavingsCount,
                        after: Object.keys(window.batterySavingsData).length,
                        difference: Object.keys(window.batterySavingsData).length - previousSavingsCount
                    }
                });
                
                // Remove old charts if they exist
                console.log('[Calendar] Removing old chart instances');
                ['energyChart','batteryChart','savingsChart'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el && el.chartInstance) {
                        console.log(`[Calendar] Destroying chart instance: ${id}`);
                        el.chartInstance.destroy();
                        el.chartInstance = null;
                    }
                });
                
                // Re-render charts
                console.log('[Calendar] Calling renderAllCharts() to update the UI');
                renderAllCharts();
                
                // Hide loading indicator
                if (loadingIndicator) {
                    loadingIndicator.classList.add('hidden');
                    console.log('[Calendar] Hiding loading indicator');
                }
                
                // Notify user of success
                showNotification(`Data loaded for ${selectedDate.toLocaleDateString()}`, 'success');
                
                console.timeEnd('[Calendar] Data fetch duration');
                console.log(`[Calendar] === END OF DATA FETCH FOR ${selectedDate.toDateString()} ===`);
            })
            .catch(err => {
                console.error(`[Calendar] Error fetching plant data for date ${selectedDate.toDateString()}:`, err);
                
                // Show detailed error notification based on error type
                let errorMsg;
                if (err.message.includes('Plant not found')) {
                    errorMsg = `Plant ID issue: ${err.message}`;
                } else if (err.message.includes('Permission denied')) {
                    errorMsg = `Access denied: ${err.message}`;
                } else {
                    errorMsg = `Failed to load data for ${selectedDate.toLocaleDateString()}: ${err.message}`;
                }
                
                showNotification(errorMsg, 'error');
                
                // Hide loading indicator
                if (loadingIndicator) {
                    loadingIndicator.classList.add('hidden');
                    console.log('[Calendar] Hiding loading indicator after error');
                }
                
                // Update dashboard to show error state
                const chartElements = ['energyChart', 'batteryChart', 'savingsChart'];
                chartElements.forEach(chartId => {
                    const chartElement = document.getElementById(chartId);
                    if (chartElement && chartElement.parentElement) {
                        chartElement.parentElement.innerHTML = 
                            `<div class="text-center py-8 px-4">
                                <div class="text-red-500 text-xl mb-2">⚠️ Data Loading Error</div>
                                <p class="text-gray-600 mb-4">${err.message}</p>
                                <p class="text-sm text-gray-500">
                                    Try refreshing the page or selecting a different date.
                                </p>
                            </div>`;
                    }
                });
                
                console.timeEnd('[Calendar] Data fetch duration');
                console.log(`[Calendar] === FETCH ERROR FOR ${selectedDate.toDateString()} ===`);
            });
    }

    function renderAllCharts() {
        console.log('[Calendar] === RENDERING ALL CHARTS ===');
        console.log('[Calendar] Chart data summary:', {
            energyDataPoints: Object.keys(window.energyData || {}).length,
            batteryPriceDataPoints: Object.keys(window.batteryPriceData || {}).length,
            batterySavingsDataPoints: Object.keys(window.batterySavingsData || {}).length
        });
        
        // --- ENERGY CHART ---
        if (typeof Chart !== 'undefined') {
            // Remove and recreate canvases to avoid Chart.js errors
            ['energyChart','batteryChart','savingsChart'].forEach(id => {
                const old = document.getElementById(id);
                if (old) {
                    const parent = old.parentElement;
                    const newCanvas = document.createElement('canvas');
                    newCanvas.id = id;
                    newCanvas.className = old.className;
                    parent.replaceChild(newCanvas, old);
                }
            });
            // Copy the chart rendering logic from the main block
            // --- ENERGY CHART RENDER ---
            try {
                console.log('[Calendar] Rendering energy chart with data points:', Object.keys(window.energyData || {}).length);
                const entries = Object.entries(window.energyData || {}).sort(([a], [b]) => new Date(a) - new Date(b));
                const labels = entries.map(([ts]) => formatLabelDate(ts));
                const pvData = entries.map(([, v]) => v.pv_p / 1000);
                const batteryData = entries.map(([, v]) => v.battery_p / 1000);
                const gridData = entries.map(([, v]) => v.grid_p / 1000);
                const ctx = document.getElementById('energyChart');
                if (ctx) {
                    ctx.chartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [
                                { label: 'PV (kW)', data: pvData, borderColor: 'rgba(0,123,255,1)', backgroundColor: 'rgba(0,123,255,0.15)', fill: true, pointRadius: 2, pointHoverRadius: 12 },
                                { label: 'Battery (kW)', data: batteryData, borderColor: 'rgba(220,53,69,1)', backgroundColor: 'rgba(220,53,69,0.12)', fill: true, pointRadius: 2, pointHoverRadius: 12 },
                                { label: 'Grid (kW)', data: gridData, borderColor: 'rgba(40,167,69,1)', backgroundColor: 'rgba(40,167,69,0.12)', fill: true, pointRadius: 2, pointHoverRadius: 12 }
                            ]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top', padding: 30 } }, interaction: { mode: 'index', intersect: false }, elements: { point: { radius: 4, hoverRadius: 12 } }, scales: { y: { title: { display: true, text: 'Power (kW)' }, ticks: { font: { size: 14 } }, grid: { lineWidth: 1, color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc' } }, x: { ticks: { font: { size: 14 } }, border: { display: true, width: 4 } } } }
                    });
                }
                // Fill Energy Data Table
                const energyTable = document.getElementById('energyDataTableBody');
                if (energyTable) {
                    energyTable.innerHTML = '';
                    entries.forEach(([ts, val]) => {
                        energyTable.innerHTML += `<tr><td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td><td class="px-4 py-2 text-center">${(val.pv_p / 1000).toFixed(2)}</td><td class="px-4 py-2 text-center">${(val.battery_p / 1000).toFixed(2)}</td><td class="px-4 py-2 text-center">${(val.grid_p / 1000).toFixed(2)}</td></tr>`;
                    });
                }
            } catch (e) { console.error('Error rendering energy chart:', e); }

            // --- BATTERY CHART RENDER ---
            try {
                console.log('[Calendar] Rendering battery chart with data points:', Object.keys(window.batteryPriceData || {}).length);
                const entries = Object.entries(window.batteryPriceData || {}).sort(([a], [b]) => new Date(a) - new Date(b));
                const labels = entries.map(([ts]) => formatLabelDate(ts));
                const batteryData = entries.map(([, v]) => v.battery_p / 1000);
                const tariffData = entries.map(([, v]) => v.tariff);
                const ctx = document.getElementById('batteryChart');
                if (ctx) {
                    ctx.chartInstance = new Chart(ctx, {
                        type: 'line',
                        data: { labels, datasets: [ { label: 'Battery Power (kW)', data: batteryData, borderColor: 'rgba(220,53,69,1)', backgroundColor: 'rgba(220,53,69,0.1)', fill: true, yAxisID: 'y', pointRadius: 2, pointHoverRadius: 12 }, { label: 'Energy Price (€/kWh)', data: tariffData, borderColor: 'rgba(75,192,192,1)', backgroundColor: 'rgba(75,192,192,0.1)', fill: false, yAxisID: 'y1', pointRadius: 2, pointHoverRadius: 12 } ] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } }, interaction: { mode: 'index', intersect: false }, scales: { y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Battery Power (kW)' }, grid: { lineWidth: 1, color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc' } }, y1: { type: 'linear', display: true, position: 'right', title: { display: true, text: 'Energy Price (€/kWh)' }, grid: { display: false } } } }
                    });
                }
                // Fill Battery Data Table
                const batteryTable = document.getElementById('batteryDataTableBody');
                if (batteryTable) {
                    batteryTable.innerHTML = '';
                    entries.forEach(([ts, val]) => {
                        batteryTable.innerHTML += `<tr><td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td><td class="px-4 py-2 text-center">${(val.battery_p / 1000).toFixed(2)}</td><td class="px-4 py-2 text-center">${val.tariff.toFixed(4)}</td></tr>`;
                    });
                }
            } catch (e) { console.error('Error rendering battery chart:', e); }

            // --- BATTERY SAVINGS CHART RENDER ---
            try {
                console.log('[Calendar] Rendering savings chart with data points:', Object.keys(window.batterySavingsData || {}).length);
                const entries = Object.entries(window.batterySavingsData || {}).sort(([a], [b]) => new Date(a) - new Date(b));
                const labels = entries.map(([ts]) => formatLabelDate(ts));
                const savingsData = entries.map(([, v]) => v.battery_savings);
                const totalSavings = savingsData.reduce((acc, val) => acc + parseFloat(val || 0), 0);
                const ctx = document.getElementById('savingsChart');
                if (ctx) {
                    ctx.chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: { labels, datasets: [{ label: 'Battery Savings (€)', data: savingsData, backgroundColor: savingsData.map(val => val >= 0 ? 'rgba(25,135,84,0.7)' : 'rgba(220,53,69,0.7)'), hoverBackgroundColor: savingsData.map(val => val >= 0 ? 'rgba(25,135,84,1)' : 'rgba(220,53,69,1)') }] },
                        options: { responsive: true, plugins: { legend: { display: false } }, interaction: { mode: 'index', intersect: false }, scales: { y: { title: { display: true, text: 'Battery Savings (€)' }, type: 'linear', position: 'left', ticks: { font: { size: 14 } }, grid: { lineWidth: 1, color: context => context.tick && context.tick.value === 0 ? '#000' : '#ccc' } }, x: { ticks: { font: { size: 14 } } } } }
                    });
                }
                // Fill Savings Data Table
                const savingsTable = document.getElementById('batterySavingsDataTableBody');
                if (savingsTable) {
                    savingsTable.innerHTML = '';
                    entries.forEach(([ts, val]) => {
                        savingsTable.innerHTML += `<tr><td class="px-4 py-2 text-center">${formatLabelDate(ts)}</td><td class="px-4 py-2 text-center">${parseFloat(val.battery_savings).toFixed(2)}</td></tr>`;
                    });
                }
                const batterySavingsTotal = document.getElementById('batterySavingsTotal');
                if (batterySavingsTotal) {
                    batterySavingsTotal.textContent = `Your savings: € ${totalSavings.toFixed(2)}`;
                }
            } catch (e) { console.error('Error rendering savings chart:', e); }
        }
    }

    if (dateInput) {
        // Set max date to today
        const today = new Date();
        dateInput.max = today.toISOString().slice(0, 10);
        
        dateInput.addEventListener('change', function() {
            console.log(`[Calendar] Date input change detected: ${this.value}`);
            
            // Validate selected date is not in the future
            const selectedDate = new Date(this.value);
            const currentDate = new Date();
            currentDate.setHours(0, 0, 0, 0);
            
            if (selectedDate > currentDate) {
                const todayStr = currentDate.toISOString().slice(0, 10);
                console.log(`[Calendar] Future date selected (${this.value}), resetting to today (${todayStr})`);
                showNotification('Cannot select future dates', 'info');
                this.value = todayStr;
            }
            
            const formattedDate = this.value.replace(/-/g, '');
            console.log(`[Calendar] Date changed to: ${this.value} (${formattedDate})`);
            console.log(`[Calendar] Fetching data for date: ${selectedDate.toDateString()}`);
            
            // Call the fetch function with the formatted date
            try {
                fetchAndUpdateCharts(formattedDate);
            } catch (err) {
                console.error('[Calendar] Error while fetching and updating charts:', err);
                showNotification('Error updating charts. See console for details.', 'error');
            }
        });
        
        // Trigger change event on page load to fetch today's data by default
        console.log('[Calendar] ===== INITIAL PAGE LOAD - FETCHING DEFAULT DATE DATA =====');
        console.log(`[Calendar] Default date set to: ${dateInput.value}`);
        
        // Add a small delay to ensure the DOM is fully loaded before fetching data
        setTimeout(() => {
            try {
                console.log('[Calendar] Dispatching date change event for initial data load');
                dateInput.dispatchEvent(new Event('change'));
            } catch (err) {
                console.error('[Calendar] Error during initial data load:', err);
                showNotification('Error loading initial data. Please try refreshing the page.', 'error');
            }
        }, 100);
    }
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (!dateInput.value) return;
            try {
                const d = new Date(dateInput.value);
                if (isNaN(d.getTime())) throw new Error('Invalid date');
                
                d.setDate(d.getDate() - 1);
                dateInput.value = d.toISOString().slice(0, 10);
                console.log(`[Calendar] Previous button clicked. Set date to: ${dateInput.value}`);
                dateInput.dispatchEvent(new Event('change'));
            } catch (e) {
                console.error('[Calendar] Error navigating to previous day:', e);
                showNotification('Could not navigate to previous day due to invalid date', 'error');
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (!dateInput.value) return;
            try {
                const d = new Date(dateInput.value);
                if (isNaN(d.getTime())) throw new Error('Invalid date');
                
                // Don't allow selecting future dates
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                const nextDay = new Date(d);
                nextDay.setDate(nextDay.getDate() + 1);
                nextDay.setHours(0, 0, 0, 0);
                
                if (nextDay > today) {
                    console.log('[Calendar] Attempted to navigate beyond today');
                    showNotification('Cannot select future dates', 'info');
                    return;
                }
                
                dateInput.value = nextDay.toISOString().slice(0, 10);
                console.log(`[Calendar] Next button clicked. Set date to: ${dateInput.value}`);
                dateInput.dispatchEvent(new Event('change'));
            } catch (e) {
                console.error('[Calendar] Error navigating to next day:', e);
                showNotification('Could not navigate to next day due to invalid date', 'error');
            }
        });
    }
    
    if (todayBtn) {
        todayBtn.addEventListener('click', function() {
            const today = new Date();
            dateInput.value = today.toISOString().slice(0, 10);
            console.log(`[Calendar] Today button clicked. Set date to: ${dateInput.value}`);
            showNotification('Showing data for today: ' + today.toLocaleDateString(), 'success');
            dateInput.dispatchEvent(new Event('change'));
        });
    }
    
    if (yesterdayBtn) {
        yesterdayBtn.addEventListener('click', function() {
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            dateInput.value = yesterday.toISOString().slice(0, 10);
            console.log(`[Calendar] Yesterday button clicked. Set date to: ${dateInput.value}`);
            showNotification('Showing data for yesterday: ' + yesterday.toLocaleDateString(), 'success');
            dateInput.dispatchEvent(new Event('change'));
        });
    }
});
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
