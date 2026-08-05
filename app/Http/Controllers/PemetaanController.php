<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\KategoriProfesi;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\Provinsi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemetaanController extends Controller
{
    public function index()
    {
        $fakultas = Fakultas::with('programStudi')->orderBy('nama_fakultas')->get();
        $programStudi = ProgramStudi::orderBy('nama_prodi')->get();
        $provinsi = Provinsi::orderBy('nama_provinsi')->get();
        $kategoriProfesi = KategoriProfesi::orderBy('nama_kategori')->get();
        $angkatanList = Mahasiswa::select('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan');

        return view('pemetaan.index', compact('fakultas', 'programStudi', 'provinsi', 'kategoriProfesi', 'angkatanList'));
    }

    public function getMapData(Request $request): JsonResponse
    {
        $query = Mahasiswa::with([
            'programStudi.fakultas',
            'kelurahan.kecamatan.kabupaten.provinsi',
            'riwayatProfesiAktif.kategoriProfesi',
            'riwayatProfesiAktif.perusahaan',
        ]);

        // Filter Fakultas
        if ($request->filled('fakultas_id')) {
            $query->whereHas('programStudi', function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id);
            });
        }

        // Filter Program Studi
        if ($request->filled('program_studi_id')) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        // Filter Angkatan
        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        // Filter Provinsi
        if ($request->filled('provinsi_id')) {
            $query->whereHas('kelurahan.kecamatan.kabupaten', function ($q) use ($request) {
                $q->where('provinsi_id', $request->provinsi_id);
            });
        }

        // Filter Kategori Profesi
        if ($request->filled('kategori_profesi_id')) {
            $query->whereHas('riwayatProfesiAktif', function ($q) use ($request) {
                $q->where('kategori_profesi_id', $request->kategori_profesi_id);
            });
        }

        // Filter Keselarasan Prodi
        if ($request->filled('keselarasan_prodi')) {
            $query->whereHas('riwayatProfesiAktif', function ($q) use ($request) {
                $q->where('keselarasan_prodi', $request->keselarasan_prodi);
            });
        }

        $mahasiswaList = $query->get();

        // Transform data untuk Map & UI Table
        $locations = $mahasiswaList->map(function ($m) {
            $kelurahan = $m->kelurahan;
            $kecamatan = $kelurahan?->kecamatan;
            $kabupaten = $kecamatan?->kabupaten;
            $provinsi = $kabupaten?->provinsi;
            $profesi = $m->riwayatProfesiAktif;

            $lat = $m->latitude ?? $profesi?->perusahaan?->latitude ?? $kelurahan?->latitude ?? $kecamatan?->latitude ?? $kabupaten?->latitude ?? $provinsi?->latitude ?? -6.2088;
            $lng = $m->longitude ?? $profesi?->perusahaan?->longitude ?? $kelurahan?->longitude ?? $kecamatan?->longitude ?? $kabupaten?->longitude ?? $provinsi?->longitude ?? 106.8456;

            return [
                'id' => $m->id,
                'nim' => $m->nim,
                'nama' => $m->nama_lengkap,
                'prodi' => $m->programStudi?->nama_prodi,
                'fakultas' => $m->programStudi?->fakultas?->nama_fakultas,
                'angkatan' => $m->angkatan,
                'status_kuliah' => ucfirst($m->status_kuliah),
                'kelurahan' => $kelurahan?->nama_kelurahan,
                'kecamatan' => $kecamatan?->nama_kecamatan,
                'kabupaten' => $kabupaten?->nama_kabupaten,
                'provinsi' => $provinsi?->nama_provinsi,
                'alamat_detail' => $m->alamat_detail,
                'profesi' => $profesi?->posisi_jabatan ?? 'Belum Diisi',
                'kategori_profesi' => $profesi?->kategoriProfesi?->nama_kategori ?? 'Lainnya',
                'perusahaan' => $profesi?->perusahaan?->nama_perusahaan ?? '-',
                'jenis_pekerjaan' => $profesi?->jenis_pekerjaan ? ucfirst(str_replace('_', ' ', $profesi->jenis_pekerjaan)) : 'Belum Bekerja',
                'keselarasan_prodi' => $profesi?->keselarasan_prodi ?? 'belum_ditentukan',
                'pendapatan_bulanan' => $profesi?->pendapatan_bulanan ? 'Rp ' . number_format($profesi->pendapatan_bulanan, 0, ',', '.') : '-',
                'lat' => (float) $lat,
                'lng' => (float) $lng,
            ];
        });

        // Statistik Utama
        $totalMahasiswa = $locations->count();
        $totalFakultas = Fakultas::count();
        $totalProdi = ProgramStudi::count();
        $totalWilayah = $locations->pluck('provinsi')->filter()->unique()->count();

        // 1. Chart Statistik Kategori Profesi (Top 10)
        $profesiDist = $locations->groupBy('kategori_profesi')
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->take(10);

        // 2. Chart Statistik Jenis Pekerjaan
        $jenisPekerjaanDist = $locations->groupBy('jenis_pekerjaan')
            ->map(fn ($group) => $group->count());

        // 3. Rate Keselarasan Karir (Linearitas Prodi)
        $totalProfesiRecorded = $locations->where('keselarasan_prodi', '!=', 'belum_ditentukan')->count();
        $totalSelaras = $locations->where('keselarasan_prodi', 'selaras')->count();
        $linearitasPercentage = $totalProfesiRecorded > 0 ? round(($totalSelaras / $totalProfesiRecorded) * 100, 1) : 0;

        $keselarasanBreakdown = [
            'Selaras' => $totalSelaras,
            'Kurang Selaras' => $locations->where('keselarasan_prodi', 'kurang_selaras')->count(),
            'Tidak Selaras' => $locations->where('keselarasan_prodi', 'tidak_selaras')->count(),
        ];

        return response()->json([
            'locations' => $locations,
            'stats' => [
                'total_mahasiswa' => $totalMahasiswa,
                'total_fakultas' => $totalFakultas,
                'total_prodi' => $totalProdi,
                'total_wilayah' => $totalWilayah,
                'linearitas_percentage' => $linearitasPercentage,
            ],
            'profesi_distribution' => $profesiDist,
            'jenis_pekerjaan_distribution' => $jenisPekerjaanDist,
            'keselarasan_breakdown' => $keselarasanBreakdown,
        ]);
    }
}
