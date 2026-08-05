<?php

namespace App\Http\Controllers;

use App\Models\KategoriProfesi;
use App\Models\Mahasiswa;
use App\Models\Perusahaan;
use App\Models\ProgramStudi;
use App\Models\Provinsi;
use App\Models\RiwayatProfesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfilController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        // Cari atau buat profil mahasiswa untuk user yang sedang login
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->with(['riwayatProfesi', 'programStudi', 'kelurahan.kecamatan.kabupaten.provinsi'])->first();

        if (!$mahasiswa) {
            // Jika mahasiswa belum ada, buat record default dasar
            $mahasiswa = Mahasiswa::create([
                'user_id' => $user->id,
                'program_studi_id' => ProgramStudi::first()?->id ?? 1,
                'nim' => 'MHS' . time(),
                'nama_lengkap' => $user->name,
                'angkatan' => date('Y'),
                'status_kuliah' => 'lulus',
            ]);
        }

        $riwayatAktif = $mahasiswa->riwayatProfesiAktif;
        $kategoriProfesi = KategoriProfesi::orderBy('nama_kategori')->get();
        $perusahaan = Perusahaan::orderBy('nama_perusahaan')->get();
        $programStudi = ProgramStudi::orderBy('nama_prodi')->get();
        $provinsi = Provinsi::orderBy('nama_provinsi')->get();

        return view('profil.edit', compact(
            'mahasiswa',
            'riwayatAktif',
            'kategoriProfesi',
            'perusahaan',
            'programStudi',
            'provinsi'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nim' => 'required|string|max:30|unique:mahasiswa,nim,' . $mahasiswa->id,
            'program_studi_id' => 'required|exists:program_studi,id',
            'angkatan' => 'required|integer|min:2000|max:' . date('Y'),
            'status_kuliah' => 'required|in:aktif,lulus,cuti,do',
            'no_hp' => 'nullable|string|max:20',
            'alamat_detail' => 'nullable|string',
            'kelurahan_id' => 'nullable|exists:kelurahan,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',

            // Data Karir / Profesi
            'kategori_profesi_id' => 'nullable|exists:kategori_profesi,id',
            'perusahaan_id' => 'nullable|exists:perusahaan,id',
            'nama_perusahaan_baru' => 'nullable|string|max:255',
            'posisi_jabatan' => 'nullable|string|max:255',
            'jenis_pekerjaan' => 'nullable|in:full_time,part_time,freelance,wirausaha,internship',
            'keselarasan_prodi' => 'nullable|in:selaras,kurang_selaras,tidak_selaras',
            'pendapatan_bulanan' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'nullable|date',
            'deskripsi_pekerjaan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update User Name
            $user->update(['name' => $validated['nama_lengkap']]);

            // Update Mahasiswa
            $mahasiswa->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'nim' => $validated['nim'],
                'program_studi_id' => $validated['program_studi_id'],
                'angkatan' => $validated['angkatan'],
                'status_kuliah' => $validated['status_kuliah'],
                'no_hp' => $validated['no_hp'],
                'alamat_detail' => $validated['alamat_detail'],
                'kelurahan_id' => $validated['kelurahan_id'] ?? $mahasiswa->kelurahan_id,
                'latitude' => $validated['latitude'] ?? $mahasiswa->latitude,
                'longitude' => $validated['longitude'] ?? $mahasiswa->longitude,
            ]);

            // Tangani Perusahaan Baru jika diinputkan
            $perusahaanId = $validated['perusahaan_id'] ?? null;
            if (!empty($validated['nama_perusahaan_baru']) && !$perusahaanId) {
                $perusahaanBaru = Perusahaan::create([
                    'nama_perusahaan' => $validated['nama_perusahaan_baru'],
                    'bidang_industri' => 'Umum / Lainnya',
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                ]);
                $perusahaanId = $perusahaanBaru->id;
            }

            // Update atau Create Riwayat Profesi Aktif
            if (!empty($validated['kategori_profesi_id']) || !empty($validated['posisi_jabatan'])) {
                RiwayatProfesi::updateOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswa->id,
                        'is_current' => true,
                    ],
                    [
                        'kategori_profesi_id' => $validated['kategori_profesi_id'] ?? 1,
                        'perusahaan_id' => $perusahaanId,
                        'posisi_jabatan' => $validated['posisi_jabatan'] ?? 'Belum Diisi',
                        'jenis_pekerjaan' => $validated['jenis_pekerjaan'] ?? 'full_time',
                        'keselarasan_prodi' => $validated['keselarasan_prodi'] ?? 'selaras',
                        'pendapatan_bulanan' => $validated['pendapatan_bulanan'],
                        'tanggal_mulai' => $validated['tanggal_mulai'],
                        'deskripsi_pekerjaan' => $validated['deskripsi_pekerjaan'],
                    ]
                );
            }

            DB::commit();
            return redirect()->route('profil.edit')->with('success', 'Profil dan Data Profesi berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }
}
