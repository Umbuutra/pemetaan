<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil & Data Profesi - Pusim Map System</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Leaflet CSS for Map Picker -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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
        .glass-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        #map-picker {
            height: 260px;
            border-radius: 0.75rem;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-dark-bg text-slate-100 font-sans antialiased min-h-screen pb-12 selection:bg-brand-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <nav class="glass-card sticky top-0 z-50 px-4 lg:px-8 py-3.5 flex items-center justify-between border-b border-slate-800 mb-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-cyan-400 text-white flex items-center justify-center shadow-lg shadow-brand-500/20">
                <i class="fa-solid fa-map-location-dot text-lg"></i>
            </div>
            <div>
                <h1 class="text-base font-bold text-white tracking-tight">Pusim Map System</h1>
                <p class="text-[11px] text-slate-400">Portal Form Profil Mandiri Mahasiswa & Alumni</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pemetaan.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold flex items-center gap-2 border border-slate-700 transition-colors">
                <i class="fa-solid fa-map"></i>
                <span class="hidden sm:inline">Peta Sebaran</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 text-xs font-semibold flex items-center gap-2 transition-colors">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="max-w-4xl mx-auto px-4">
        
        <!-- Header Banner -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">
                    Profil Mandiri & Tracer Study
                </h2>
                <p class="text-xs text-slate-400 mt-1">Perbarui data diri, status karir, lokasi, dan riwayat pekerjaan Anda</p>
            </div>
            <div class="px-3 py-1.5 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-400 text-xs font-medium flex items-center gap-2">
                <i class="fa-solid fa-user-shield"></i>
                <span class="capitalize">Role: {{ auth()->user()->role }}</span>
            </div>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs space-y-1">
                <div class="flex items-center gap-2 font-bold text-rose-300">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>Terdapat Kesalahan Input:</span>
                </div>
                <ul class="list-disc list-inside pl-2 space-y-0.5 text-slate-300 text-[11px]">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profil.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Card 1: Informasi Akademik & Diri -->
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <div class="flex items-center gap-3 mb-5 pb-3 border-b border-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-brand-500/10 text-brand-400 flex items-center justify-center">
                        <i class="fa-solid fa-id-card text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-200">1. Informasi Akademik & Diri</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $mahasiswa->nama_lengkap) }}" required
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">NIM (Nomor Induk Mahasiswa)</label>
                        <input type="text" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" required
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Program Studi</label>
                        <select name="program_studi_id" required
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-brand-500">
                            @foreach($programStudi as $prodi)
                                <option value="{{ $prodi->id }}" {{ old('program_studi_id', $mahasiswa->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama_prodi }} ({{ $prodi->kode_prodi }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Angkatan</label>
                        <input type="number" name="angkatan" min="2000" max="{{ date('Y') }}" value="{{ old('angkatan', $mahasiswa->angkatan) }}" required
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Status Akademik</label>
                        <select name="status_kuliah" required
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-brand-500">
                            <option value="lulus" {{ old('status_kuliah', $mahasiswa->status_kuliah) == 'lulus' ? 'selected' : '' }}>Lulus / Alumni</option>
                            <option value="aktif" {{ old('status_kuliah', $mahasiswa->status_kuliah) == 'aktif' ? 'selected' : '' }}>Mahasiswa Aktif</option>
                            <option value="cuti" {{ old('status_kuliah', $mahasiswa->status_kuliah) == 'cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="do" {{ old('status_kuliah', $mahasiswa->status_kuliah) == 'do' ? 'selected' : '' }}>Drop Out</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nomor Handphone / WhatsApp</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $mahasiswa->no_hp) }}" placeholder="081234567890"
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    </div>
                </div>
            </div>

            <!-- Card 2: Status Pekerjaan & Profesi (Tracer Study) -->
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <div class="flex items-center gap-3 mb-5 pb-3 border-b border-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <i class="fa-solid fa-briefcase text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-200">2. Status Karir & Profesi Pekerjaan</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kategori Bidang Profesi</label>
                        <select name="kategori_profesi_id"
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-brand-500">
                            <option value="">-- Pilih Kategori Profesi --</option>
                            @foreach($kategoriProfesi as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_profesi_id', $riwayatAktif?->kategori_profesi_id) == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Posisi / Jabatan Pekerjaan</label>
                        <input type="text" name="posisi_jabatan" value="{{ old('posisi_jabatan', $riwayatAktif?->posisi_jabatan) }}" placeholder="Contoh: Senior Fullstack Engineer"
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Perusahaan Tempat Bekerja</label>
                        <select name="perusahaan_id" id="perusahaan_id"
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-brand-500">
                            <option value="">-- Pilih dari Daftar Perusahaan --</option>
                            @foreach($perusahaan as $p)
                                <option value="{{ $p->id }}" {{ old('perusahaan_id', $riwayatAktif?->perusahaan_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_perusahaan }} ({{ $p->kota ?? 'Indonesia' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Atau Nama Perusahaan Baru (Jika Tidak Ada di Daftar)</label>
                        <input type="text" name="nama_perusahaan_baru" value="{{ old('nama_perusahaan_baru') }}" placeholder="Ketik nama perusahaan..."
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Jenis Pekerjaan</label>
                        <select name="jenis_pekerjaan"
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-brand-500">
                            <option value="full_time" {{ old('jenis_pekerjaan', $riwayatAktif?->jenis_pekerjaan) == 'full_time' ? 'selected' : '' }}>Full Time / Penuh Waktu</option>
                            <option value="part_time" {{ old('jenis_pekerjaan', $riwayatAktif?->jenis_pekerjaan) == 'part_time' ? 'selected' : '' }}>Part Time / Paruh Waktu</option>
                            <option value="freelance" {{ old('jenis_pekerjaan', $riwayatAktif?->jenis_pekerjaan) == 'freelance' ? 'selected' : '' }}>Freelance / Pekerja Lepas</option>
                            <option value="wirausaha" {{ old('jenis_pekerjaan', $riwayatAktif?->jenis_pekerjaan) == 'wirausaha' ? 'selected' : '' }}>Wirausaha / Founder</option>
                            <option value="internship" {{ old('jenis_pekerjaan', $riwayatAktif?->jenis_pekerjaan) == 'internship' ? 'selected' : '' }}>Internship / Magang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Keselarasan Karir dengan Program Studi</label>
                        <select name="keselarasan_prodi"
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-brand-500">
                            <option value="selaras" {{ old('keselarasan_prodi', $riwayatAktif?->keselarasan_prodi) == 'selaras' ? 'selected' : '' }}>✅ Selaras / Sesuai Jurusan</option>
                            <option value="kurang_selaras" {{ old('keselarasan_prodi', $riwayatAktif?->keselarasan_prodi) == 'kurang_selaras' ? 'selected' : '' }}>⚠️ Kurang Selaras</option>
                            <option value="tidak_selaras" {{ old('keselarasan_prodi', $riwayatAktif?->keselarasan_prodi) == 'tidak_selaras' ? 'selected' : '' }}>❌ Tidak Selaras / Lintas Bidang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Estimasi Pendapatan Bulanan (Rp)</label>
                        <input type="number" name="pendapatan_bulanan" step="100000" value="{{ old('pendapatan_bulanan', $riwayatAktif?->pendapatan_bulanan) }}" placeholder="Contoh: 8000000"
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tanggal Mulai Bekerja</label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $riwayatAktif?->tanggal_mulai) }}"
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-brand-500">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Deskripsi Singkat Pekerjaan & Tanggung Jawab</label>
                    <textarea name="deskripsi_pekerjaan" rows="2" placeholder="Menjelaskan tugas utama atau teknologi yang digunakan..."
                        class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl p-3 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-brand-500">{{ old('deskripsi_pekerjaan', $riwayatAktif?->deskripsi_pekerjaan) }}</textarea>
                </div>
            </div>

            <!-- Card 3: Lokasi Peta GIS (Pinpoint Koordinat) -->
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                        <i class="fa-solid fa-location-dot text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-200">3. Titik Lokasi Peta GIS (Leaflet Map Picker)</h3>
                        <p class="text-[11px] text-slate-400">Klik di peta untuk menentukan koordinat tempat bekerja / domisili Anda</p>
                    </div>
                </div>

                <div class="mb-4">
                    <div id="map-picker"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Latitude</label>
                        <input type="text" name="latitude" id="latitude" readonly value="{{ old('latitude', $mahasiswa->latitude ?? -6.2088) }}"
                            class="w-full bg-slate-900/50 border border-slate-800 rounded-xl px-3 py-2 text-xs text-cyan-400 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Longitude</label>
                        <input type="text" name="longitude" id="longitude" readonly value="{{ old('longitude', $mahasiswa->longitude ?? 106.8456) }}"
                            class="w-full bg-slate-900/50 border border-slate-800 rounded-xl px-3 py-2 text-xs text-cyan-400 font-mono">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Alamat Detail Domisili / Perusahaan</label>
                    <textarea name="alamat_detail" rows="2" placeholder="Nama jalan, gedung, RT/RW, Kota..."
                        class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl p-3 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-brand-500">{{ old('alamat_detail', $mahasiswa->alamat_detail) }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('pemetaan.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-semibold text-xs shadow-lg shadow-brand-500/25 flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>
        </form>

    </div>

    <!-- Leaflet JS Map Picker Script -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const defaultLat = parseFloat(document.getElementById('latitude').value) || -6.2088;
            const defaultLng = parseFloat(document.getElementById('longitude').value) || 106.8456;

            const map = L.map('map-picker').setView([defaultLat, defaultLng], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            function updateInputs(lat, lng) {
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);
            }

            map.on('click', function (e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                marker.setLatLng([lat, lng]);
                updateInputs(lat, lng);
            });

            marker.on('dragend', function (e) {
                const position = marker.getLatLng();
                updateInputs(position.lat, position.lng);
            });
        });
    </script>
</body>
</html>
