<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Portal Monitoring Charging - Line Selection') ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: rgba(17, 24, 39, 0.75);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-a: #0ea5e9;
            --accent-a-glow: rgba(14, 165, 233, 0.35);
            --accent-f: #8b5cf6;
            --accent-f-glow: rgba(139, 92, 246, 0.35);
            --success: #10b981;
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font-family);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow-x: hidden;
            padding: 2.5rem 1.5rem 1.5rem;
        }

        /* Ambient background glow */
        body::before {
            content: '';
            position: absolute;
            top: -15%;
            left: 20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            top: -10%;
            right: 15%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
        }

        .container {
            max-width: 1180px;
            margin: 0 auto;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        /* Top Header */
        .portal-header {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .system-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #38bdf8;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 1.25rem;
            backdrop-filter: blur(12px);
        }

        .system-pill .dot {
            width: 7px;
            height: 7px;
            background-color: var(--success);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--success);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        .portal-title {
            font-size: 2.75rem;
            font-weight: 900;
            letter-spacing: -0.03em;
            line-height: 1.15;
            background: linear-gradient(180deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.75rem;
        }

        .portal-subtitle {
            font-size: 1.05rem;
            color: var(--text-secondary);
            max-width: 650px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Live Clock & Info */
        .portal-meta {
            margin-top: 1.25rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }

        .portal-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Selection Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .line-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.25rem;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(20px);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
            color: inherit;
        }

        .line-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            opacity: 0.8;
            transition: height 0.3s ease, opacity 0.3s ease;
        }

        /* Line A specific styling */
        .card-line-a::before {
            background: linear-gradient(90deg, #0284c7, #38bdf8);
        }
        .card-line-a:hover {
            border-color: rgba(14, 165, 233, 0.4);
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -15px var(--accent-a-glow);
        }
        .card-line-a .icon-wrapper {
            background: rgba(14, 165, 233, 0.12);
            color: #38bdf8;
            border: 1px solid rgba(14, 165, 233, 0.25);
        }
        .card-line-a .badge-line {
            background: rgba(14, 165, 233, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(14, 165, 233, 0.3);
        }
        .card-line-a .action-btn {
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
            box-shadow: 0 4px 14px rgba(14, 165, 233, 0.4);
        }
        .card-line-a .action-btn:hover {
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.6);
        }

        /* Line F specific styling */
        .card-line-f::before {
            background: linear-gradient(90deg, #6d28d9, #a78bfa);
        }
        .card-line-f:hover {
            border-color: rgba(139, 92, 246, 0.4);
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -15px var(--accent-f-glow);
        }
        .card-line-f .icon-wrapper {
            background: rgba(139, 92, 246, 0.12);
            color: #a78bfa;
            border: 1px solid rgba(139, 92, 246, 0.25);
        }
        .card-line-f .badge-line {
            background: rgba(139, 92, 246, 0.15);
            color: #a78bfa;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }
        .card-line-f .action-btn {
            background: linear-gradient(135deg, #6d28d9 0%, #8b5cf6 100%);
            box-shadow: 0 4px 14px rgba(139, 92, 246, 0.4);
        }
        .card-line-f .action-btn:hover {
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.6);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.75rem;
        }

        .icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .line-card:hover .icon-wrapper {
            transform: scale(1.08) rotate(3deg);
        }

        .badge-line {
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 50px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .card-content h2 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .card-desc {
            font-size: 0.925rem;
            color: var(--text-secondary);
            line-height: 1.55;
            margin-bottom: 1.75rem;
        }

        /* Metric Highlights inside cards */
        .metrics-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 14px;
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .metric-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .metric-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .metric-val {
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
        }

        .action-btn {
            width: 100%;
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .action-btn svg {
            transition: transform 0.25s ease;
        }

        .line-card:hover .action-btn svg {
            transform: translateX(4px);
        }

        /* Footer */
        .portal-footer {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            color: var(--text-muted);
            font-size: 0.825rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-status {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot-active {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
        }

        @media (max-width: 860px) {
            .cards-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            .portal-title {
                font-size: 2.2rem;
            }
            .portal-footer {
                flex-direction: column;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- Header -->
        <header class="portal-header">
            <div class="system-pill">
                <span class="dot"></span>
                <span>Production Monitoring Center</span>
            </div>
            <h1 class="portal-title">Pilih Lini Charging</h1>
            <p class="portal-subtitle">
                Sistem pemantauan terpusat untuk menganalisis performa pengisian arus (Ampere), tegangan (Voltase), dan status sirkuit secara realtime.
            </p>
            <div class="portal-meta">
                <span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span id="liveClock">Memuat waktu...</span>
                </span>
                <span>•</span>
                <span>Database: SQL Server (prod_control)</span>
            </div>
        </header>

        <!-- Selection Cards -->
        <div class="cards-grid">
            
            <!-- Card 1: Charging Line A -->
            <a href="<?= site_url('charging/a') ?>" class="line-card card-line-a">
                <div>
                    <div class="card-top">
                        <div class="icon-wrapper">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="16" height="10" rx="2" ry="2"></rect>
                                <line x1="22" y1="11" x2="22" y2="13"></line>
                                <polyline points="9 10 7 13 11 13 9 16"></polyline>
                            </svg>
                        </div>
                        <span class="badge-line">Unit Line A</span>
                    </div>

                    <div class="card-content">
                        <h2>Charging Line A</h2>
                        <p class="card-desc">
                            Monitoring realtime arus dan tegangan sirkuit untuk Line Charging A. Dilengkapi dengan mode histori dan inspeksi interval.
                        </p>
                    </div>

                    <div class="metrics-row">
                        <div class="metric-item">
                            <span class="metric-label">Tabel</span>
                            <span class="metric-val" style="color: #38bdf8;">z_amp_chargingA</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-label">Sirkuit</span>
                            <span class="metric-val">1 - 110 ID</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-label">Update</span>
                            <span class="metric-val" style="color: var(--success);">Tiap 3 Detik</span>
                        </div>
                    </div>
                </div>

                <div class="action-btn">
                    <span>Buka Dashboard Line A</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </div>
            </a>

            <!-- Card 2: Charging Line F -->
            <a href="<?= site_url('charging/f') ?>" class="line-card card-line-f">
                <div>
                    <div class="card-top">
                        <div class="icon-wrapper">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                            </svg>
                        </div>
                        <span class="badge-line">Unit Line F</span>
                    </div>

                    <div class="card-content">
                        <h2>Charging Line F</h2>
                        <p class="card-desc">
                            Monitoring realtime arus dan tegangan sirkuit untuk Line Charging F. Pemantauan terisolasi dengan data langsung dari unit F.
                        </p>
                    </div>

                    <div class="metrics-row">
                        <div class="metric-item">
                            <span class="metric-label">Tabel</span>
                            <span class="metric-val" style="color: #a78bfa;">z_amp_chargingF</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-label">Sirkuit</span>
                            <span class="metric-val">1 - 110 ID</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-label">Update</span>
                            <span class="metric-val" style="color: var(--success);">Tiap 3 Detik</span>
                        </div>
                    </div>
                </div>

                <div class="action-btn">
                    <span>Buka Dashboard Line F</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </div>
            </a>

        </div>

        <!-- Footer -->
        <footer class="portal-footer">
            <div class="footer-status">
                <span class="status-dot-active"></span>
                <span>Production Server Active &bull; CodeIgniter v<?= CodeIgniter\CodeIgniter::CI_VERSION ?></span>
            </div>
            <div>
                <span>&copy; <?= date('Y') ?> Production Charging Control System</span>
            </div>
        </footer>

    </div>

    <script>
        // Update Live Clock
        function updateClock() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            };
            document.getElementById('liveClock').textContent = now.toLocaleDateString('id-ID', options);
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>
