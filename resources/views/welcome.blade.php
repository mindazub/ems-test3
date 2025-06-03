<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>EDIS Lab | EMS Dashboard App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        tailwind.config = {
            darkMode: 'class'
        };
    </script>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">




</head>

<body class="bg-[#fdfdfc] text-gray-800">



    <!-- NAVBAR -->
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/edislab_monitoring.png') }}" alt="EDISLAB Logo" class="h-8 w-auto">
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Dark Mode Toggle -->
                    <button id="darkModeToggle"
                        class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                        title="Toggle Dark Mode">
                        <!-- Sun Icon -->
                        <svg id="sunIcon" class="w-5 h-5 text-yellow-400 dark:hidden" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m8.66-12.34l-.7.7M4.34 4.34l-.7.7M21 12h1M2 12H1m16.24 4.24l-.7-.7M4.34 19.66l-.7-.7M12 5a7 7 0 100 14 7 7 0 000-14z" />
                        </svg>
                        <!-- Moon Icon -->
                        <svg id="moonIcon" class="w-5 h-5 hidden dark:inline text-white" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                    </button>

                    <!-- Auth Buttons -->
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">Login</a>
                    <a href="{{ route('register') }}"
                        class="px-4 py-2 rounded border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition">Register</a>
                </div>

            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="bg-blue-100 py-20 px-4">
        <div class="max-w-6xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">
                Business build on <span class="text-blue-700">EDIS Lab EMS</span>
            </h1>
            <p class="mt-6 text-lg text-gray-700">
                Powerful energy monitoring tools to help manage solar, battery, and grid performance in one place.
            </p>
            <div class="mt-8">
                <a href="{{ route('register') }}"
                    class="px-6 py-3 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition">
                    Start Free Trial
                </a>
                <span class="text-sm text-gray-500 ml-4">No credit card required</span>
            </div>
        </div>

        <!-- CHARACTERS -->
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto px-4">
            <div class="text-center">
                <img src="{{ asset('images/energyChart_01.png') }}" alt="Char 1" class="h-32 mx-auto mb-4">
                <p class="text-sm text-gray-700">"The EMS dashboards help us track solar ROI in real time!"</p>
                <span class="block text-xs text-gray-500 mt-2">– Jane Doe, Energy Analyst</span>
            </div>
            <div class="text-center">
                <img src="{{ asset('images/batteryChart_02.png') }}" alt="Char 2" class="h-32 mx-auto mb-4">
                <p class="text-sm text-gray-700">"Battery efficiency optimization has never been easier."</p>
                <span class="block text-xs text-gray-500 mt-2">– John Solar, Installer</span>
            </div>
            <div class="text-center">
                <img src="{{ asset('images/batterySavingsChart_03.png') }}" alt="Char 3" class="h-32 mx-auto mb-4">
                <p class="text-sm text-gray-700">"I can monitor multiple plants in one simple view."</p>
                <span class="block text-xs text-gray-500 mt-2">– Alex Power, Manager</span>
            </div>
        </div>
    </section>

    <!-- FEATURE SECTION -->
    <section class="py-20 px-4 bg-white">
        <div class="max-w-6xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-6">Everything you need to manage your energy systems</h2>
            <p class="max-w-2xl mx-auto text-gray-600 mb-10">
                EDIS Lab offers real-time monitoring, historical data, smart alerts, and energy savings optimization.
            </p>

            <div class="grid md:grid-cols-3 gap-10 text-left">
                <div>
                    <h3 class="text-xl font-semibold mb-2">Live Energy Monitoring</h3>
                    <p class="text-gray-600">Track PV, battery, and grid power usage with live graphs and data charts.
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-2">Battery Optimization</h3>
                    <p class="text-gray-600">Analyze charge/discharge patterns and improve lifecycle efficiency.</p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-2">Savings Reports</h3>
                    <p class="text-gray-600">View detailed reports on energy savings and tariff-based usage.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 px-4 bg-blue-900 text-white text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to get started?</h2>
        <p class="mb-6">Start using EDIS Lab today to monitor, analyze, and save energy like never before.</p>
        <a href="{{ route('register') }}"
            class="inline-block px-6 py-3 bg-white text-blue-700 font-semibold rounded hover:bg-gray-100 transition">
            Get Started Free
        </a>
    </section>


    <!-- FOOTER -->
    <footer class="bg-gray-100 border-t border-gray-300 py-10 text-sm text-gray-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-4 gap-8">
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">EDIS Lab</h4>
                <p>Smart EMS tools for solar, battery, and grid energy management.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Platform</h4>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">Live Monitoring</a></li>
                    <li><a href="#" class="hover:text-blue-600">Reports</a></li>
                    <li><a href="#" class="hover:text-blue-600">Battery Insights</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Company</h4>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">About</a></li>
                    <li><a href="#" class="hover:text-blue-600">Contact</a></li>
                    <li><a href="#" class="hover:text-blue-600">Blog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Legal</h4>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-blue-600">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-blue-600">Cookies</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-8 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} EDIS Lab EMS. All rights reserved.
        </div>
    </footer>


    <script>
        const toggleBtn = document.getElementById('darkModeToggle');
        const html = document.documentElement;

        // Init on load
        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('dark-mode') === 'true') {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        });

        // Toggle on click
        toggleBtn?.addEventListener('click', () => {
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('dark-mode', isDark);
        });
    </script>

</body>

</html>
