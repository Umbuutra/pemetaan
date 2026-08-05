<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerusahaanSeeder extends Seeder
{
    public function run(): void
    {
        $perusahaan = [
            [
                'nama_perusahaan' => 'PT Tokopedia (GoTo Group)',
                'bidang_industri' => 'E-Commerce & Tech',
                'alamat' => 'Tokopedia Tower, Jl. Prof. DR. Satrio No.11, Jakarta Selatan',
                'kota' => 'Jakarta Selatan',
                'provinsi' => 'DKI Jakarta',
                'negara' => 'Indonesia',
                'latitude' => -6.223838,
                'longitude' => 106.822839,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_perusahaan' => 'PT Bank Central Asia Tbk (BCA)',
                'bidang_industri' => 'Perbankan & Keuangan',
                'alamat' => 'Menara BCA, Grand Indonesia, Jl. M.H. Thamrin No.1, Jakarta Pusat',
                'kota' => 'Jakarta Pusat',
                'provinsi' => 'DKI Jakarta',
                'negara' => 'Indonesia',
                'latitude' => -6.195156,
                'longitude' => 106.820374,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_perusahaan' => 'PT Telkom Indonesia Tbk',
                'bidang_industri' => 'Telekomunikasi & IT',
                'alamat' => 'Telkom Landmark Tower, Jl. Jend. Gatot Subroto Kav. 52, Jakarta Selatan',
                'kota' => 'Jakarta Selatan',
                'provinsi' => 'DKI Jakarta',
                'negara' => 'Indonesia',
                'latitude' => -6.230193,
                'longitude' => 106.817652,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_perusahaan' => 'PT Paragon Technology and Innovation',
                'bidang_industri' => 'FMCG & Manufaktur',
                'alamat' => 'Kawasan Industri Jatake, Tangerang',
                'kota' => 'Tangerang',
                'provinsi' => 'Banten',
                'negara' => 'Indonesia',
                'latitude' => -6.198300,
                'longitude' => 106.565100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_perusahaan' => 'Shopee Singapore Private Limited',
                'bidang_industri' => 'Technology & E-Commerce',
                'alamat' => '5 Science Park Drive, Shopee Building, Singapore',
                'kota' => 'Singapore',
                'provinsi' => 'Central Region',
                'negara' => 'Singapura',
                'latitude' => 1.288921,
                'longitude' => 103.784561,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('perusahaan')->insert($perusahaan);
    }
}
