<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring - Amp Charging A</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        :root {
            --bg-color: #f8fafc;
            --panel-bg: #ffffff;
            --border-color: #e2e8f0;
            --primary: #0ea5e9;
            --primary-light: rgba(14, 165, 233, 0.1);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --danger: #ef4444;
            --success: #10b981;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
        }

        /* Diperlebar: max-width dari 1200px menjadi 95% (atau 1600px maksimal) */
        header {
            width: 100%;
            max-width: 95%;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 0.5rem;
        }

        .header-title h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
        }

        .subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .live-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: var(--success);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Diperlebar: max-width dari 1200px menjadi 95% */
        .dashboard-container {
            width: 100%;
            max-width: 95%;
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            padding: 0 0.5rem;
        }

        /* Filter Controls */
        .filter-card {
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            align-items: flex-end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-group label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-control {
            padding: 0.6rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            color: var(--text-main);
            background-color: #f8fafc;
            outline: none;
            transition: all 0.2s ease;
        }
        
        .filter-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
            background-color: #fff;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 2px 4px rgba(14, 165, 233, 0.3);
        }

        .btn-primary:hover {
            background-color: #0284c7;
            transform: translateY(-1px);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-main);
        }

        .btn-outline:hover {
            background-color: #f1f5f9;
        }

        /* Chart Card */
        .card {
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 1.1rem;
            color: var(--text-main);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.4rem;
        }

        .current-value-box {
            text-align: right;
        }

        .current-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
        }
        
        .unit {
            font-size: 1rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-left: 4px;
        }

        .error-message {
            background: #fef2f2;
            color: var(--danger);
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #fecaca;
            margin-bottom: 1rem;
            display: none;
            font-weight: 500;
            font-size: 0.9rem;
        }

        #chart {
            width: 100%;
            min-height: 450px; /* Ditinggikan sedikit */
        }

        /* Status Badge */
        .status-badge-inline {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .status-badge-inline .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-badge-inline.status-active {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-badge-inline.status-active .badge-dot {
            background: #10b981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5);
            animation: pulse-green 2s infinite;
        }

        .status-badge-inline.status-rest {
            background: rgba(245, 158, 11, 0.12);
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, 0.35);
        }

        .status-badge-inline.status-rest .badge-dot {
            background: #f59e0b;
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.5);
            animation: pulse-yellow 2s infinite;
        }

        .status-badge-inline.status-off {
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626; /* Tulisan warna merah */
            border: 1px solid rgba(239, 68, 68, 0.35);
        }

        .status-badge-inline.status-off .badge-dot {
            background: #ef4444;
        }

        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
            70% { box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        @keyframes pulse-yellow {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.6); }
            70% { box-shadow: 0 0 0 7px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }

        @media (max-width: 768px) {
            header, .card-header { flex-direction: column; gap: 1rem; align-items: flex-start; }
            .current-value-box { text-align: left; }
            .filter-card { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>

    <header>
        <div class="header-title">
            <h1>Production Dashboard</h1>
            <div class="subtitle">Ampere Charging A Monitoring</div>
        </div>
        <div class="live-badge" id="liveIndicator">
            <div class="pulse-dot"></div>
            <span>Live Mode</span>
        </div>
    </header>

    <div class="dashboard-container">
        
        <!-- Filter Bar -->
        <div class="filter-card">
            
            <div class="filter-group">
                <label for="circuit">Circuit ID</label>
                <select id="circuit" class="filter-control" style="font-weight: 600; color: var(--primary); min-width: 150px;">
                    <!-- Option akan diisi oleh JavaScript (1-100) -->
                </select>
            </div>

            <div class="filter-group">
                <label for="startDate">Dari Tanggal & Waktu</label>
                <input type="datetime-local" id="startDate" class="filter-control">
            </div>
            
            <div class="filter-group">
                <label for="endDate">Sampai Tanggal & Waktu</label>
                <input type="datetime-local" id="endDate" class="filter-control">
            </div>
            
            <div class="filter-group">
                <label for="interval">Tampilkan Per</label>
                <select id="interval" class="filter-control">
                    <option value="raw" selected>Realtime (Raw Data)</option>
                    <option value="1m">1 Menit</option>
                    <option value="10m">10 Menit</option>
                    <option value="30m">30 Menit</option>
                    <option value="1h">1 Jam</option>
                </select>
            </div>
            
            <div class="filter-group" style="flex-direction: row; align-items: flex-end; margin-bottom: 2px;">
                <button class="btn btn-primary" onclick="applyFilter()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    Terapkan Filter
                </button>
                <button class="btn btn-outline" onclick="resetToLive()" id="btnReset" style="display: none;">
                    Kembali ke Live
                </button>
            </div>
        </div>

        <div class="error-message" id="errorMsg"></div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">
                        Trend Data - <span id="currentCircuitTitle" style="color: var(--primary);">Circuit 21</span>
                    </div>
                    <div class="card-subtitle" id="chartSubtitle">Menampilkan data real-time terakhir</div>
                </div>
                <div style="display: flex; gap: 2.25rem; align-items: center; justify-content: flex-end; flex-wrap: wrap;">
                    <div class="current-value-box">
                        <div>
                            <span class="current-value" id="currentVoltase" style="color: var(--success);">--</span>
                            <span class="unit">V</span>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">Voltase Terakhir</div>
                    </div>
                    <div class="current-value-box">
                        <div>
                            <span class="current-value" id="currentAmp">--</span>
                            <span class="unit">A</span>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">Ampere Terakhir</div>
                    </div>
                    <div class="current-value-box" style="text-align: right;">
                        <div style="min-height: 40px; display: flex; align-items: center; justify-content: flex-end;">
                            <span id="currentStatusBadge" class="status-badge-inline status-off">
                                <span class="badge-dot"></span>
                                <span id="currentStatusIcon"></span>
                                <span id="currentStatusText">Memuat...</span>
                            </span>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">Status Charging</div>
                    </div>
                </div>
            </div>
            <div id="chart"></div>
        </div>

    </div>

    <script>
        // Inisialisasi dropdown circuit dengan pengelompokan 10 circuit aktif
        const circuitSelect = document.getElementById('circuit');
        const activeCircuitsList = [
            'Circuit 21', 'Circuit 26', 'Circuit 31', 'Circuit 36', 'Circuit 41',
            'Circuit 46', 'Circuit 51', 'Circuit 56', 'Circuit 61', 'Circuit 66'
        ];

        // Group 1: 10 Circuit Terupdate
        const optGroupActive = document.createElement('optgroup');
        optGroupActive.label = '⭐ 10 Circuit Terupdate (Aktif)';
        activeCircuitsList.forEach(cid => {
            let option = document.createElement('option');
            option.value = cid;
            option.text = cid;
            if (cid === 'Circuit 21') option.selected = true;
            optGroupActive.appendChild(option);
        });
        circuitSelect.appendChild(optGroupActive);

        // Group 2: Semua Circuit (1 - 110)
        const optGroupAll = document.createElement('optgroup');
        optGroupAll.label = 'Semua Circuit (1 - 110)';
        for (let i = 1; i <= 110; i++) {
            let option = document.createElement('option');
            option.value = `Circuit ${i}`;
            option.text = `Circuit ${i}`;
            optGroupAll.appendChild(option);
        }
        circuitSelect.appendChild(optGroupAll);

        // Helper Status: Active Charging, Rest Charging, Off Charging
        function getStatusInfo(amp, volt) {
            const numAmp = (amp !== null && amp !== undefined && amp !== '') ? parseFloat(amp) : 0;
            const numVolt = (volt !== null && volt !== undefined && volt !== '') ? parseFloat(volt) : 0;
            
            const hasAmp = !isNaN(numAmp) && numAmp > 0;
            const hasVolt = !isNaN(numVolt) && numVolt > 0;
            
            if (hasAmp && hasVolt) {
                // Ada nilai Ampere dan Voltase terbaca -> Active Charging (Hijau)
                return {
                    status: 'Active Charging',
                    className: 'status-active',
                    icon: `<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>`
                };
            } else if (hasVolt && !hasAmp) {
                // Hanya Voltase terbaca, Ampere tidak terbaca -> Rest Charging (Kuning)
                return {
                    status: 'Rest Charging',
                    className: 'status-rest',
                    icon: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="10" y1="15" x2="10" y2="9"></line><line x1="14" y1="15" x2="14" y2="9"></line></svg>`
                };
            } else {
                // Tidak ada Voltase atau Ampere terbaca -> Off Charging (Simbol & Tulisan Merah)
                return {
                    status: 'Off Charging',
                    className: 'status-off',
                    icon: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>`
                };
            }
        }

        function updateStatusDisplay(amp, volt) {
            const statusInfo = getStatusInfo(amp, volt);
            const badge = document.getElementById('currentStatusBadge');
            if (badge) {
                badge.className = `status-badge-inline ${statusInfo.className}`;
                document.getElementById('currentStatusIcon').innerHTML = statusInfo.icon;
                document.getElementById('currentStatusText').innerText = statusInfo.status;
            }
        }

        // Kalau ganti circuit di mode Live, langsung update tanpa perlu klik apply
        circuitSelect.addEventListener('change', function() {
            document.getElementById('currentCircuitTitle').innerText = this.value;
            if (isLiveMode) {
                fetchData();
            }
        });

        let isLiveMode = true;
        let fetchIntervalId = null;

        const options = {
            series: [
                { name: 'Ampere', data: [] },
                { name: 'Voltase', data: [] }
            ],
            chart: {
                type: 'area',
                height: 450,
                background: '#ffffff',
                toolbar: { show: true, tools: { download: true, selection: true, zoom: true, pan: true } },
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            colors: ['#0ea5e9', '#10b981'],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.0, stops: [0, 100] }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2.5 },
            xaxis: {
                type: 'datetime',
                labels: { style: { colors: '#64748b', fontFamily: 'Inter' }, datetimeUTC: false },
                axisBorder: { show: true, color: '#e2e8f0' },
                axisTicks: { show: true, color: '#e2e8f0' }
            },
            yaxis: [
                {
                    seriesName: 'Ampere',
                    labels: { style: { colors: '#0ea5e9', fontFamily: 'Inter' }, formatter: (val) => val.toFixed(1) },
                    min: 0,
                    max: function(max) { return max > 22 ? max : 22; },
                    tickAmount: 11,
                    title: { text: 'Ampere (A)', style: { color: '#0ea5e9', fontFamily: 'Inter', fontWeight: 600 } }
                },
                {
                    opposite: true,
                    seriesName: 'Voltase',
                    labels: { style: { colors: '#10b981', fontFamily: 'Inter' }, formatter: (val) => val.toFixed(1) },
                    min: 0,
                    tickAmount: 11,
                    title: { text: 'Voltase (V)', style: { color: '#10b981', fontFamily: 'Inter', fontWeight: 600 } }
                }
            ],
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
            },
            annotations: {
                yaxis: []
            },
            theme: { mode: 'light' },
            tooltip: {
                x: { format: 'dd MMM yyyy HH:mm:ss' },
                y: [
                    { formatter: function(val) { return val.toFixed(1) + ' A'; } },
                    { formatter: function(val) { return val.toFixed(1) + ' V'; } }
                ]
            }
        };

        const chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

        async function fetchData() {
            try {
                let url = 'api.php';
                const activeCircuit = document.getElementById('circuit').value;
                const interval = document.getElementById('interval').value;
                
                let params = `?circuit=${encodeURIComponent(activeCircuit)}&interval=${interval}`;
                
                if (!isLiveMode) {
                    const start = document.getElementById('startDate').value;
                    const end = document.getElementById('endDate').value;
                    if(start && end) {
                        params += `&start=${start}&end=${end}`;
                    }
                }

                const response = await fetch(url + params);
                const result = await response.json();
                
                if (result.error) {
                    showError(result.error);
                    updateStatusDisplay(null, null);
                    return;
                }

                if (result.length > 0) {
                    document.getElementById('errorMsg').style.display = 'none';
                    
                    const chartDataAmp = result.map(item => [new Date(item.time).getTime(), item.amp]);
                    const chartDataVolt = result.map(item => [new Date(item.time).getTime(), item.voltase]);
                    
                    chart.updateSeries([
                        { name: 'Ampere', data: chartDataAmp },
                        { name: 'Voltase', data: chartDataVolt }
                    ]);

                    const latestAmp = result[result.length - 1].amp;
                    const latestVolt = result[result.length - 1].voltase;
                    
                    const elCurrent = document.getElementById('currentAmp');
                    const elVolt = document.getElementById('currentVoltase');
                    
                    elCurrent.innerText = latestAmp !== null && latestAmp !== undefined ? parseFloat(latestAmp).toFixed(1) : '--';
                    elVolt.innerText = latestVolt !== null && latestVolt !== undefined ? parseFloat(latestVolt).toFixed(1) : '--';

                    if (latestAmp > 20) {
                        elCurrent.style.color = 'var(--danger)';
                    } else {
                        elCurrent.style.color = 'var(--primary)';
                    }

                    // Update Status Charging
                    updateStatusDisplay(latestAmp, latestVolt);
                } else {
                    chart.updateSeries([{ data: [] }, { data: [] }]);
                    document.getElementById('currentAmp').innerText = '--';
                    document.getElementById('currentVoltase').innerText = '--';
                    updateStatusDisplay(null, null);
                    showError("Tidak ada data pada rentang waktu / Circuit tersebut.");
                }
            } catch (error) {
                showError("Gagal mengambil data dari server database.");
                updateStatusDisplay(null, null);
                console.error(error);
            }
        }

        function applyFilter() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            const interval = document.getElementById('interval').value;
            const activeCircuit = document.getElementById('circuit').value;
            
            const intervalTexts = {'raw': 'Raw Data', '1m': '1 Menit', '10m': '10 Menit', '30m': '30 Menit', '1h': '1 Jam'};
            document.getElementById('currentCircuitTitle').innerText = activeCircuit;

            // Jika tanggal Kosong, maka ini adalah filter Live Data (hanya ganti interval / circuit)
            if (!start || !end) {
                isLiveMode = true;
                
                // Ubah UI Indicator ke Live
                const badge = document.getElementById('liveIndicator');
                badge.style.background = 'rgba(16, 185, 129, 0.1)';
                badge.style.color = 'var(--success)';
                badge.innerHTML = `<div class="pulse-dot"></div><span>Live Mode</span>`;
                
                document.getElementById('btnReset').style.display = 'none';
                document.getElementById('chartSubtitle').innerText = `Menampilkan data real-time terakhir (Per ${intervalTexts[interval]})`;
                
                // Tarik data dengan interval baru
                fetchData();
                return;
            }

            // Mode History Aktif (Jika tanggal diisi)
            isLiveMode = false;
            clearInterval(fetchIntervalId); // Hentikan polling real-time saat lihat histori
            
            // Ubah UI Indicator
            const badge = document.getElementById('liveIndicator');
            badge.style.background = 'rgba(100, 116, 139, 0.1)';
            badge.style.color = '#64748b';
            badge.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> Historical Data`;
            
            document.getElementById('btnReset').style.display = 'flex';
            document.getElementById('chartSubtitle').innerText = `Data Histori dari ${start.replace('T', ' ')} s/d ${end.replace('T', ' ')} (Per ${intervalTexts[interval]})`;

            // Buat Query Param
            fetchData();
        }

        function resetToLive() {
            isLiveMode = true;
            const activeCircuit = document.getElementById('circuit').value;
            document.getElementById('currentCircuitTitle').innerText = activeCircuit;
            
            // Reset input tanggal
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            
            // Kembalikan UI ke Live
            const badge = document.getElementById('liveIndicator');
            badge.style.background = 'rgba(16, 185, 129, 0.1)';
            badge.style.color = 'var(--success)';
            badge.innerHTML = `<div class="pulse-dot"></div><span>Live Mode</span>`;
            
            document.getElementById('btnReset').style.display = 'none';
            document.getElementById('chartSubtitle').innerText = 'Menampilkan data real-time terakhir';

            // Mulai ulang fetching
            fetchData();
            
            // Interval otomatis realtime tiap 3 detik
            fetchIntervalId = setInterval(() => {
                if (isLiveMode) {
                    fetchData();
                }
            }, 3000);
        }

        function showError(msg) {
            const err = document.getElementById('errorMsg');
            err.innerText = msg;
            err.style.display = 'block';
        }

        // --- INISIALISASI ---
        document.getElementById('chartSubtitle').innerText = 'Menampilkan data real-time terakhir';
        fetchData(); // Panggilan pertama chart
        
        // Interval auto update realtime tiap 3 detik
        fetchIntervalId = setInterval(() => {
            if (isLiveMode) {
                fetchData();
            }
        }, 3000);

    </script>
</body>
</html>
