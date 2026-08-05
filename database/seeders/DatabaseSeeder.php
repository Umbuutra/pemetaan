<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FakultasSeeder::class,
            ProgramStudiSeeder::class,
            WilayahSeeder::class,
            KategoriProfesiSeeder::class,
            PerusahaanSeeder::class,
        ]);

        // 1. Super Admin Account
        DB::table('users')->insert([
            'name' => 'Administrator Pusim',
            'email' => 'admin@pusim.ac.id',
            'role' => 'superadmin',
            'password' => Hash::make('12345678'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Admin Fakultas Account
        DB::table('users')->insert([
            'name' => 'Admin Fakultas Ilmu Komputer',
            'email' => 'filkom@pusim.ac.id',
            'role' => 'admin_fakultas',
            'password' => Hash::make('12345678'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Mahasiswa 1: Budi Santoso (DKI Jakarta -> Kebayoran Baru -> Senayan)
        $userMhs1 = DB::table('users')->insertGetId([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@mhs.ac.id',
            'role' => 'mahasiswa',
            'password' => Hash::make('12345678'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mhs1Id = DB::table('mahasiswa')->insertGetId([
            'user_id' => $userMhs1,
            'program_studi_id' => 1, // Teknik Informatika
            'kelurahan_id' => 1, // Senayan, Kebayoran Baru, Jakarta Selatan
            'nim' => '20220801001',
            'nama_lengkap' => 'Budi Santoso',
            'angkatan' => 2022,
            'status_kuliah' => 'lulus',
            'alamat_detail' => 'Jl. Jend. Sudirman No. 45, RT 01 / RW 03, Senayan',
            'latitude' => -6.2268,
            'longitude' => 106.8048,
            'no_hp' => '081234567890',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('riwayat_profesi')->insert([
            'mahasiswa_id' => $mhs1Id,
            'kategori_profesi_id' => 1, // Software Engineering
            'perusahaan_id' => 1, // PT Tokopedia
            'posisi_jabatan' => 'Junior Fullstack Web Engineer',
            'jenis_pekerjaan' => 'full_time',
            'keselarasan_prodi' => 'selaras',
            'pendapatan_bulanan' => 9500000.00,
            'tanggal_mulai' => '2026-01-15',
            'is_current' => true,
            'deskripsi_pekerjaan' => 'Mengembangkan RESTful API dengan Laravel dan Vue.js di tim E-Commerce Platform.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Mahasiswa 2: Siti Rahma (Jawa Barat -> Kota Bandung -> Coblong -> Dago)
        $userMhs2 = DB::table('users')->insertGetId([
            'name' => 'Siti Rahma',
            'email' => 'siti.rahma@mhs.ac.id',
            'role' => 'mahasiswa',
            'password' => Hash::make('12345678'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mhs2Id = DB::table('mahasiswa')->insertGetId([
            'user_id' => $userMhs2,
            'program_studi_id' => 2, // Sistem Informasi
            'kelurahan_id' => 2, // Dago, Coblong, Kota Bandung
            'nim' => '20220802005',
            'nama_lengkap' => 'Siti Rahma',
            'angkatan' => 2022,
            'status_kuliah' => 'lulus',
            'alamat_detail' => 'Jl. Ir. H. Juanda No. 102, Dago, Coblong',
            'latitude' => -6.8778,
            'longitude' => 107.6186,
            'no_hp' => '081987654321',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('riwayat_profesi')->insert([
            'mahasiswa_id' => $mhs2Id,
            'kategori_profesi_id' => 2, // Data Science
            'perusahaan_id' => 2, // BCA
            'posisi_jabatan' => 'Data Analyst Specialist',
            'jenis_pekerjaan' => 'full_time',
            'keselarasan_prodi' => 'selaras',
            'pendapatan_bulanan' => 11000000.00,
            'tanggal_mulai' => '2026-02-01',
            'is_current' => true,
            'deskripsi_pekerjaan' => 'Mengelola Dashboard analytics transaksi perbankan digital.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
