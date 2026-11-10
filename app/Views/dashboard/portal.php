<?php
    $cbiLogoPath = FCPATH . 'assets/images/cbi-logo.png';
    $cbiLogoSrc = is_file($cbiLogoPath) 
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($cbiLogoPath)) 
        : base_url('assets/images/cbi-logo.png');

    $lineACartoonPath = FCPATH . 'assets/images/line-a-cartoon.svg';
    $lineACartoonSrc = is_file($lineACartoonPath)
        ? 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($lineACartoonPath))
        : base_url('assets/images/line-a-cartoon.svg');

    $lineFCartoonPath = FCPATH . 'assets/images/line-f-cartoon.svg';
    $lineFCartoonSrc = is_file($lineFCartoonPath)
        ? 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($lineFCartoonPath))
        : base_url('assets/images/line-f-cartoon.svg');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= esc($title ?? 'WETCHARGE — Katalog Wet Charging | PT Century Batteries Indonesia') ?></title>
    
    <!-- Favicon (Battery Electric Charging Themed) -->
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>" />
    <link rel="alternate icon" type="image/png" href="<?= base_url('assets/images/favicon-charging.png') ?>" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }

        .font-mono-tech {
            font-family: 'JetBrains Mono', monospace;
        }

        .bg-grid-pattern {
            background-size: 40px 40px;
            background-image:
                linear-gradient(to right, rgba(226, 232, 240, 0.75) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(226, 232, 240, 0.75) 1px, transparent 1px);
        }

        .text-outline {
            -webkit-text-stroke: 2px #0047AB;
            color: transparent;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0%);
            }
            100% {
                transform: translateX(-50%);
            }
        }

        .animate-marquee {
            display: flex;
            width: max-content;
            animation: marquee 35s linear infinite;
        }

        .animate-marquee:hover {
            animation-play-state: paused;
        }

        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.45);
            }
            70% {
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .pulse-emerald {
            animation: pulse-ring 2s infinite;
        }

        /* Entrance Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .anim-header { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .anim-card-1 { animation: fadeInUp 0.65s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards; opacity: 0; }
        .anim-card-2 { animation: fadeInUp 0.65s cubic-bezier(0.16, 1, 0.3, 1) 0.2s forwards; opacity: 0; }
    </style>
</head>

<body class="bg-grid-pattern antialiased selection:bg-blue-700 selection:text-white min-h-screen flex flex-col justify-between">

    <!-- TOP NAVBAR -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all duration-200">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <!-- Brand Logo with CBI Official Company Logo -->
            <a href="<?= site_url('/') ?>" class="flex items-center gap-3.5 group">
                <img src="<?= $cbiLogoSrc ?>" alt="Logo PT Century Batteries Indonesia (CBI)" class="w-11 h-11 object-contain drop-shadow-sm transition-transform duration-300 group-hover:scale-105" />
                <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-black text-xl tracking-tight leading-none text-slate-900 group-hover:text-[#0047AB] transition">WETCHARGE</span>
                        <span class="bg-[#0047AB] text-white text-[10px] font-bold px-1.5 py-0.5 rounded tracking-wide font-mono-tech">CBI</span>
                    </div>
                    <div class="font-mono-tech text-[10px] tracking-widest text-slate-500 mt-1 font-medium">PT CENTURY BATTERIES INDONESIA</div>
                </div>
            </a>

            <!-- Right Controls: Digital Realtime Clock -->
            <div class="flex items-center gap-2.5 px-3.5 py-1.5 rounded-xl bg-slate-100/90 border border-slate-200 font-mono-tech text-xs text-slate-700 shadow-sm">
                <svg class="w-4 h-4 text-[#0047AB]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span id="liveClock" class="font-bold tracking-wider text-slate-800 text-xs sm:text-sm">--:--:-- WIB</span>
            </div>
        </div>
    </header>

    <!-- MAIN CATALOG CONTAINER -->
    <main class="max-w-7xl mx-auto px-6 py-10 lg:py-14 w-full flex-grow flex flex-col justify-center">
        
        <!-- Header Title Section -->
        <div class="text-center max-w-3xl mx-auto mb-10 anim-header">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-slate-200 shadow-sm mb-4">
                <span class="w-2 h-2 rounded-full bg-[#0047AB]"></span>
                <span class="font-mono-tech text-xs tracking-wider text-slate-700 font-semibold">PORTAL APLIKASI WET CHARGING</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.05] text-slate-900 mb-4">
                WET CHARGING <span class="text-outline">PORTAL</span>
            </h1>

            <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto">
                Pintu masuk aplikasi monitoring wet charging PT Century Batteries Indonesia. Pilih katalog lini formasi di bawah untuk membuka dashboard sirkuit secara realtime.
            </p>
        </div>

        <!-- 2 DIRECT CATALOG CARDS: WET A & WET F -->
        <div class="grid md:grid-cols-2 gap-8 lg:gap-10 max-w-5xl mx-auto w-full">
            
            <!-- KATALOG 1: WET A -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group anim-card-1">
                <div>
                    <!-- Visual Cartoon Banner (Line A) -->
                    <div class="relative h-64 bg-slate-100 overflow-hidden">
                        <img src="<?= $lineACartoonSrc ?>"
                            alt="Kartun Ilustrasi Charging Line A" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" />
                        
                        <!-- Badges on Image -->
                        <div class="absolute top-4 left-4 flex items-center gap-2">
                            <span class="bg-[#0047AB] text-white font-mono-tech text-xs font-bold px-3.5 py-1.5 rounded-lg shadow-sm">
                                TIPE A
                            </span>
                            <span class="bg-white/95 backdrop-blur-md text-slate-800 font-mono-tech text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200/80 shadow-sm">
                                110 Sirkuit
                            </span>
                        </div>

                        <div class="absolute bottom-4 right-4 bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-lg border border-slate-200/80 text-right shadow-sm">
                            <span class="font-mono-tech text-[10px] text-slate-500 uppercase tracking-wider block">Target SG</span>
                            <span class="font-mono-tech text-xs font-bold text-slate-900">1.260 @ 20°C</span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-8">
                        <h2 class="text-3xl font-black text-slate-900 group-hover:text-[#0047AB] transition mb-6">
                            Charging Line A
                        </h2>

                        <!-- Tech Specs Grid -->
                        <div class="grid grid-cols-2 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 font-mono-tech text-xs">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Arus Awal</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">12.5 A</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Waktu Formasi</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">18–24 Jam</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Metode Charge</span>
                                <span class="font-semibold text-slate-800 text-xs mt-0.5 block">CC Multi-Step</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Batas Suhu</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">&lt; 45 °C</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Action Footer -->
                <div class="px-8 pb-8 pt-2">
                    <a href="<?= site_url('charging/a') ?>"
                        class="w-full flex items-center justify-center gap-3 py-4 rounded-2xl bg-[#0047AB] text-white font-mono-tech text-xs font-bold tracking-wider hover:bg-blue-800 transition-all duration-200 shadow-md hover:shadow-xl hover:shadow-blue-500/25 active:scale-[0.98]">
                        <span>BUKA DASHBOARD WET A</span>
                        <span class="text-sm transition-transform duration-200 group-hover:translate-x-1.5">➔</span>
                    </a>
                </div>
            </div>

            <!-- KATALOG 2: WET F -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group anim-card-2">
                <div>
                    <!-- Visual Cartoon Banner (Line F) -->
                    <div class="relative h-64 bg-slate-900 overflow-hidden">
                        <img src="<?= $lineFCartoonSrc ?>"
                            alt="Kartun Ilustrasi Charging Line F" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" />
                        
                        <!-- Badges on Image -->
                        <div class="absolute top-4 left-4 flex items-center gap-2">
                            <span class="bg-indigo-950 text-indigo-300 border border-indigo-500/30 font-mono-tech text-xs font-bold px-3.5 py-1.5 rounded-lg shadow-sm">
                                TIPE F
                            </span>
                            <span class="bg-white/95 backdrop-blur-md text-slate-800 font-mono-tech text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200/80 shadow-sm">
                                110 Sirkuit
                            </span>
                        </div>

                        <div class="absolute bottom-4 right-4 bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-lg border border-slate-200/80 text-right shadow-sm">
                            <span class="font-mono-tech text-[10px] text-slate-500 uppercase tracking-wider block">Target SG</span>
                            <span class="font-mono-tech text-xs font-bold text-slate-900">1.280 @ 20°C</span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-8">
                        <h2 class="text-3xl font-black text-slate-900 group-hover:text-blue-700 transition mb-6">
                            Charging Line F
                        </h2>

                        <!-- Tech Specs Grid -->
                        <div class="grid grid-cols-2 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 font-mono-tech text-xs">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Arus Fase 1</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">15.0 A</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Waktu Formasi</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">12–16 Jam</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Metode Charge</span>
                                <span class="font-semibold text-slate-800 text-xs mt-0.5 block">Fast 3-Phase CC/CV</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Batas Suhu</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">&lt; 42 °C</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Action Footer -->
                <div class="px-8 pb-8 pt-2">
                    <a href="<?= site_url('charging/f') ?>"
                        class="w-full flex items-center justify-center gap-3 py-4 rounded-2xl bg-slate-900 text-white font-mono-tech text-xs font-bold tracking-wider hover:bg-slate-800 transition-all duration-200 shadow-md hover:shadow-xl hover:shadow-slate-800/25 active:scale-[0.98]">
                        <span>BUKA DASHBOARD WET F</span>
                        <span class="text-sm transition-transform duration-200 group-hover:translate-x-1.5">➔</span>
                    </a>
                </div>
            </div>

        </div>

    </main>

    <!-- FOOTER (SIMPLE & CLEAN) -->
    <footer class="bg-slate-900 text-slate-300 py-6 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4 font-mono-tech text-xs">
            <div class="flex items-center gap-3">
                <div class="bg-white p-1 rounded-md shadow-sm">
                    <img src="<?= $cbiLogoSrc ?>" alt="Logo PT Century Batteries Indonesia" class="w-6 h-6 object-contain" />
                </div>
                <span class="font-bold text-white tracking-wide">PT CENTURY BATTERIES INDONESIA</span>
            </div>
            <div class="text-slate-400">
                &copy; <?= date('Y') ?> PT Century Batteries Indonesia. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- DIGITAL CLOCK SCRIPT -->
    <script>
        function updateClock() {
            const el = document.getElementById('liveClock');
            if (!el) return;
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            el.textContent = `${hours}:${minutes}:${seconds} WIB`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>
