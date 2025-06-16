@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plant Details') }} - {{ isset($id) ? Str::substr($id, 0, 8) :  'N/A' }}
        </h2>
    </x-slot>

    {{-- Only Leaflet CSS needed --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    
    <style>
        /* Ensure grid layout works properly */
        @media (min-width: 1024px) {
            .plant-grid {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 1.5rem !important;
            }
            .plant-grid > div {
                min-width: 0 !important;
                max-width: 100% !important;
            }
        }
        
        /* Prevent table from overflowing its container */
        .table-container {
            overflow: hidden;
            max-width: 100%;
        }
        
        .table-container table {
            table-layout: fixed;
            width: 100%;
        }
        
        .table-container td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        /* Map legend styling */
        .map-legend {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 12px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            font-size: 12px;
            border: 1px solid #e5e7eb;
        }
        
        .map-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 2px;
        }
        
        .map-legend-item:last-child {
            margin-bottom: 0;
        }
        
        .legend-icon {
            width: 12px;
            height: 12px;
            background: #3b82f6;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        /* Ensure spinner animation works */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">

                <!-- PLANT INFO SECTION -->
                <div class="mb-6">
                    <!-- Plant ID Header -->
                    <div class="mb-6">
                        <h1 class="text-4xl font-bold">
                            <span class="text-gray-400 italic">#ID&nbsp;{{ Str::substr($id ?? 'N/A', 0, 8) }}</span>
                        </h1>
                    </div>

                    <!-- Main Content: Table + Map in 50/50 layout -->
                    <div class="plant-grid grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                        <!-- Left Side: General Info Table -->
                        <div class="min-w-0 w-full">
                            <h2 class="text-lg font-semibold mb-3">General Info</h2>
                            <div class="table-container bg-white rounded-lg overflow-hidden border border-gray-200 shadow-sm max-w-full">
                                @if(!empty($plant->metadata_flat))
                                    <div class="overflow-x-auto">
                                        <table class="w-full table-fixed">
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach($plant->metadata_flat as $metaKey => $metaValue)
                                                    <tr class="hover:bg-gray-50 transition-colors">
                                                        <td class="px-3 py-2 text-sm font-medium text-gray-900 bg-gray-50 border-r border-gray-200 w-2/5 break-words">
                                                            {{ $metaKey }}
                                                        </td>
                                                        <td class="px-3 py-2 text-sm text-gray-700 w-3/5 break-words">
                                                    @if(is_array($metaValue))
                                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                            {{ json_encode($metaValue) }}
                                                        </span>
                                                    @elseif($metaKey === 'Owner Email')
                                                        <a href="mailto:{{ $metaValue }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                            {{ $metaValue }}
                                                        </a>
                                                    @elseif(in_array($metaKey, ['Latitude', 'Longitude']))
                                                        <span class="font-mono text-green-700">{{ $metaValue }}</span>
                                                    @elseif($metaKey === 'Status')
                                                        @if($metaValue === 'Working')
                                                            <span class="bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-green-900 dark:text-green-300">{{ $metaValue }}</span>
                                                        @elseif($metaValue === 'Maintenance')
                                                            <span class="bg-yellow-100 text-yellow-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-yellow-900 dark:text-yellow-300">{{ $metaValue }}</span>
                                                        @elseif($metaValue === 'Offline')
                                                            <span class="bg-red-100 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-red-900 dark:text-red-300">{{ $metaValue }}</span>
                                                        @else
                                                            <span class="bg-gray-100 text-gray-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300">{{ $metaValue }}</span>
                                                        @endif
                                                    @elseif($metaKey === 'Capacity')
                                                        <span class="font-semibold text-indigo-700">
                                                            {{ number_format($metaValue / 1000) }} kWh
                                                        </span>
                                                    @elseif(str_contains($metaKey, 'Updated at') || str_contains($metaKey, 'Date'))
   
                                                    <span class="text-gray-600">

                                                            {{ $metaValue }}
                                                        </span>
                                                    @else
                                                        {{ $metaValue }}
                                                    @endif                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                            @else
                                <div class="px-4 py-6 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2">No plant information available</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('plants.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-2 rounded transition">
                                    Back to All Plants List
                                </a>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-3">
                                <!-- Download Report PDF (Icon Only) -->
                                <div class="relative group">
                                    <button id="download-report-pdf" class="inline-flex items-center justify-center w-10 h-10 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors shadow-md hover:shadow-lg">
                                        <svg class="w-5 h-5 text-white p-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2 2 2 0 0 0 2 2h12a2 2 0 0 0 2-2 2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2V4a2 2 0 0 0-2-2h-7Zm-6 9a1 1 0 0 0-1 1v5a1 1 0 1 0 2 0v-1h.5a2.5 2.5 0 0 0 0-5H5Zm1.5 3H6v-1h.5a.5.5 0 0 1 0 1Zm4.5-3a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h1.376A2.626 2.626 0 0 0 15 15.375v-1.75A2.626 2.626 0 0 0 12.375 11H11Zm1 5v-3h.375a.626.626 0 0 1 .625.626v1.748a.625.625 0 0 1-.626.626H12Zm5-5a1 1 0 0 0-1 1v5a1 1 0 1 0 2 0v-1h1a1 1 0 1 0 0-2h-1v-1h1a1 1 0 1 0 0-2h-2Z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-12 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                                        Download Report PDF
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>

                                <!-- Download Pictures JPG/PNG (Icon Only) -->
                                <div class="relative group">
                                    <button id="download-all-charts" class="inline-flex items-center justify-center w-10 h-10 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors shadow-md hover:shadow-lg">
                                        <svg class="w-5 h-5 text-white p-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 16 5-7 6 6.5m6.5 2.5L16 13l-4.286 6M14 10h.01M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"/>
                                        </svg>
                                    </button>
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-12 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                                        Download Pictures JPG/PNG
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>

                                <!-- Download Data CSV (Icon Only) -->
                                <div class="relative group">
                                    <button id="download-all-csv" class="inline-flex items-center justify-center w-10 h-10 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors shadow-md hover:shadow-lg">
                                        <svg class="w-5 h-5 text-white p-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2 2 2 0 0 0 2 2h12a2 2 0 0 0 2-2 2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2V4a2 2 0 0 0-2-2h-7Zm1.018 8.828a2.34 2.34 0 0 0-2.373 2.13v.008a2.32 2.32 0 0 0 2.06 2.497l.535.059a.993.993 0 0 0 .136.006.272.272 0 0 1 .263.367l-.008.02a.377.377 0 0 1-.018.044.49.49 0 0 1-.078.02 1.689 1.689 0 0 1-.297.021h-1.13a1 1 0 1 0 0 2h1.13c.417 0 .892-.05 1.324-.279.47-.248.78-.648.953-1.134a2.272 2.272 0 0 0-2.115-3.06l-.478-.052a.32.32 0 0 1-.285-.341.34.34 0 0 1 .344-.306l.94.02a1 1 0 1 0 .043-2l-.943-.02h-.003Zm7.933 1.482a1 1 0 1 0-1.902-.62l-.57 1.747-.522-1.726a1 1 0 0 0-1.914.578l1.443 4.773a1 1 0 0 0 1.908.021l1.557-4.773Zm-13.762.88a.647.647 0 0 1 .458-.19h1.018a1 1 0 1 0 0-2H6.647A2.647 2.647 0 0 0 4 13.647v1.706A2.647 2.647 0 0 0 6.647 18h1.018a1 1 0 1 0 0-2H6.647A.647.647 0 0 1 6 15.353v-1.706c0-.172.068-.336.19-.457Z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-12 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                                        Download Data CSV
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            </div>
 
                        </div>

                        <!-- Right Side: Map -->
                        <div class="min-w-0 w-full">
                            <h3 class="text-lg font-semibold mb-3">Map Location</h3>
                            <div class="relative">
                                <div id="map" class="rounded-lg shadow border border-gray-200 w-full max-w-full"></div>
                                <div class="map-legend">
                                    <div class="map-legend-item">
                                        <div class="legend-icon"></div>
                                        <span class="text-gray-700 font-medium">Plant Location</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CHARTS -->
                @include('plants.partials.plant-chart', ['plant' => $plant, 'user' => $user])

                <!-- DEVICES LIST -->
                @include('plants.partials.devices-list')
            </div>
        </div>
    </div>

    {{-- JS Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        // --- LEAFLET MAP INIT ---
        document.addEventListener('DOMContentLoaded', function() {
            // Function to adjust map height to match table
            function adjustMapHeight() {
                const tableContainer = document.querySelector('.table-container');
                const mapContainer = document.getElementById('map');
                
                if (tableContainer && mapContainer) {
                    // Get just the table height (without the back button)
                    const tableHeight = tableContainer.offsetHeight;
                    
                    // Set map height to match table exactly
                    mapContainer.style.height = tableHeight + 'px';
                    
                    // Invalidate map size and recenter to ensure proper rendering
                    if (window.mapInstance) {
                        setTimeout(() => {
                            window.mapInstance.invalidateSize();
                            // Recenter the map to ensure marker is in the center
                            const currentCenter = window.mapInstance.getCenter();
                            window.mapInstance.setView(currentCenter, window.mapInstance.getZoom());
                        }, 150);
                    }
                }
            }
            
            // Prefer plant_metadata lat/lng if available, else fallback
            let lat = @json($plant->metadata_flat['Latitude'] ?? $plant->latitude ?? 0);
            let lng = @json($plant->metadata_flat['Longitude'] ?? $plant->longitude ?? 0);
            const map = L.map('map').setView([lat, lng], 13);
            
            // Store map instance globally for height adjustments
            window.mapInstance = map;

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([lat, lng])
                .addTo(map)
                .bindPopup(
                    "<strong>{{ $plant->name ?? $plant->uid }}</strong><br>" +
                    "Lat: " + lat + "<br>" +
                    "Long: " + lng
                )
                .openPopup();
            
            // Ensure the marker is centered in the map view
            map.setView([lat, lng], 13);
            
            // Adjust map height after map is loaded and center the marker
            setTimeout(() => {
                adjustMapHeight();
                // Re-center after height adjustment to ensure marker is visible and centered
                map.setView([lat, lng], 13);
            }, 200);
            
            // Adjust map height on window resize
            window.addEventListener('resize', adjustMapHeight);
        });
        
        // SIMPLE WORKING DOWNLOAD SOLUTION
        document.addEventListener('DOMContentLoaded', function() {
            const plantId = @json($plant->uid ?? $id);
            
            // Show notification function
            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                    type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
                    type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
                    'bg-blue-100 text-blue-800 border border-blue-200'
                }`;
                notification.textContent = message;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 3000);
            }
            
            // Function to get chart image with white background
            function getChartImageWithWhiteBackground(canvas) {
                // Create a new canvas with white background
                const tempCanvas = document.createElement('canvas');
                const tempCtx = tempCanvas.getContext('2d');
                
                // Set canvas size to match original
                tempCanvas.width = canvas.width;
                tempCanvas.height = canvas.height;
                
                // Fill with white background
                tempCtx.fillStyle = '#FFFFFF';
                tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
                
                // Draw the original chart on top
                tempCtx.drawImage(canvas, 0, 0);
                
                // Return the data URL with white background
                return tempCanvas.toDataURL('image/png');
            }
            
            // Simple window.open download - WORKS LIKE INDIVIDUAL DOWNLOADS
            function simpleDownload(url, loadingMessage) {
                showNotification(loadingMessage, 'info');
                
                // Use same method as working individual downloads
                const link = document.createElement('a');
                link.href = url;
                link.download = '';
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                setTimeout(() => {
                    showNotification('Download started!', 'success');
                }, 1000);
            }
            
            // Get current date
            function getCurrentDate() {
                return document.getElementById('energy-date')?.value || new Date().toISOString().split('T')[0];
            }
            
            // Function to show loading spinner in button
            function showButtonLoading(button, originalContent) {
                button.innerHTML = `
                    <svg aria-hidden="true" class="w-4 h-4 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                `;
                button.disabled = true;
                button.classList.add('opacity-75', 'cursor-not-allowed');
            }
            
            // Function to restore button original state
            function restoreButton(button, originalContent) {
                button.innerHTML = originalContent;
                button.disabled = false;
                button.classList.remove('opacity-75', 'cursor-not-allowed');
            }
            
            // Download Report PDF - Save images first, then generate PDF
            document.getElementById('download-report-pdf').addEventListener('click', async function(e) {
                e.preventDefault();
                
                const btn = this;
                const originalContent = btn.innerHTML;
                showButtonLoading(btn, originalContent);
                
                try {
                    showNotification('Preparing chart images for PDF...', 'info');
                    
                    // Wait for charts to be fully rendered
                    await new Promise(resolve => setTimeout(resolve, 500));
                    
                    // First, save chart images to session for PDF generation
                    const chartImages = {};
                    const chartTypes = ['energy', 'battery', 'savings'];
                    let imagesFound = 0;
                    
                    chartTypes.forEach(chartType => {
                        const chartCanvas = document.getElementById(`${chartType}Chart`);
                        if (chartCanvas) {
                            // Get chart image with white background for PDF
                            const imageData = getChartImageWithWhiteBackground(chartCanvas);
                            if (imageData && imageData.length > 100) { // Basic validation
                                chartImages[chartType] = imageData;
                                imagesFound++;
                                console.log(`✅ Captured ${chartType} chart image (${imageData.length} bytes)`);
                            } else {
                                console.warn(`⚠️ Failed to capture ${chartType} chart image`);
                            }
                        } else {
                            console.warn(`⚠️ Chart canvas not found: ${chartType}Chart`);
                        }
                    });
                    
                    console.log(`📊 Total chart images captured: ${imagesFound}`);
                    
                    if (imagesFound > 0) {
                        // Save images to session for PDF generation
                        showNotification(`Saving ${imagesFound} chart images...`, 'info');
                        
                        const saveResponse = await fetch(`/plants/${plantId}/save-chart-images`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                            },
                            body: JSON.stringify({
                                chart_images: chartImages,
                                date: getCurrentDate()
                            })
                        });
                        
                        if (!saveResponse.ok) {
                            const errorText = await saveResponse.text();
                            console.error('Failed to save chart images:', errorText);
                            throw new Error('Failed to save chart images for PDF');
                        }
                        
                        const saveResult = await saveResponse.json();
                        console.log('Chart images saved successfully:', saveResult);
                    } else {
                        console.warn('No chart images captured, proceeding with PDF generation anyway');
                    }
                    
                    // Now generate PDF with images and data
                    showNotification('Generating comprehensive PDF report...', 'info');
                    const currentDate = getCurrentDate();
                    const url = `/plants/${plantId}/download-report-pdf?date=${currentDate}`;
                    simpleDownload(url, 'Generating comprehensive PDF report...');
                    
                } catch (error) {
                    console.error('PDF generation error:', error);
                    showNotification('Failed to generate PDF report: ' + error.message, 'error');
                } finally {
                    setTimeout(() => {
                        restoreButton(btn, originalContent);
                    }, 2000);
                }
            });
            
            // Download All Charts - First save images, then download
            document.getElementById('download-all-charts').addEventListener('click', async function(e) {
                e.preventDefault();
                
                const btn = this;
                const originalContent = btn.innerHTML;
                showButtonLoading(btn, originalContent);
                
                try {
                    showNotification('Preparing chart images...', 'info');
                    
                    // Collect chart images
                    const chartImages = {};
                    const chartTypes = ['energy', 'battery', 'savings'];
                    
                    chartTypes.forEach(chartType => {
                        const chartCanvas = document.getElementById(`${chartType}Chart`);
                        if (chartCanvas) {
                            // Get chart image with white background
                            chartImages[chartType] = getChartImageWithWhiteBackground(chartCanvas);
                        }
                    });
                    
                    if (Object.keys(chartImages).length === 0) {
                        throw new Error('No chart images found. Please ensure charts are loaded.');
                    }
                    
                    // Save images to server first
                    const saveResponse = await fetch(`/plants/${plantId}/save-chart-images`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        },
                        body: JSON.stringify({
                            chart_images: chartImages,
                            date: getCurrentDate()
                        })
                    });
                    
                    if (!saveResponse.ok) {
                        throw new Error('Failed to save chart images');
                    }
                    
                    // Now download using simple GET
                    const currentDate = getCurrentDate();
                    const url = `/plants/${plantId}/download-all-charts?date=${currentDate}`;
                    simpleDownload(url, 'Downloading chart images...');
                    
                } catch (error) {
                    showNotification('Failed to download chart images: ' + error.message, 'error');
                } finally {
                    setTimeout(() => {
                        restoreButton(btn, originalContent);
                    }, 2000);
                }
            });
            
            // Download All CSV - Save data first, then download
            document.getElementById('download-all-csv').addEventListener('click', async function(e) {
                e.preventDefault();
                
                const btn = this;
                const originalContent = btn.innerHTML;
                showButtonLoading(btn, originalContent);
                
                try {
                    showNotification('Preparing CSV data...', 'info');
                    
                    // Collect chart data
                    const chartData = {};
                    const chartTypes = ['energy', 'battery', 'savings'];
                    
                    chartTypes.forEach(chartType => {
                        const chartCanvas = document.getElementById(`${chartType}Chart`);
                        if (chartCanvas) {
                            const chartInstance = Chart.getChart(chartCanvas);
                            if (chartInstance && chartInstance.data) {
                                chartData[chartType] = {
                                    labels: chartInstance.data.labels || [],
                                    datasets: (chartInstance.data.datasets || []).map(dataset => ({
                                        label: dataset.label,
                                        data: dataset.data
                                    }))
                                };
                            }
                        }
                    });
                    
                    if (Object.keys(chartData).length === 0) {
                        throw new Error('No chart data found. Please ensure charts are loaded.');
                    }
                    
                    // Save data to server first
                    const saveResponse = await fetch(`/plants/${plantId}/save-chart-data`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        },
                        body: JSON.stringify({
                            chart_data: chartData,
                            date: getCurrentDate()
                        })
                    });
                    
                    if (!saveResponse.ok) {
                        throw new Error('Failed to save chart data');
                    }
                    
                    // Now download using simple GET
                    const currentDate = getCurrentDate();
                    const url = `/plants/${plantId}/download-all-csv?date=${currentDate}`;
                    simpleDownload(url, 'Downloading CSV data...');
                    
                } catch (error) {
                    showNotification('Failed to download CSV data: ' + error.message, 'error');
                } finally {
                    setTimeout(() => {
                        restoreButton(btn, originalContent);
                    }, 2000);
                }
            });
        });
    </script>
</x-app-layout>
