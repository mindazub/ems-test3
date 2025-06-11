<!-- FOOTER -->
<footer class="bg-gray-100 border-t border-gray-300 py-10 text-sm text-gray-600 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-4 gap-8">
        <div>
            <h4 class="font-semibold text-gray-800 mb-2">EDIS Lab</h4>
            <p>Smart EMS tools for solar, battery, and grid energy management.</p>
        </div>
        <div>
            <h4 class="font-semibold text-gray-800 mb-2">Platform</h4>
            <ul class="space-y-1">
                @auth
                    <li><a href="{{ route('plants.index') }}" class="hover:text-blue-600">Live Monitoring</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="hover:text-blue-600">Live Monitoring</a></li>
                @endauth
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
