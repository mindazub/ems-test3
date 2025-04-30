<!DOCTYPE html>
<html>
<head>
    <title>{{ $plant->name }} - Energy Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px; border: 1px solid #ccc; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>{{ $plant->name }} - Energy Report</h2>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>PV (kW)</th>
                <th>Battery (kW)</th>
                <th>Grid (kW)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $ts => $entry)
                <tr>
                    <td>{{ $ts }}</td>
                    <td>{{ number_format($entry['pv_p'], 2) }}</td>
                    <td>{{ number_format($entry['battery_p'], 2) }}</td>
                    <td>{{ number_format($entry['grid_p'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
