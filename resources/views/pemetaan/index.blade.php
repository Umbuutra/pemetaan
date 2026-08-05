<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Pemetaan Sebaran Mahasiswa & Alumni - Pusim</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Leaflet.js GIS Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        },
                        dark: {
                            bg: '#0f172a',
                            card: '#1e293b',
                            border: '#334155',
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        #map {
            height: 500px;
            width: 100%;
            border-radius: 1rem;
            z-index: 10;
        }
        .glass-panel {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .custom-popup .leaflet-popup-content-wrapper {
            background: #1e293b;
            color: #f8fafc;
            border-radius: 0.75rem;
            border: 1px solid #334155;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
            padding: 0.5rem;
        }
        .custom-popup .leaflet-popup-tip {
            background: #1e293b;
        }
    </style>
</head>
<body class="bg-dark-bg text-slate-100 font-sans antialiased min-h-screen selection:bg-brand-500 selection:text-white">

    <!-- Header Navigation Bar -->
    <header class="sticky top-0 z-50 glass-panel border-b border-slate-800/80 px-6 py-4">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-cyan-400 flex items-center justify-center text-white shadow-lg shadow-brand-500/30">
                    <i class="fa-solid fa-map-location-dot text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">
                        Pusim Map System
                    </h1>
                    <p class="text-xs text-slate-400">Pemetaan Sebaran Mahasiswa & Alumni Berdasarkan Profesi (GIS)</p>
                </div>
            </div>

            <!-- Header Quick Stats & Auth Badge -->
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    Live GIS Connected
                </span>
                <a href="http://localhost:8080" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-database text-cyan-400"></i> phpMyAdmin
                </a>

                @auth
                    <div class="flex items-center gap-2 ml-2 pl-2 border-l border-slate-800">
                        <a href="{{ route('profil.edit') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-500/10 hover:bg-brand-500/20 text-brand-300 border border-brand-500/30 transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-user-pen text-brand-400"></i> Edit Profil
                        </a>
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-semibold text-white">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-brand-400 font-medium uppercase">{{ Auth::user()->role }}</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" title="Keluar" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 transition-colors flex items-center gap-1.5">
                                <i class="fa-solid fa-right-from-bracket"></i> Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="ml-2 px-4 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white shadow-md shadow-brand-500/20 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-right-to-bracket"></i> Masuk
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">

        <!-- Stat Counter Cards Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Card 1 -->
            <div class="glass-panel p-4 rounded-2xl flex items-center gap-3.5 border border-slate-800 hover:border-brand-500/40 transition-all group">
                <div class="w-11 h-11 rounded-xl bg-brand-500/10 text-brand-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Total Terdata</p>
                    <h3 class="text-xl font-bold text-white mt-0.5" id="stat-mahasiswa">0</h3>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="glass-panel p-4 rounded-2xl flex items-center gap-3.5 border border-slate-800 hover:border-cyan-500/40 transition-all group">
                <div class="w-11 h-11 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Fakultas</p>
                    <h3 class="text-xl font-bold text-white mt-0.5" id="stat-fakultas">0</h3>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="glass-panel p-4 rounded-2xl flex items-center gap-3.5 border border-slate-800 hover:border-purple-500/40 transition-all group">
                <div class="w-11 h-11 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Program Studi</p>
                    <h3 class="text-xl font-bold text-white mt-0.5" id="stat-prodi">0</h3>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="glass-panel p-4 rounded-2xl flex items-center gap-3.5 border border-slate-800 hover:border-emerald-500/40 transition-all group">
                <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-map-marked-alt"></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Provinsi Sebaran</p>
                    <h3 class="text-xl font-bold text-white mt-0.5" id="stat-wilayah">0</h3>
                </div>
            </div>

            <!-- Card 5 (Linearitas Rate) -->
            <div class="glass-panel p-4 rounded-2xl flex items-center gap-3.5 border border-slate-800 hover:border-amber-500/40 transition-all group">
                <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Linearitas Karir</p>
                    <h3 class="text-xl font-bold text-amber-400 mt-0.5" id="stat-linearitas">0%</h3>
                </div>
            </div>
        </div>

        <!-- Dynamic Filter Controls Bar -->
        <div class="glass-panel p-5 rounded-2xl border border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-filter text-brand-400"></i> Filter Interaktif Sebaran & Profesi
                </h3>
                <button id="btn-reset" class="text-xs text-slate-400 hover:text-white transition-colors flex items-center gap-1">
                    <i class="fa-solid fa-rotate-left"></i> Reset Filter
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <!-- Filter Fakultas -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Fakultas</label>
                    <select id="filter-fakultas" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-brand-500">
                        <option value="">Semua Fakultas</option>
                        @foreach($fakultas as $f)
                            <option value="{{ $f->id }}">{{ $f->nama_fakultas }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Program Studi -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Program Studi</label>
                    <select id="filter-prodi" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-brand-500">
                        <option value="">Semua Prodi</option>
                        @foreach($programStudi as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }} ({{ $p->jenjang }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Kategori Profesi -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Kategori Profesi</label>
                    <select id="filter-kategori-profesi" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-brand-500">
                        <option value="">Semua Kategori Profesi</option>
                        @foreach($kategoriProfesi as $kp)
                            <option value="{{ $kp->id }}">{{ $kp->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Keselarasan Karir -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Keselarasan Karir</label>
                    <select id="filter-keselarasan" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-brand-500">
                        <option value="">Semua Keselarasan</option>
                        <option value="selaras">✅ Selaras</option>
                        <option value="kurang_selaras">⚠️ Kurang Selaras</option>
                        <option value="tidak_selaras">❌ Tidak Selaras</option>
                    </select>
                </div>

                <!-- Filter Angkatan -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Angkatan</label>
                    <select id="filter-angkatan" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-brand-500">
                        <option value="">Semua Angkatan</option>
                        @foreach($angkatanList as $a)
                            <option value="{{ $a }}">{{ $a }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Provinsi -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Provinsi</label>
                    <select id="filter-provinsi" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-brand-500">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinsi as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->nama_provinsi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Interactive GIS Map Section -->
        <div class="glass-panel p-4 rounded-2xl border border-slate-800 relative">
            <div class="flex items-center justify-between px-2 mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-brand-500 animate-pulse"></span>
                    <h3 class="text-sm font-semibold text-white">Peta GIS Sebaran Tempat Kerja & Profesi Alumni</h3>
                </div>
                <span class="text-xs text-slate-400" id="map-counter-info">Menampilkan 0 Titik Lokasi</span>
            </div>

            <!-- Map Container -->
            <div id="map"></div>
        </div>

        <!-- ApexCharts Visual Analytics Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart 1: Bar Chart Top 10 Profesi -->
            <div class="glass-panel p-5 rounded-2xl border border-slate-800 lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-brand-400"></i> Top 10 Kategori Profesi Terbanyak
                    </h3>
                    <span class="text-[11px] text-slate-400">Statistik Profesi</span>
                </div>
                <div id="chart-profesi" class="min-h-[280px]"></div>
            </div>

            <!-- Chart 2: Gauge Linearitas Karir -->
            <div class="glass-panel p-5 rounded-2xl border border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-cyan-400"></i> Linearitas Karir dengan Prodi
                    </h3>
                </div>
                <div id="chart-linearitas" class="min-h-[280px]"></div>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="glass-panel rounded-2xl border border-slate-800 overflow-hidden">
            <div class="p-5 border-b border-slate-800/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-semibold text-white">Daftar Detail Sebaran Mahasiswa & Profesi</h3>
                    <p class="text-xs text-slate-400">Data mahasiswa, lokasi kerja, serta tingkat keselarasan prodi</p>
                </div>
                <div class="relative w-full md:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-xs text-slate-400"></i>
                    <input type="text" id="search-table" placeholder="Cari nama, NIM, profesi..." class="w-full bg-slate-900 border border-slate-700/80 rounded-xl pl-9 pr-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/80 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5">NIM & Nama</th>
                            <th class="px-5 py-3.5">Prodi & Fakultas</th>
                            <th class="px-5 py-3.5">Kategori Profesi</th>
                            <th class="px-5 py-3.5">Posisi & Perusahaan</th>
                            <th class="px-5 py-3.5 text-center">Keselarasan</th>
                            <th class="px-5 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="divide-y divide-slate-800/60">
                        <!-- Dynamic Rows -->
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-6 mt-12 text-center text-xs text-slate-500">
        <p>© 2026 Pusim Map System — Aplikasi Pemetaan Sebaran Mahasiswa (Laravel 13 & Docker)</p>
    </footer>

    <!-- Leaflet.js & MarkerCluster JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <!-- ApexCharts JS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // Initialize Leaflet Map centered on Indonesia
        const map = L.map('map', {
            zoomControl: true,
        }).setView([-2.548926, 118.014863], 5);

        // Dark Map Tile Layer (CartoDB Dark Matter)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO'
        }).addTo(map);

        const markersCluster = L.markerClusterGroup({
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });
        map.addLayer(markersCluster);

        // Chart Instances
        let chartProfesiInstance = null;
        let chartLinearitasInstance = null;

        // Custom Marker Icon SVG
        const customIcon = L.divIcon({
            className: 'custom-pin',
            html: `<div class="w-8 h-8 rounded-full bg-gradient-to-tr from-brand-600 to-cyan-400 border-2 border-white flex items-center justify-center text-white shadow-lg shadow-brand-500/50">
                    <i class="fa-solid fa-briefcase text-xs"></i>
                   </div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        // Load GIS & Analytics Data
        async function fetchMapData() {
            const fakultasId = document.getElementById('filter-fakultas').value;
            const prodiId = document.getElementById('filter-prodi').value;
            const katProfesiId = document.getElementById('filter-kategori-profesi').value;
            const keselarasan = document.getElementById('filter-keselarasan').value;
            const angkatan = document.getElementById('filter-angkatan').value;
            const provinsiId = document.getElementById('filter-provinsi').value;

            const url = new URL('/api/pemetaan/data', window.location.origin);
            if (fakultasId) url.searchParams.append('fakultas_id', fakultasId);
            if (prodiId) url.searchParams.append('program_studi_id', prodiId);
            if (katProfesiId) url.searchParams.append('kategori_profesi_id', katProfesiId);
            if (keselarasan) url.searchParams.append('keselarasan_prodi', keselarasan);
            if (angkatan) url.searchParams.append('angkatan', angkatan);
            if (provinsiId) url.searchParams.append('provinsi_id', provinsiId);

            try {
                const response = await fetch(url);
                const data = await response.json();

                updateStats(data.stats);
                updateMapMarkers(data.locations);
                updateApexCharts(data.profesi_distribution, data.keselarasan_breakdown, data.stats.linearitas_percentage);
                updateTable(data.locations);
            } catch (error) {
                console.error('Error fetching map data:', error);
            }
        }

        function updateStats(stats) {
            document.getElementById('stat-mahasiswa').innerText = stats.total_mahasiswa;
            document.getElementById('stat-fakultas').innerText = stats.total_fakultas;
            document.getElementById('stat-prodi').innerText = stats.total_prodi;
            document.getElementById('stat-wilayah').innerText = stats.total_wilayah;
            document.getElementById('stat-linearitas').innerText = stats.linearitas_percentage + '%';
        }

        function updateMapMarkers(locations) {
            markersCluster.clearLayers();
            document.getElementById('map-counter-info').innerText = `Menampilkan ${locations.length} Titik Lokasi`;

            if (locations.length === 0) return;

            const bounds = [];

            locations.forEach(loc => {
                if (loc.lat && loc.lng) {
                    const marker = L.marker([loc.lat, loc.lng], { icon: customIcon });

                    let badgeColor = 'bg-emerald-500/20 text-emerald-300';
                    if (loc.keselarasan_prodi === 'kurang_selaras') badgeColor = 'bg-amber-500/20 text-amber-300';
                    if (loc.keselarasan_prodi === 'tidak_selaras') badgeColor = 'bg-rose-500/20 text-rose-300';

                    const popupContent = `
                        <div class="space-y-2 font-sans text-xs">
                            <div class="flex items-center justify-between border-b border-slate-700 pb-1.5">
                                <span class="font-bold text-brand-400">${loc.nama}</span>
                                <span class="bg-slate-800 text-slate-300 px-2 py-0.5 rounded text-[10px] font-medium">${loc.nim}</span>
                            </div>
                            <div class="text-slate-300 space-y-1">
                                <p><i class="fa-solid fa-graduation-cap text-slate-400 mr-1"></i> ${loc.prodi} (${loc.fakultas})</p>
                                <p><i class="fa-solid fa-briefcase text-emerald-400 mr-1"></i> <strong>${loc.profesi}</strong> (${loc.kategori_profesi})</p>
                                <p><i class="fa-solid fa-building text-cyan-400 mr-1"></i> ${loc.perusahaan}</p>
                                <p><i class="fa-solid fa-location-dot text-rose-400 mr-1"></i> ${loc.kabupaten || loc.provinsi || 'Indonesia'}</p>
                                <div class="pt-1">
                                    <span class="${badgeColor} px-2 py-0.5 rounded text-[10px] font-semibold capitalize">
                                        Keselarasan: ${loc.keselarasan_prodi.replace('_', ' ')}
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;

                    marker.bindPopup(popupContent, { className: 'custom-popup' });
                    markersCluster.addLayer(marker);
                    bounds.push([loc.lat, loc.lng]);
                }
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 12 });
            }
        }

        function updateApexCharts(profesiDist, keselarasanBreakdown, linearitasRate) {
            // 1. Bar Chart Top 10 Profesi
            const profesiLabels = Object.keys(profesiDist);
            const profesiValues = Object.values(profesiDist);

            const optionsProfesi = {
                series: [{
                    name: 'Jumlah Alumni',
                    data: profesiValues
                }],
                chart: {
                    type: 'bar',
                    height: 280,
                    toolbar: { show: false },
                    foreColor: '#94a3b8',
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        horizontal: true,
                        barHeight: '55%',
                    }
                },
                colors: ['#6366f1'],
                dataLabels: { enabled: true, style: { fontSize: '10px', colors: ['#fff'] } },
                xaxis: { categories: profesiLabels },
                grid: { borderColor: '#334155', strokeDashArray: 3 }
            };

            if (chartProfesiInstance) chartProfesiInstance.destroy();
            chartProfesiInstance = new ApexCharts(document.querySelector("#chart-profesi"), optionsProfesi);
            chartProfesiInstance.render();

            // 2. Donut / Radial Gauge Linearitas Karir
            const optionsLinearitas = {
                series: [linearitasRate],
                chart: {
                    type: 'radialBar',
                    height: 280,
                    sparkline: { enabled: true }
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        track: {
                            background: '#1e293b',
                            strokeWidth: '97%',
                        },
                        dataLabels: {
                            name: {
                                show: true,
                                fontSize: '13px',
                                color: '#94a3b8',
                                offsetY: -20
                            },
                            value: {
                                offsetY: -10,
                                fontSize: '24px',
                                fontWeight: 'bold',
                                color: '#f59e0b',
                                formatter: function (val) {
                                    return val + "%";
                                }
                            }
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'horizontal',
                        gradientToColors: ['#10b981'],
                        stops: [0, 100]
                    }
                },
                labels: ['Keselarasan Karir'],
            };

            if (chartLinearitasInstance) chartLinearitasInstance.destroy();
            chartLinearitasInstance = new ApexCharts(document.querySelector("#chart-linearitas"), optionsLinearitas);
            chartLinearitasInstance.render();
        }

        function updateTable(locations) {
            const tbody = document.getElementById('table-body');
            tbody.innerHTML = '';

            if (locations.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-slate-500">Tidak ada data alumni/mahasiswa ditemukan.</td></tr>`;
                return;
            }

            locations.forEach(loc => {
                let badgeClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                let badgeText = '✅ Selaras';
                if (loc.keselarasan_prodi === 'kurang_selaras') {
                    badgeClass = 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                    badgeText = '⚠️ Kurang';
                } else if (loc.keselarasan_prodi === 'tidak_selaras') {
                    badgeClass = 'bg-rose-500/10 text-rose-400 border-rose-500/20';
                    badgeText = '❌ Tidak Selaras';
                }

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-800/40 transition-colors';
                tr.innerHTML = `
                    <td class="px-5 py-3.5">
                        <div class="font-semibold text-slate-100">${loc.nama}</div>
                        <div class="text-[11px] text-slate-400">${loc.nim} (Angkatan ${loc.angkatan})</div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="text-slate-200">${loc.prodi}</div>
                        <div class="text-[11px] text-slate-400">${loc.fakultas}</div>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="px-2 py-1 rounded bg-slate-800 text-slate-300 text-[11px] font-medium border border-slate-700">
                            ${loc.kategori_profesi}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="font-medium text-emerald-400">${loc.profesi}</div>
                        <div class="text-[11px] text-slate-400">${loc.perusahaan}</div>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-medium border ${badgeClass}">
                            ${badgeText}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <button onclick="zoomToMarker(${loc.lat}, ${loc.lng}, '${loc.nama}')" class="px-2.5 py-1 rounded-lg bg-brand-500/20 text-brand-300 hover:bg-brand-500 hover:text-white transition-all text-xs flex items-center gap-1 mx-auto">
                            <i class="fa-solid fa-crosshairs"></i> Fokus Peta
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function zoomToMarker(lat, lng, name) {
            map.setView([lat, lng], 14, { animate: true });
            window.scrollTo({ top: document.getElementById('map').offsetTop - 100, behavior: 'smooth' });
        }

        // Search in Table
        document.getElementById('search-table').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#table-body tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });

        // Event Listeners for Filters
        ['filter-fakultas', 'filter-prodi', 'filter-kategori-profesi', 'filter-keselarasan', 'filter-angkatan', 'filter-provinsi'].forEach(id => {
            document.getElementById(id).addEventListener('change', fetchMapData);
        });

        document.getElementById('btn-reset').addEventListener('click', function() {
            document.getElementById('filter-fakultas').value = '';
            document.getElementById('filter-prodi').value = '';
            document.getElementById('filter-kategori-profesi').value = '';
            document.getElementById('filter-keselarasan').value = '';
            document.getElementById('filter-angkatan').value = '';
            document.getElementById('filter-provinsi').value = '';
            fetchMapData();
        });

        // Initial Load
        document.addEventListener('DOMContentLoaded', fetchMapData);
    </script>
</body>
</html>
