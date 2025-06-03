<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <!-- EMS Chart 1: Energy Live Chart -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Energy Live Chart</h2>
                <canvas id="energyLiveChart" class="w-full h-64"></canvas>
            </div>
            <!-- EMS Chart 2: Battery Chart -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Battery Chart</h2>
                <canvas id="batteryChart" class="w-full h-64"></canvas>
            </div>
            <!-- EMS Chart 3: Battery Savings Chart -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Battery Savings Chart</h2>
                <canvas id="batterySavingsChart" class="w-full h-64"></canvas>
            </div>
        </div>
        <!-- Site Traffic Chart below EMS charts -->
        <div class="grid grid-cols-1 gap-8 mb-8">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Site Traffic</h2>
                <canvas id="adminChart" class="w-full h-64"></canvas>
            </div>
        </div>
        <!-- Recent Activity Table -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Recent Activity</h2>
            <table class="w-full text-sm border rounded bg-white dark:bg-gray-900 dark:text-gray-100">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Action</th>
                        <th class="px-4 py-2 text-left">User</th>
                        <th class="px-4 py-2 text-left">Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-4 py-2">Logged in</td>
                        <td class="px-4 py-2">admin@example.com</td>
                        <td class="px-4 py-2">09:01</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2">Edited Plant</td>
                        <td class="px-4 py-2">user1@example.com</td>
                        <td class="px-4 py-2">08:55</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2">Added Device</td>
                        <td class="px-4 py-2">user2@example.com</td>
                        <td class="px-4 py-2">08:40</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Users List -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Users</h2>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                <li class="py-3 flex items-center">
                    <span class="flex-1 text-gray-900 dark:text-gray-100">admin@example.com</span>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Admin</span>
                </li>
                <li class="py-3 flex items-center">
                    <span class="flex-1 text-gray-900 dark:text-gray-100">user1@example.com</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">User</span>
                </li>
                <li class="py-3 flex items-center">
                    <span class="flex-1 text-gray-900 dark:text-gray-100">user2@example.com</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">User</span>
                </li>
            </ul>
        </div>
    </div>
    <!-- Chart.js loader -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Energy Live Chart
        fetch('/energy_live_chart.json')
            .then(res => res.json())
            .then(data => {
                if (!data || !data.labels) {
                    data = {
                        labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                        datasets: [{
                            label: 'Energy (kWh)',
                            data: [10, 15, 20, 18, 22, 17, 12],
                            borderColor: 'rgba(34,197,94,1)',
                            backgroundColor: 'rgba(34,197,94,0.15)',
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 8
                        }]
                    };
                }
                new Chart(document.getElementById('energyLiveChart'), {
                    type: 'line',
                    data: data,
                    options: { responsive: true, plugins: { legend: { display: true } } }
                });
            }).catch(() => {
                // fallback static data
                new Chart(document.getElementById('energyLiveChart'), {
                    type: 'line',
                    data: {
                        labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                        datasets: [{
                            label: 'Energy (kWh)',
                            data: [10, 15, 20, 18, 22, 17, 12],
                            borderColor: 'rgba(34,197,94,1)',
                            backgroundColor: 'rgba(34,197,94,0.15)',
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 8
                        }]
                    },
                    options: { responsive: true, plugins: { legend: { display: true } } }
                });
            });
        // Battery Chart
        fetch('/battery_tariff_data.json')
            .then(res => res.json())
            .then(data => {
                if (!data || !data.labels) {
                    data = {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Battery Level (%)',
                            data: [80, 75, 90, 85, 70, 95, 88],
                            backgroundColor: 'rgba(59,130,246,0.5)',
                            borderColor: 'rgba(59,130,246,1)',
                            borderWidth: 1
                        }]
                    };
                }
                new Chart(document.getElementById('batteryChart'), {
                    type: 'bar',
                    data: data,
                    options: { responsive: true, plugins: { legend: { display: true } } }
                });
            }).catch(() => {
                // fallback static data
                new Chart(document.getElementById('batteryChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Battery Level (%)',
                            data: [80, 75, 90, 85, 70, 95, 88],
                            backgroundColor: 'rgba(59,130,246,0.5)',
                            borderColor: 'rgba(59,130,246,1)',
                            borderWidth: 1
                        }]
                    },
                    options: { responsive: true, plugins: { legend: { display: true } } }
                });
            });
        // Battery Savings Chart
        fetch('/battery_savings.json')
            .then(res => res.json())
            .then(data => {
                if (!data || !data.labels) {
                    data = {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                        datasets: [{
                            label: 'Savings (€)',
                            data: [50, 60, 55, 70, 65, 80, 75],
                            borderColor: 'rgba(251,191,36,1)',
                            backgroundColor: 'rgba(251,191,36,0.15)',
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 8
                        }]
                    };
                }
                new Chart(document.getElementById('batterySavingsChart'), {
                    type: 'line',
                    data: data,
                    options: { responsive: true, plugins: { legend: { display: true } } }
                });
            }).catch(() => {
                // fallback static data
                new Chart(document.getElementById('batterySavingsChart'), {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                        datasets: [{
                            label: 'Savings (€)',
                            data: [50, 60, 55, 70, 65, 80, 75],
                            borderColor: 'rgba(251,191,36,1)',
                            backgroundColor: 'rgba(251,191,36,0.15)',
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 8
                        }]
                    },
                    options: { responsive: true, plugins: { legend: { display: true } } }
                });
            });
        // Site Traffic Chart
        new Chart(document.getElementById('adminChart'), {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Visits',
                    data: [120, 190, 170, 210, 160, 230, 200],
                    borderColor: 'rgba(99,102,241,1)',
                    backgroundColor: 'rgba(99,102,241,0.15)',
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 10
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { font: { size: 14 } } },
                    x: { ticks: { font: { size: 14 } } }
                }
            }
        });
    });
    </script>
</x-app-layout>
