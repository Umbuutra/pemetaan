<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Provinsi
        $prov1 = DB::table('provinsi')->insertGetId([
            'kode_provinsi' => '31',
            'nama_provinsi' => 'DKI Jakarta',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $prov2 = DB::table('provinsi')->insertGetId([
            'kode_provinsi' => '32',
            'nama_provinsi' => 'Jawa Barat',
            'latitude' => -6.9175,
            'longitude' => 107.6191,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Kabupaten
        $kab1 = DB::table('kabupaten')->insertGetId([
            'provinsi_id' => $prov1,
            'kode_kabupaten' => '3174',
            'nama_kabupaten' => 'Kota Jakarta Selatan',
            'jenis' => 'kota',
            'latitude' => -6.2615,
            'longitude' => 106.8106,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $kab2 = DB::table('kabupaten')->insertGetId([
            'provinsi_id' => $prov2,
            'kode_kabupaten' => '3273',
            'nama_kabupaten' => 'Kota Bandung',
            'jenis' => 'kota',
            'latitude' => -6.9175,
            'longitude' => 107.6191,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Kecamatan
        $kec1 = DB::table('kecamatan')->insertGetId([
            'kabupaten_id' => $kab1,
            'kode_kecamatan' => '317401',
            'nama_kecamatan' => 'Kebayoran Baru',
            'latitude' => -6.2442,
            'longitude' => 106.7972,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $kec2 = DB::table('kecamatan')->insertGetId([
            'kabupaten_id' => $kab2,
            'kode_kecamatan' => '327301',
            'nama_kecamatan' => 'Coblong',
            'latitude' => -6.8872,
            'longitude' => 107.6152,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Kelurahan
        DB::table('kelurahan')->insert([
            [
                'id' => 1,
                'kecamatan_id' => $kec1,
                'kode_kelurahan' => '317401001',
                'nama_kelurahan' => 'Senayan',
                'kode_pos' => '12190',
                'latitude' => -6.2268,
                'longitude' => 106.8048,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'kecamatan_id' => $kec2,
                'kode_kelurahan' => '327301002',
                'nama_kelurahan' => 'Dago',
                'kode_pos' => '40135',
                'latitude' => -6.8778,
                'longitude' => 107.6186,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
