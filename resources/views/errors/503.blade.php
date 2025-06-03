<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Unavailable | EDIS Lab EMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-blue-100 min-h-screen flex flex-col justify-center items-center text-gray-800">
    <div class="max-w-lg w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <img src="{{ asset('images/edislab_monitoring.png') }}" alt="EDISLAB Logo" class="h-12 mx-auto mb-6">
        <h1 class="text-4xl font-extrabold text-blue-700 mb-4">503</h1>
        <h2 class="text-2xl font-bold mb-2">Service Unavailable</h2>
        <p class="mb-6 text-gray-600">Our EMS platform is currently undergoing maintenance or is temporarily unavailable.<br>
        Please check back soon or contact support if the issue persists.</p>
        <a href="{{ url('/') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition">Go to Home</a>
        <div class="mt-8 text-xs text-gray-400">&copy; {{ date('Y') }} EDIS Lab EMS. All rights reserved.</div>
    </div>
</body>
</html>
