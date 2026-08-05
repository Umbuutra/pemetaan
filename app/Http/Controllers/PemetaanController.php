<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
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
        $angkatanList = Mahasiswa::select('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan');

        return view('pemetaan.index', compact('fakultas', 'programStudi', 'provinsi', 'angkatanList'));
    }

    public function getMapData(Request $request): JsonResponse
    {
        $query = Mahasiswa::with([
            'programStudi.fakultas',
            'kelurahan.kecamatan.kabupaten.provinsi',
            'riwayatProfesiAktif.kategoriProfesi',
            'riwayatProfesiAktif.perusahaan',
        ]);

        // Filters
        if ($request->filled('fakultas_id')) {
            $query->whereHas('programStudi', function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id);
            });
        }

        if ($request->filled('program_studi_id')) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        if ($request->filled('provinsi_id')) {
            $query->whereHas('kelurahan.kecamatan.kabupaten', function ($q) use ($request) {
                $q->where('provinsi_id', $request->provinsi_id);
            });
        }

        $mahasiswaList = $query->get();

        // Transform for Leaflet map & UI table
        $locations = $mahasiswaList->map(function ($m) {
            $kelurahan = $m->kelurahan;
            $kecamatan = $kelurahan?->kecamatan;
            $kabupaten = $kecamatan?->kabupaten;
            $provinsi = $kabupaten?->provinsi;
            $profesi = $m->riwayatProfesiAktif;

            // Coordinates fallback (mahasiswa -> kelurahan -> kecamatan -> kabupaten -> provinsi)
            $lat = $m->latitude ?? $kelurahan?->latitude ?? $kecamatan?->latitude ?? $kabupaten?->latitude ?? $provinsi?->latitude ?? -6.2088;
            $lng = $m->longitude ?? $kelurahan?->longitude ?? $kecamatan?->longitude ?? $kabupaten?->longitude ?? $provinsi?->longitude ?? 106.8456;

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
                'profesi' => $profesi?->posisi_jabatan ?? 'Belum Bekerja',
                'perusahaan' => $profesi?->perusahaan?->nama_perusahaan ?? '-',
                'lat' => (float) $lat,
                'lng' => (float) $lng,
            ];
        });

        // Statistics
        $totalMahasiswa = $locations->count();
        $totalFakultas = Fakultas::count();
        $totalProdi = ProgramStudi::count();
        $totalWilayah = $locations->pluck('provinsi')->filter()->unique()->count();

        // Distribution by Fakultas
        $fakultasDist = $locations->groupBy('fakultas')->map(fn ($group) => $group->count());

        // Distribution by Kabupaten (Top 5)
        $kabupatenDist = $locations->groupBy('kabupaten')->map(fn ($group) => $group->count())->sortDesc()->take(5);

        return response()->json([
            'locations' => $locations,
            'stats' => [
                'total_mahasiswa' => $totalMahasiswa,
                'total_fakultas' => $totalFakultas,
                'total_prodi' => $totalProdi,
                'total_wilayah' => $totalWilayah,
            ],
            'fakultas_distribution' => $fakultasDist,
            'kabupaten_distribution' => $kabupatenDist,
        ]);
    }
}
