<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $plant->name }} - {{ ucfirst($chart) }} Report</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            font-size: 11px; 
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 20px;
            margin: 0 0 5px 0;
            color: #4f46e5;
        }
        
        .header .subtitle {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-cell {
            display: table-cell;
            padding: 5px 10px 5px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #374151;
        }
        
        .chart-container {
            text-align: center;
            margin: 20px 0;
            page-break-inside: avoid;
        }
        
        .chart-image {
            max-width: 100%;
            height: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        
        .summary-section {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-cell {
            display: table-cell;
            padding: 3px 15px 3px 0;
            font-size: 10px;
        }
        
        .summary-label {
            font-weight: bold;
            color: #6b7280;
        }
        
        .summary-value {
            color: #111827;
        }
        
        .data-section {
            margin-top: 30px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
            font-size: 9px;
        }
        
        th, td { 
            border: 1px solid #e5e7eb; 
            padding: 4px 6px; 
            text-align: left; 
        }
        
        th { 
            background-color: #f3f4f6; 
            font-weight: bold;
            color: #374151;
            font-size: 10px;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Plant Report with Charts</h1>
        <p class="subtitle">Plant ID: {{ $plant->uid ?? 'Unknown' }} | Date: {{ \Carbon\Carbon::parse($selectedDate)->format('Y-m-d') }} | Generated: {{ $generatedAt }}</p>
        @if(!empty($chartImage))
            <p style="font-size: 10px; color: #6b7280; margin: 5px 0 0 0;">Debug: {{ count($chartData ?? []) }} {{ $chart }} entries{{ empty($chartImage) ? ', no chart image' : ', chart image available' }}.</p>
        @endif
    </div>

    <!-- Plant Information Section -->
    <div class="section-title">Plant Information</div>
    @if(!empty($plant->metadata_flat))
        <table style="margin-bottom: 20px;">
            <tbody>
                @foreach($plant->metadata_flat as $metaKey => $metaValue)
                    <tr>
                        <td style="background-color: #f9fafb; font-weight: bold; width: 35%;">{{ $metaKey }}</td>
                        <td style="width: 65%;">
                            @if($metaKey === 'Owner Email')
                                {{ $metaValue }}
                            @elseif($metaKey === 'Status')
                                @if($metaValue === 'Working')
                                    <span style="background-color: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 3px; font-size: 9px;">{{ $metaValue }}</span>
                                @elseif($metaValue === 'Maintenance')
                                    <span style="background-color: #fef3c7; color: #92400e; padding: 2px 6px; border-radius: 3px; font-size: 9px;">{{ $metaValue }}</span>
                                @elseif($metaValue === 'Offline')
                                    <span style="background-color: #fecaca; color: #991b1b; padding: 2px 6px; border-radius: 3px; font-size: 9px;">{{ $metaValue }}</span>
                                @else
                                    <span style="background-color: #f3f4f6; color: #374151; padding: 2px 6px; border-radius: 3px; font-size: 9px;">{{ $metaValue }}</span>
                                @endif
                            @elseif($metaKey === 'Capacity')
                                <strong style="color: #4338ca;">{{ number_format($metaValue / 1000, 2) }} kWh</strong>
                            @else
                                {{ $metaValue }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="padding: 15px; text-align: center; color: #6b7280; border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 20px;">
            <p>No detailed plant information available</p>
        </div>
    @endif

    @if($chartImage)
        <div class="chart-container">
            <img src="{{ $chartImage }}" alt="Chart Image" class="chart-image">
        </div>
    @endif

    @if(!empty($summary))
        <div class="summary-section">
            <div class="summary-title">Summary Statistics</div>
            <div class="summary-grid">
                @if($chart === 'energy')
                    <div class="summary-row">
                        <div class="summary-cell summary-label">Average PV Power:</div>
                        <div class="summary-cell summary-value">{{ $summary['avg_pv'] ?? 'N/A' }} kW</div>
                        <div class="summary-cell summary-label">Total PV Energy:</div>
                        <div class="summary-cell summary-value">{{ $summary['total_pv'] ?? 'N/A' }} kWh</div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-cell summary-label">Average Battery Power:</div>
                        <div class="summary-cell summary-value">{{ $summary['avg_battery'] ?? 'N/A' }} kW</div>
                        <div class="summary-cell summary-label">Average Grid Power:</div>
                        <div class="summary-cell summary-value">{{ $summary['avg_grid'] ?? 'N/A' }} kW</div>
                    </div>
                @elseif($chart === 'battery')
                    <div class="summary-row">
                        <div class="summary-cell summary-label">Average Battery Power:</div>
                        <div class="summary-cell summary-value">{{ $summary['avg_power'] ?? 'N/A' }} kW</div>
                        <div class="summary-cell summary-label">Average Energy Price:</div>
                        <div class="summary-cell summary-value">{{ $summary['avg_price'] ?? 'N/A' }} €/kWh</div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-cell summary-label">Max Energy Price:</div>
                        <div class="summary-cell summary-value">{{ $summary['max_price'] ?? 'N/A' }} €/kWh</div>
                        <div class="summary-cell summary-label">Min Energy Price:</div>
                        <div class="summary-cell summary-value">{{ $summary['min_price'] ?? 'N/A' }} €/kWh</div>
                    </div>
                @elseif($chart === 'savings')
                    <div class="summary-row">
                        <div class="summary-cell summary-label">Total Savings:</div>
                        <div class="summary-cell summary-value">€{{ $summary['total_savings'] ?? 'N/A' }}</div>
                        <div class="summary-cell summary-label">Average Savings:</div>
                        <div class="summary-cell summary-value">€{{ $summary['avg_savings'] ?? 'N/A' }}</div>
                    </div>
                @endif
                <div class="summary-row">
                    <div class="summary-cell summary-label">Data Points:</div>
                    <div class="summary-cell summary-value">{{ $summary['data_points'] ?? 'N/A' }}</div>
                    <div class="summary-cell summary-label"></div>
                    <div class="summary-cell summary-value"></div>
                </div>
            </div>
        </div>
    @endif

    @if(!empty($chartData))
        <div class="data-section">
            <div class="section-title">Detailed Data</div>
            <table>
                <thead>
                    <tr>
                        <th class="text-center">Time</th>
                        @if($chart === 'energy')
                            <th class="text-right">PV Power (kW)</th>
                            <th class="text-right">Battery Power (kW)</th>
                            <th class="text-right">Grid Power (kW)</th>
                        @elseif($chart === 'battery')
                            <th class="text-right">Battery Power (kW)</th>
                            <th class="text-right">Energy Price (€/kWh)</th>
                        @elseif($chart === 'savings')
                            <th class="text-right">Battery Savings (€)</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sortedData = collect($chartData)->sortBy(function($value, $key) {
                            return strtotime($key);
                        });
                    @endphp
                    @foreach($sortedData as $timestamp => $values)
                        <tr>
                            <td class="text-center">
                                @if($user && $user->getTimeFormat() === '12')
                                    {{ date('g:i A', strtotime($timestamp)) }}
                                @else
                                    {{ date('H:i', strtotime($timestamp)) }}
                                @endif
                            </td>
                            @if($chart === 'energy')
                                <td class="text-right">{{ number_format(($values['pv_p'] ?? 0) / 1000, 2) }}</td>
                                <td class="text-right">{{ number_format(($values['battery_p'] ?? 0) / 1000, 2) }}</td>
                                <td class="text-right">{{ number_format(($values['grid_p'] ?? 0) / 1000, 2) }}</td>
                            @elseif($chart === 'battery')
                                <td class="text-right">{{ number_format(($values['battery_p'] ?? 0) / 1000, 2) }}</td>
                                <td class="text-right">{{ number_format($values['tariff'] ?? 0, 4) }}</td>
                            @elseif($chart === 'savings')
                                <td class="text-right">{{ number_format($values['battery_savings'] ?? 0, 4) }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="data-section">
            <div class="section-title">Data</div>
            <p style="text-align: center; color: #9ca3af; padding: 40px;">No data available for the selected date.</p>
        </div>
    @endif

    <div class="footer">
        Generated by EMS Platform on 
        @if($user && $user->getTimeFormat() === '12')
            {{ date('Y-m-d g:i:s A', strtotime($generatedAt)) }}
        @else
            {{ $generatedAt }}
        @endif
        | Plant: {{ $plant->name }} | Report: {{ ucfirst($chart) }}
    </div>
</body>
</html>
