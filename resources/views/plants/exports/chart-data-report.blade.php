@php
    use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Plant Report with Charts - {{ $plant->name ?? $id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .chart-section {
            margin-bottom: 40px;
            page-break-before: auto;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #4f46e5;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8fafc;
            border-radius: 6px;
        }
        
        .chart-image {
            width: 100%;
            max-width: 100%;
            height: auto;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 20px;
            display: block;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .info-table th,
        .info-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        
        .info-table th {
            background-color: #f9fafb;
            font-weight: bold;
            width: 30%;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 20px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #e5e7eb;
            padding: 4px 6px;
            text-align: center;
        }
        
        .data-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        
        .status-working { color: #10b981; font-weight: bold; }
        .status-maintenance { color: #f59e0b; font-weight: bold; }
        .status-offline { color: #ef4444; font-weight: bold; }
        
        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
        }
        
        .summary-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        .data-summary {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 11px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="title">Plant Report with Charts</div>
        <div class="subtitle">
            Plant ID: {{ Str::substr($id, 0, 8) }} | 
            Date: {{ $selectedDate }} | 
            Generated: {{ $generatedAt }}
        </div>
        @if(app()->environment('local'))
        <div style="font-size: 8px; color: #999; margin-top: 5px;">
            Debug: {{ count($chartImages ?? []) }} chart images available
            {{ !empty($energyData) ? count($energyData) : 0 }} energy, 
            {{ !empty($batteryData) ? count($batteryData) : 0 }} battery, 
            {{ !empty($savingsData) ? count($savingsData) : 0 }} savings entries
        </div>
        @endif
    </div>

    <!-- Plant Information -->
    <div class="section">
        <div class="section-title">Plant Information</div>
        @if(!empty($metadata))
            <table class="info-table">
                @foreach($metadata as $key => $value)
                <tr>
                    <th>{{ $key }}</th>
                    <td>
                        @if($key === 'Status')
                            <span class="status-{{ strtolower($value) }}">{{ $value }}</span>
                        @elseif($key === 'Capacity')
                            {{ number_format($value / 1000) }} kWh
                        @elseif(in_array($key, ['Latitude', 'Longitude']))
                            {{ $value }}°
                        @else
                            {{ $value }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        @else
            <p>No plant information available.</p>
        @endif
    </div>

    <!-- Data Summary -->
    @if(!empty($energyData) || !empty($batteryData) || !empty($savingsData))
    <div class="section">
        <div class="section-title">Daily Summary</div>
        <div class="summary-box">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-value">
                        {{ !empty($energyData) ? count($energyData) : 0 }}
                    </div>
                    <div class="summary-label">Energy Data Points</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">
                        {{ !empty($batteryData) ? count($batteryData) : 0 }}
                    </div>
                    <div class="summary-label">Battery Data Points</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">
                        {{ !empty($savingsData) ? count($savingsData) : 0 }}
                    </div>
                    <div class="summary-label">Savings Data Points</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Energy Chart and Data -->
    @if(!empty($energyData))
    <div class="chart-section page-break">
        <div class="chart-title">Energy Production and Consumption</div>
        
        @if(!empty($chartImages['energy']) && str_starts_with($chartImages['energy'], 'data:image/'))
            <img src="{{ $chartImages['energy'] }}" alt="Energy Chart" class="chart-image">
            <p style="text-align: center; font-size: 10px; color: #666; margin-bottom: 15px;">
                <em>Energy chart showing PV generation, battery flow, and grid consumption</em>
            </p>
        @else
            <div style="text-align: center; padding: 40px; border: 2px dashed #d1d5db; margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px;">
                    <strong>Energy chart image not available</strong><br>
                    <small>Chart data is shown in the table below</small>
                </p>
            </div>
        @endif
        
        <div class="data-summary">
            <strong>Chart Data Summary:</strong> This chart shows PV Power generation (blue), Battery Power flow (red), and Grid Power consumption (green) throughout the day. Positive battery values indicate charging, negative values indicate discharging.
        </div>
        
        <div class="section-title">Energy Live Table</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>PV (kW)</th>
                    <th>Battery (kW)</th>
                    <th>Grid (kW)</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($energyData, 0, 48) as $entry)
                <tr>
                    <td>
                        @if($user && $user->getTimeFormat() === '12')
                            {{ date('g:i A', strtotime($entry['time'] ?? '00:00')) }}
                        @else
                            {{ $entry['time'] ?? '00:00' }}
                        @endif
                    </td>
                    <td>{{ number_format(($entry['pv_power'] ?? 0) / 1000, 2) }}</td>
                    <td>{{ number_format(($entry['battery_power'] ?? 0) / 1000, 2) }}</td>
                    <td>{{ number_format(($entry['grid_power'] ?? 0) / 1000, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($energyData) > 48)
        <p style="font-style: italic; color: #666; text-align: center;">
            Showing first 48 of {{ count($energyData) }} total energy data points.
        </p>
        @endif
    </div>
    @endif

    <!-- Battery Chart and Data -->
    @if(!empty($batteryData))
    <div class="chart-section page-break">
        <div class="chart-title">Battery Power and Energy Price</div>
        
        @if(!empty($chartImages['battery']) && str_starts_with($chartImages['battery'], 'data:image/'))
            <img src="{{ $chartImages['battery'] }}" alt="Battery Chart" class="chart-image">
            <p style="text-align: center; font-size: 10px; color: #666; margin-bottom: 15px;">
                <em>Battery power flow and energy pricing throughout the day</em>
            </p>
        @else
            <div style="text-align: center; padding: 40px; border: 2px dashed #d1d5db; margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px;">
                    <strong>Battery chart image not available</strong><br>
                    <small>Battery data is shown in the table below</small>
                </p>
            </div>
        @endif
        
        <div class="data-summary">
            <strong>Chart Data Summary:</strong> This chart displays Battery Power flow throughout the day and the Energy Price (tariff) used for calculations. Positive battery values indicate discharging, negative values indicate charging.
        </div>
        
        <div class="section-title">Battery Power and Energy Price Table</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Battery Power (kW)</th>
                    <th>Energy Price (€ / kWh)</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($batteryData, 0, 48) as $entry)
                <tr>
                    <td>
                        @if($user && $user->getTimeFormat() === '12')
                            {{ date('g:i A', strtotime($entry['time'] ?? '00:00')) }}
                        @else
                            {{ $entry['time'] ?? '00:00' }}
                        @endif
                    </td>
                    <td>{{ number_format(($entry['battery_power'] ?? 0) / 1000, 2) }}</td>
                    <td>{{ number_format($entry['energy_price'] ?? 0.1500, 4) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($batteryData) > 48)
        <p style="font-style: italic; color: #666; text-align: center;">
            Showing first 48 of {{ count($batteryData) }} total battery data points.
        </p>
        @endif
    </div>
    @endif

    <!-- Savings Chart and Data -->
    @if(!empty($savingsData))
    <div class="chart-section page-break">
        <div class="chart-title">Battery Savings</div>
        
        @if(!empty($chartImages['savings']) && str_starts_with($chartImages['savings'], 'data:image/'))
            <img src="{{ $chartImages['savings'] }}" alt="Savings Chart" class="chart-image">
            <p style="text-align: center; font-size: 10px; color: #666; margin-bottom: 15px;">
                <em>Financial savings achieved through battery optimization</em>
            </p>
        @else
            <div style="text-align: center; padding: 40px; border: 2px dashed #d1d5db; margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px;">
                    <strong>Savings chart image not available</strong><br>
                    <small>Savings data is shown in the table below</small>
                </p>
            </div>
        @endif
        
        <div class="data-summary">
            <strong>Chart Data Summary:</strong> This chart shows financial savings achieved through solar energy usage. Higher savings indicate better solar system performance.
        </div>
        
        <div class="section-title">Battery Savings Table</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Battery Savings (€)</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($savingsData, 0, 48) as $entry)
                <tr>
                    <td>
                        @if($user && $user->getTimeFormat() === '12')
                            {{ date('g:i A', strtotime($entry['time'] ?? '00:00')) }}
                        @else
                            {{ $entry['time'] ?? '00:00' }}
                        @endif
                    </td>
                    <td>{{ number_format($entry['savings'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($savingsData) > 48)
        <p style="font-style: italic; color: #666; text-align: center;">
            Showing first 48 of {{ count($savingsData) }} total savings data points.
        </p>
        @endif
    </div>
    @endif

    <!-- Temperature Chart and Data (if available) -->
    @if(!empty($temperatureData))
    <div class="chart-section page-break">
        <div class="chart-title">Temperature Monitoring</div>
        
        @if(!empty($chartImages['temperature']))
            <img src="{{ $chartImages['temperature'] }}" alt="Temperature Chart" class="chart-image">
        @else
            <div style="text-align: center; padding: 40px; border: 2px dashed #d1d5db; margin-bottom: 20px;">
                <p style="color: #6b7280;">Temperature chart image not available</p>
            </div>
        @endif
        
        <div class="data-summary">
            <strong>Chart Data Summary:</strong> This chart tracks temperature variations throughout the day, which can affect solar panel efficiency and battery performance.
        </div>
        
        <div class="section-title">Temperature Data Table</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Temperature (°C)</th>
                    <th>Ambient Temp (°C)</th>
                    <th>Panel Temp (°C)</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($temperatureData, 0, 48) as $entry)
                <tr>
                    <td>
                        @if($user && $user->getTimeFormat() === '12')
                            {{ date('g:i A', strtotime($entry['time'] ?? '00:00')) }}
                        @else
                            {{ $entry['time'] ?? '00:00' }}
                        @endif
                    </td>
                    <td>{{ number_format($entry['temperature'] ?? 0, 1) }}</td>
                    <td>{{ number_format($entry['ambient_temp'] ?? 0, 1) }}</td>
                    <td>{{ number_format($entry['panel_temp'] ?? 0, 1) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($temperatureData) > 48)
        <p style="font-style: italic; color: #666; text-align: center;">
            Showing first 48 of {{ count($temperatureData) }} total temperature data points.
        </p>
        @endif
    </div>
    @endif

    <!-- Report Summary -->
    <div class="section page-break">
        <div class="section-title">Report Summary</div>
        <div class="summary-box">
            <h4>Key Performance Indicators</h4>
            @if(!empty($energyData))
            <p><strong>Energy Production:</strong> 
                Total PV Power Generated: {{ number_format(array_sum(array_column($energyData, 'pv_power')) / 1000, 2) }} kWh
            </p>
            @endif
            @if(!empty($batteryData))
            <p><strong>Battery Performance:</strong> 
                Average SOC: {{ number_format(array_sum(array_column($batteryData, 'soc')) / count($batteryData), 1) }}%
            </p>
            @endif
            @if(!empty($savingsData))
            <p><strong>Financial Savings:</strong> 
                Total Daily Savings: €{{ number_format(array_sum(array_column($savingsData, 'savings')), 2) }}
            </p>
            @endif
            <p><strong>Data Quality:</strong> 
                {{ (!empty($energyData) ? count($energyData) : 0) + (!empty($batteryData) ? count($batteryData) : 0) + (!empty($savingsData) ? count($savingsData) : 0) }} 
                total data points collected for {{ $selectedDate }}
            </p>
        </div>
    </div>

    <!-- No Data Message -->
    @if(empty($energyData) && empty($batteryData) && empty($savingsData))
    <div class="section">
        <div class="section-title">No Data Available</div>
        <div style="text-align: center; padding: 40px; border: 2px dashed #fbbf24; background-color: #fef3c7;">
            <h3 style="color: #92400e;">No Chart Data Found</h3>
            <p>No chart data was found for the selected date: <strong>{{ $selectedDate }}</strong></p>
            <p>Please ensure data exists for this plant and date before generating the report.</p>
            <p>Try selecting a different date or check if the plant was operational on this date.</p>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Plant Report with Charts Generated on {{ $generatedAt }} | Plant ID: {{ $id }} | Page <span class="pageNumber"></span>
    </div>
</body>
</html>
