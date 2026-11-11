<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard Monitoring - Amp Charging ' . ($line ?? 'A')) ?></title>
    
    <!-- Favicon (Battery Electric Charging Themed) -->
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>" />
    <link rel="alternate icon" type="image/png" href="<?= base_url('assets/images/favicon-charging.png') ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- jsPDF for direct PDF Report Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <?php 
        $currentLine = strtoupper($line ?? 'A');
        $primaryColor = ($currentLine === 'F') ? '#8b5cf6' : '#0ea5e9';
        $primaryLight = ($currentLine === 'F') ? 'rgba(139, 92, 246, 0.12)' : 'rgba(14, 165, 233, 0.12)';
        $primaryHover = ($currentLine === 'F') ? '#7c3aed' : '#0284c7';

        $cbiLogoPath = FCPATH . 'assets/images/cbi-logo.png';
        $cbiLogoSrc = is_file($cbiLogoPath) 
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($cbiLogoPath)) 
            : base_url('assets/images/cbi-logo.png');
    ?>

    <style>
        :root {
            --bg-color: #f8fafc;
            --panel-bg: #ffffff;
            --border-color: #e2e8f0;
            --primary: <?= $primaryColor ?>;
            --primary-light: <?= $primaryLight ?>;
            --primary-hover: <?= $primaryHover ?>;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
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
            padding: 1.5rem 1rem 2.5rem;
        }

        /* Top Navigation Bar */
        .top-nav {
            width: 100%;
            max-width: 95%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding: 0 0.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn-portal-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
        }

        .btn-portal-back:hover {
            color: var(--text-main);
            background: #f1f5f9;
            transform: translateX(-2px);
        }

        /* Line Switcher Tabs */
        .line-switcher {
            display: flex;
            background: #e2e8f0;
            padding: 4px;
            border-radius: 12px;
            gap: 4px;
        }

        .line-tab {
            padding: 6px 18px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            color: var(--text-muted);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .line-tab.active-a {
            background: #0ea5e9;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(14, 165, 233, 0.4);
        }

        .line-tab.active-f {
            background: #8b5cf6;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(139, 92, 246, 0.4);
        }

        .line-tab:not(.active-a):not(.active-f):hover {
            background: rgba(255, 255, 255, 0.6);
            color: var(--text-main);
        }

        /* Main Header */
        header {
            width: 100%;
            max-width: 95%;
            margin-bottom: 1.5rem;
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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .line-badge-hero {
            font-size: 0.8rem;
            font-weight: 800;
            background: var(--primary);
            color: #ffffff;
            padding: 3px 10px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px var(--primary-light);
        }

        .ci4-badge {
            font-size: 0.75rem;
            font-weight: 700;
            background: #ef4444;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
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
            padding: 0.6rem 1.25rem;
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
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
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

        .btn-export {
            background-color: #059669;
            color: white;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.2);
        }

        .btn-export:hover {
            background-color: #047857;
            transform: translateY(-1px);
        }

        .export-dropdown-wrapper {
            position: relative;
            display: inline-block;
        }

        .export-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            min-width: 250px;
            z-index: 1000;
            padding: 6px;
        }

        .export-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: #334155;
            font-size: 0.85rem;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .export-item:hover {
            background-color: #f8fafc;
        }

        .btn-demo {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: #b45309;
            font-size: 0.85rem;
        }

        .btn-demo:hover {
            background: rgba(245, 158, 11, 0.25);
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
            font-size: 1.15rem;
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
            font-size: 2.3rem;
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
            padding: 1rem 1.25rem;
            border-radius: 10px;
            border: 1px solid #fecaca;
            margin-bottom: 1rem;
            display: none;
            font-weight: 500;
            font-size: 0.9rem;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        #chart {
            width: 100%;
            min-height: 460px;
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
            color: #dc2626;
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

        @media (max-width: 860px) {
            header, .card-header { flex-direction: column; gap: 1rem; align-items: flex-start; }
            .current-value-box { text-align: left; }
            .filter-card { flex-direction: column; align-items: stretch; }
            .top-nav { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <div class="top-nav">
        <a href="<?= site_url('/') ?>" class="btn-portal-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Portal Pemilihan</span>
        </a>
    </div>

    <!-- Main Header -->
    <header>
        <div style="display: flex; align-items: center; gap: 14px;">
            <img src="<?= $cbiLogoSrc ?>" alt="Logo PT Century Batteries Indonesia (CBI)" style="width: 46px; height: 46px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.08));" />
            <div class="header-title">
                <h1>
                    Production Dashboard 
                    <span class="line-badge-hero">Line <?= esc($currentLine) ?></span>
                    <span class="ci4-badge">CI4</span>
                </h1>
                <div class="subtitle">
                    PT Century Batteries Indonesia &bull; Ampere & Voltase Charging <?= esc($currentLine) ?>
                </div>
            </div>
        </div>
        <div class="header-actions">
            <button class="btn btn-demo" id="btnToggleDemo" onclick="toggleDemoMode()" title="Toggle Simulasi Data saat offline">
                ⚡ Simulasi Data: OFF
            </button>
            <div class="live-badge" id="liveIndicator">
                <div class="pulse-dot"></div>
                <span>Live Mode</span>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        
        <!-- Filter Bar -->
        <div class="filter-card">
            
            <div class="filter-group">
                <label for="circuit">Circuit ID</label>
                <select id="circuit" class="filter-control" style="font-weight: 600; color: var(--primary); min-width: 170px;">
                    <optgroup label="⭐ 10 Circuit Terupdate (Aktif)">
                        <?php foreach ($activeCircuits as $cid): ?>
                            <option value="<?= esc($cid) ?>" <?= ($cid === ($defaultCircuit ?? 'Circuit 21')) ? 'selected' : '' ?>>
                                <?= esc($cid) ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="Semua Circuit (1 - <?= esc($totalCircuits) ?>)">
                        <?php for ($i = 1; $i <= $totalCircuits; $i++): ?>
                            <option value="Circuit <?= $i ?>">Circuit <?= $i ?></option>
                        <?php endfor; ?>
                    </optgroup>
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
                <!-- Export Dropdown -->
                <div class="export-dropdown-wrapper">
                    <button class="btn btn-export" onclick="toggleExportMenu(event)" id="btnExportMenu" title="Export Laporan PDF, Gambar Grafik, atau CSV">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Export</span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    
                    <div id="exportDropdown" class="export-dropdown-menu" style="display: none;">
                        <div onclick="exportPDF()" class="export-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            <div>
                                <div style="font-weight: 600; color: #1e293b;">Export Laporan PDF</div>
                                <div style="font-size: 0.72rem; color: #64748b;">Grafik & Rincian Telemetri</div>
                            </div>
                        </div>
                        <div onclick="exportPNG()" class="export-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            <div>
                                <div style="font-weight: 600; color: #1e293b;">Download Gambar (PNG)</div>
                                <div style="font-size: 0.72rem; color: #64748b;">File Gambar Grafik HD</div>
                            </div>
                        </div>
                        <div onclick="exportData()" class="export-item" style="border-top: 1px solid #f1f5f9; margin-top: 4px; padding-top: 8px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg>
                            <div>
                                <div style="font-weight: 600; color: #1e293b;">Export Data (CSV / Excel)</div>
                                <div style="font-size: 0.72rem; color: #64748b;">Dataset Tabel Mentah</div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-outline" onclick="resetToLive()" id="btnReset" style="display: none;">
                    Kembali ke Live
                </button>
            </div>
        </div>

        <div class="error-message" id="errorMsg">
            <span id="errorText"></span>
            <button class="btn btn-demo" onclick="enableDemoMode()" style="padding: 4px 10px; font-size: 0.8rem;">
                Gunakan Mode Simulasi
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">
                        Trend Data Line <?= esc($currentLine) ?> - <span id="currentCircuitTitle" style="color: var(--primary);">Circuit 21</span>
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
        const CURRENT_LINE = '<?= esc($currentLine) ?>';
        const PRIMARY_CHART_COLOR = '<?= $primaryColor ?>';
        
        const API_BASE_URL = (() => {
            const configuredUrl = '<?= site_url('api/charging') ?>';
            try {
                const parsed = new URL(configuredUrl, window.location.origin);
                if (parsed.origin !== window.location.origin) {
                    const cleanPath = window.location.pathname.replace(/\/+$/, '');
                    return cleanPath ? cleanPath + '/api/charging' : '/api/charging';
                }
                return configuredUrl;
            } catch (e) {
                return 'api/charging';
            }
        })();

        const circuitSelect = document.getElementById('circuit');
        let isLiveMode = true;
        let isFetching = false;
        let pollingTimer = null;
        const POLL_INTERVAL = 5000; // Smart interval: 5 detik (optimal untuk telemetri industri)
        let isDemoMode = false;
        let currentLoadedData = [];

        function toggleExportMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('exportDropdown');
            menu.style.display = (menu.style.display === 'none' || !menu.style.display) ? 'block' : 'none';
        }

        function closeExportMenu() {
            const menu = document.getElementById('exportDropdown');
            if (menu) menu.style.display = 'none';
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.export-dropdown-wrapper')) {
                closeExportMenu();
            }
        });

        async function exportPNG() {
            closeExportMenu();
            try {
                const activeCircuit = document.getElementById('circuit').value || 'Circuit';
                const safeCircuitName = activeCircuit.toLowerCase().replace(/[^a-z0-9]/g, '_');
                const dateStr = new Date().toISOString().slice(0, 10);
                
                const dataURI = await chart.dataURI();
                const link = document.createElement('a');
                link.href = dataURI.imgURI;
                link.download = `grafik_charging_line_${CURRENT_LINE.toLowerCase()}_${safeCircuitName}_${dateStr}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (e) {
                console.error('Export PNG failed:', e);
                alert('Gagal mendownload gambar grafik: ' + e.message);
            }
        }

        async function exportPDF() {
            closeExportMenu();
            try {
                const activeCircuit = document.getElementById('circuit').value || 'Circuit';
                const safeCircuitName = activeCircuit.toLowerCase().replace(/[^a-z0-9]/g, '_');
                const now = new Date();
                const dateStr = now.toISOString().slice(0, 10);
                const timeStr = now.toLocaleTimeString('id-ID');

                const voltText = document.getElementById('currentVoltase')?.innerText || '--';
                const ampText = document.getElementById('currentAmp')?.innerText || '--';
                const statusText = document.getElementById('currentStatusText')?.innerText || 'Active Charging';

                // Ambil gambar canvas grafik dari ApexCharts
                const { imgURI } = await chart.dataURI();

                if (window.jspdf && window.jspdf.jsPDF) {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

                    // Header bar biru CBI
                    doc.setFillColor(0, 71, 171); // #0047AB Royal Blue
                    doc.rect(0, 0, 297, 18, 'F');

                    // Header Text
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(13);
                    doc.setTextColor(255, 255, 255);
                    doc.text('PT CENTURY BATTERIES INDONESIA', 14, 12);
                    
                    doc.setFontSize(9.5);
                    doc.setFont('helvetica', 'normal');
                    doc.text(`WET CHARGING MONITORING SYSTEM - LINE ${CURRENT_LINE}`, 190, 12);

                    // Judul Laporan & Metadata
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(14);
                    doc.setTextColor(15, 23, 42); // slate-900
                    doc.text(`LAPORAN GRAFIK TELEMETRI: LINE ${CURRENT_LINE} - ${activeCircuit.toUpperCase()}`, 14, 28);

                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(9.5);
                    doc.setTextColor(71, 85, 105); // slate-600
                    doc.text(`Waktu Cetak: ${now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })} pk ${timeStr} WIB`, 14, 34);

                    // Metrik Telemetri Terakhir
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(10);
                    doc.setTextColor(0, 71, 171);
                    doc.text(`Ampere Terakhir: ${ampText} A`, 14, 42);
                    doc.setTextColor(16, 185, 129); // green
                    doc.text(`Voltase Terakhir: ${voltText} V`, 75, 42);
                    doc.setTextColor(15, 23, 42);
                    doc.text(`Status Charging: ${statusText}`, 140, 42);

                    // Garis Pembatas
                    doc.setDrawColor(226, 232, 240);
                    doc.line(14, 46, 283, 46);

                    // Sisipkan Gambar Grafik Resolusi Tinggi
                    doc.addImage(imgURI, 'PNG', 14, 50, 269, 135);

                    // Footer Dokumen
                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(8);
                    doc.setTextColor(148, 163, 184);
                    doc.text(`Internal Production Monitoring System - PT Century Batteries Indonesia (Hal 1/1)`, 14, 198);

                    doc.save(`laporan_grafik_charging_line_${CURRENT_LINE.toLowerCase()}_${safeCircuitName}_${dateStr}.pdf`);
                } else {
                    window.print();
                }
            } catch (e) {
                console.error('Export PDF failed:', e);
                alert('Gagal membuat file PDF: ' + e.message);
            }
        }

        function exportData() {
            closeExportMenu();
            if (!currentLoadedData || currentLoadedData.length === 0) {
                alert("Tidak ada data charging yang tersedia untuk diekspor.");
                return;
            }

            const activeCircuit = document.getElementById('circuit').value || 'Circuit';
            
            // Format CSV
            let csv = "Waktu,Line,Circuit ID,Ampere (A),Voltase (V),Status Charging\r\n";

            currentLoadedData.forEach(item => {
                const statusInfo = getStatusInfo(item.amp, item.voltase);
                const timeStr = item.time || '';
                const ampVal = (item.amp !== null && item.amp !== undefined) ? parseFloat(item.amp).toFixed(1) : '0.0';
                const voltVal = (item.voltase !== null && item.voltase !== undefined) ? parseFloat(item.voltase).toFixed(1) : '0.0';
                
                csv += `"${timeStr}","Line ${CURRENT_LINE}","${activeCircuit}","${ampVal}","${voltVal}","${statusInfo.status}"\r\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            const safeCircuitName = activeCircuit.toLowerCase().replace(/[^a-z0-9]/g, '_');
            const nowStr = new Date().toISOString().slice(0, 10);

            link.setAttribute("href", url);
            link.setAttribute("download", `charging_line_${CURRENT_LINE.toLowerCase()}_${safeCircuitName}_${nowStr}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function toggleDemoMode() {
            isDemoMode = !isDemoMode;
            updateDemoButton();
            fetchData();
        }

        function enableDemoMode() {
            isDemoMode = true;
            updateDemoButton();
            document.getElementById('errorMsg').style.display = 'none';
            fetchData();
        }

        function updateDemoButton() {
            const btn = document.getElementById('btnToggleDemo');
            if (isDemoMode) {
                btn.style.background = 'rgba(16, 185, 129, 0.15)';
                btn.style.color = '#059669';
                btn.style.borderColor = 'rgba(16, 185, 129, 0.4)';
                btn.innerText = '⚡ Simulasi Data: ON';
            } else {
                btn.style.background = 'rgba(245, 158, 11, 0.15)';
                btn.style.color = '#b45309';
                btn.style.borderColor = 'rgba(245, 158, 11, 0.35)';
                btn.innerText = '⚡ Simulasi Data: OFF';
            }
        }

        function getStatusInfo(amp, volt) {
            const numAmp = (amp !== null && amp !== undefined && amp !== '') ? parseFloat(amp) : 0;
            const numVolt = (volt !== null && volt !== undefined && volt !== '') ? parseFloat(volt) : 0;
            
            const hasAmp = !isNaN(numAmp) && numAmp > 0;
            const hasVolt = !isNaN(numVolt) && numVolt > 0;
            
            if (hasAmp && hasVolt) {
                return {
                    status: 'Active Charging',
                    className: 'status-active',
                    icon: `<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>`
                };
            } else if (hasVolt && !hasAmp) {
                return {
                    status: 'Rest Charging',
                    className: 'status-rest',
                    icon: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="10" y1="15" x2="10" y2="9"></line><line x1="14" y1="15" x2="14" y2="9"></line></svg>`
                };
            } else {
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

        circuitSelect.addEventListener('change', function() {
            document.getElementById('currentCircuitTitle').innerText = this.value;
            if (isLiveMode) {
                fetchData();
            }
        });

        const options = {
            series: [
                { name: 'Ampere', data: [] },
                { name: 'Voltase', data: [] }
            ],
            chart: {
                type: 'area',
                height: 460,
                background: '#ffffff',
                toolbar: { show: true, tools: { download: true, selection: true, zoom: true, pan: true } },
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            colors: [PRIMARY_CHART_COLOR, '#10b981'],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.0, stops: [0, 100] }
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
                    labels: { style: { colors: PRIMARY_CHART_COLOR, fontFamily: 'Inter' }, formatter: (val) => val.toFixed(1) },
                    min: 0,
                    max: function(max) { return max > 22 ? max : 22; },
                    tickAmount: 11,
                    title: { text: 'Ampere (A)', style: { color: PRIMARY_CHART_COLOR, fontFamily: 'Inter', fontWeight: 600 } }
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
            theme: { mode: 'light' },
            tooltip: {
                x: { format: 'dd MMM yyyy HH:mm:ss' },
                y: [
                    { formatter: function(val) { return (val !== null && val !== undefined) ? val.toFixed(1) + ' A' : '--'; } },
                    { formatter: function(val) { return (val !== null && val !== undefined) ? val.toFixed(1) + ' V' : '--'; } }
                ]
            }
        };

        const chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

        function scheduleNextPoll() {
            if (pollingTimer) clearTimeout(pollingTimer);
            if (isLiveMode && !document.hidden) {
                pollingTimer = setTimeout(fetchData, POLL_INTERVAL);
            }
        }

        // Page Visibility API: Stop polling saat user minimize browser atau pindah tab
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                if (pollingTimer) clearTimeout(pollingTimer);
            } else {
                if (isLiveMode && !isFetching) {
                    fetchData();
                }
            }
        });

        async function fetchData() {
            if (isFetching) return; // Request guard: anti-stacking / anti-traffic jam
            isFetching = true;

            try {
                const activeCircuit = document.getElementById('circuit').value;
                const interval = document.getElementById('interval').value;
                
                let params = `?circuit=${encodeURIComponent(activeCircuit)}&interval=${interval}&line=${encodeURIComponent(CURRENT_LINE)}`;
                if (isDemoMode) {
                    params += `&demo=1`;
                }
                
                if (!isLiveMode) {
                    const start = document.getElementById('startDate').value;
                    const end = document.getElementById('endDate').value;
                    if (start && end) {
                        params += `&start=${start}&end=${end}`;
                    }
                }

                const response = await fetch(API_BASE_URL + params);
                const result = await response.json();
                
                const dataSource = response.headers.get('X-Data-Source') || (isDemoMode ? 'demo' : 'live');
                const rawMsg = response.headers.get('X-DB-Message');
                const dbMessage = rawMsg ? decodeURIComponent(rawMsg) : '';
                
                // Update status indicators
                const liveBadge = document.getElementById('liveIndicator');
                const chartSub = document.getElementById('chartSubtitle');

                if (dataSource === 'live') {
                    if (liveBadge && isLiveMode) {
                        liveBadge.innerHTML = '<div class="pulse-dot" style="background:#10b981;"></div><span style="color:#059669; font-weight:700;">Live SQL Server</span>';
                        liveBadge.style.background = 'rgba(16, 185, 129, 0.12)';
                        liveBadge.style.borderColor = 'rgba(16, 185, 129, 0.35)';
                    }
                    if (chartSub && isLiveMode) {
                        chartSub.innerText = 'Live terhubung ke SQL Server';
                    }
                } else if (dataSource === 'preview_fallback' || dataSource === 'live_waiting') {
                    if (liveBadge && isLiveMode && !isDemoMode) {
                        liveBadge.innerHTML = '<div class="pulse-dot" style="background:#f59e0b;"></div><span style="color:#d97706; font-weight:700;">Siap Integrasi (Standby)</span>';
                        liveBadge.style.background = 'rgba(245, 158, 11, 0.12)';
                        liveBadge.style.borderColor = 'rgba(245, 158, 11, 0.35)';
                    }
                    if (chartSub && isLiveMode && !isDemoMode) {
                        chartSub.innerText = 'Menampilkan data telemetri real-time terakhir';
                    }
                } else if (isDemoMode) {
                    if (liveBadge) {
                        liveBadge.innerHTML = '<div class="pulse-dot" style="background:#0284c7;"></div><span style="color:#0284c7; font-weight:700;">Mode Simulasi Manual</span>';
                        liveBadge.style.background = 'rgba(2, 132, 199, 0.12)';
                        liveBadge.style.borderColor = 'rgba(2, 132, 199, 0.35)';
                    }
                }

                if (result.error && !Array.isArray(result)) {
                    showError(result.error);
                    updateStatusDisplay(null, null);
                    return;
                }

                const rows = Array.isArray(result) ? result : (result.data || []);
                currentLoadedData = rows;

                if (rows.length > 0) {
                    document.getElementById('errorMsg').style.display = 'none';
                    
                    const chartDataAmp = rows.map(item => [new Date(item.time).getTime(), item.amp]);
                    const chartDataVolt = rows.map(item => [new Date(item.time).getTime(), item.voltase]);
                    
                    // animate: false saat polling berkala menghemat CPU/GPU browser secara drastis
                    chart.updateSeries([
                        { name: 'Ampere', data: chartDataAmp },
                        { name: 'Voltase', data: chartDataVolt }
                    ], false);

                    const latestAmp = rows[rows.length - 1].amp;
                    const latestVolt = rows[rows.length - 1].voltase;
                    
                    const elCurrent = document.getElementById('currentAmp');
                    const elVolt = document.getElementById('currentVoltase');
                    
                    elCurrent.innerText = (latestAmp !== null && latestAmp !== undefined) ? parseFloat(latestAmp).toFixed(1) : '--';
                    elVolt.innerText = (latestVolt !== null && latestVolt !== undefined) ? parseFloat(latestVolt).toFixed(1) : '--';

                    if (latestAmp > 20) {
                        elCurrent.style.color = 'var(--danger)';
                    } else {
                        elCurrent.style.color = 'var(--primary)';
                    }

                    updateStatusDisplay(latestAmp, latestVolt);
                } else {
                    chart.updateSeries([{ data: [] }, { data: [] }], false);
                    document.getElementById('currentAmp').innerText = '--';
                    document.getElementById('currentVoltase').innerText = '--';
                    updateStatusDisplay(null, null);
                    if (!isLiveMode) {
                        showError("Tidak ada data pada rentang waktu / Circuit tersebut.");
                    }
                }
            } catch (error) {
                console.warn("Auto-detect polling:", error);
            } finally {
                isFetching = false;
                scheduleNextPoll();
            }
        }

        function applyFilter() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            const interval = document.getElementById('interval').value;
            const activeCircuit = document.getElementById('circuit').value;
            
            const intervalTexts = {'raw': 'Raw Data', '1m': '1 Menit', '10m': '10 Menit', '30m': '30 Menit', '1h': '1 Jam'};
            document.getElementById('currentCircuitTitle').innerText = activeCircuit;

            if (!start || !end) {
                isLiveMode = true;
                
                const badge = document.getElementById('liveIndicator');
                badge.style.background = 'rgba(16, 185, 129, 0.1)';
                badge.style.color = 'var(--success)';
                badge.innerHTML = `<div class="pulse-dot"></div><span>Live Mode</span>`;
                
                document.getElementById('btnReset').style.display = 'none';
                document.getElementById('chartSubtitle').innerText = `Menampilkan data real-time terakhir (Per ${intervalTexts[interval]})`;
                
                fetchData();
                return;
            }

            isLiveMode = false;
            if (pollingTimer) clearTimeout(pollingTimer);
            
            const badge = document.getElementById('liveIndicator');
            badge.style.background = 'rgba(100, 116, 139, 0.1)';
            badge.style.color = '#64748b';
            badge.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> Historical Data`;
            
            document.getElementById('btnReset').style.display = 'flex';
            document.getElementById('chartSubtitle').innerText = `Data Histori dari ${start.replace('T', ' ')} s/d ${end.replace('T', ' ')} (Per ${intervalTexts[interval]})`;

            fetchData();
        }

        function resetToLive() {
            isLiveMode = true;
            const activeCircuit = document.getElementById('circuit').value;
            document.getElementById('currentCircuitTitle').innerText = activeCircuit;
            
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            
            const badge = document.getElementById('liveIndicator');
            badge.style.background = 'rgba(16, 185, 129, 0.1)';
            badge.style.color = 'var(--success)';
            badge.innerHTML = `<div class="pulse-dot"></div><span>Live Mode</span>`;
            
            document.getElementById('btnReset').style.display = 'none';
            document.getElementById('chartSubtitle').innerText = 'Menampilkan data real-time terakhir';

            if (pollingTimer) clearTimeout(pollingTimer);
            fetchData();
        }

        function showError(msg) {
            const err = document.getElementById('errorMsg');
            const errText = document.getElementById('errorText');
            errText.innerText = msg;
            err.style.display = 'flex';
        }

        // Inisialisasi awal (otomatis menjadwalkan polling berikutnya secara pintar)
        document.getElementById('chartSubtitle').innerText = 'Menampilkan data real-time terakhir';
        fetchData();
    </script>
</body>
</html>
