<!DOCTYPE html>
<html>
<head>
    <title>Load Data Fix Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Testing Load Data Fix</h1>
    <button onclick="testLoadData()">Test Load Data from PlantController</button>
    <div id="result"></div>
    
    <script>
    async function testLoadData() {
        const plantId = '65f20fa1-047a-4379-8464-59f1d94be3c7';
        
        // Get today's date in the format expected by the API
        const today = new Date();
        const start = Math.floor(new Date(today.getFullYear(), today.getMonth(), today.getDate() - 1, 21, 0, 0).getTime() / 1000); // Yesterday 21:00
        
        try {
            const response = await fetch(`/plants/${plantId}/data?start=${start}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            console.log('=== LOAD DATA FIX TEST ===');
            console.log('Full response:', data);
            
            let html = '<h2>Test Results</h2>';
            
            if (data.energy_chart) {
                const entries = Object.entries(data.energy_chart).slice(0, 5);
                html += '<h3>Energy Chart Data (first 5 entries):</h3>';
                html += '<table border="1" style="border-collapse: collapse;">';
                html += '<tr><th>Timestamp</th><th>PV (W)</th><th>Battery (W)</th><th>Grid (W)</th><th>Load (W)</th><th>Status</th></tr>';
                
                let hasLoadData = false;
                entries.forEach(([timestamp, values]) => {
                    const loadStatus = values.load_p !== undefined && values.load_p !== 0 ? '✅ HAS DATA' : '❌ MISSING';
                    if (values.load_p !== undefined && values.load_p !== 0) hasLoadData = true;
                    
                    html += `<tr>`;
                    html += `<td>${timestamp}</td>`;
                    html += `<td>${values.pv_p || 0}</td>`;
                    html += `<td>${values.battery_p || 0}</td>`;
                    html += `<td>${values.grid_p || 0}</td>`;
                    html += `<td style="background-color: ${values.load_p !== 0 ? 'lightgreen' : 'pink'};">${values.load_p || 0}</td>`;
                    html += `<td>${loadStatus}</td>`;
                    html += `</tr>`;
                });
                html += '</table>';
                
                // Summary
                const allLoadValues = Object.values(data.energy_chart).map(entry => entry.load_p || 0);
                const nonZeroLoadValues = allLoadValues.filter(val => val !== 0);
                
                html += '<h3>Summary:</h3>';
                html += `<p><strong>Fix Status:</strong> ${hasLoadData ? '✅ SUCCESS - Load data is now present!' : '❌ FAILED - Still no load data'}</p>`;
                html += `<p>Total entries: ${allLoadValues.length}</p>`;
                html += `<p>Non-zero load entries: ${nonZeroLoadValues.length}</p>`;
                html += `<p>Min load: ${Math.min(...allLoadValues)}W</p>`;
                html += `<p>Max load: ${Math.max(...allLoadValues)}W</p>`;
                
                if (nonZeroLoadValues.length > 0) {
                    html += `<p>Average non-zero load: ${(nonZeroLoadValues.reduce((a, b) => a + b, 0) / nonZeroLoadValues.length).toFixed(2)}W</p>`;
                }
            } else {
                html += '<p style="color: red;">❌ No energy_chart data found!</p>';
            }
            
            document.getElementById('result').innerHTML = html;
            
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('result').innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
        }
    }
    </script>
</body>
</html>
