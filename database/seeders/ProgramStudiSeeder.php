<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramStudiSeeder extends Seeder
{
    public function run(): void
    {
        $prodi = [
            [
                'fakultas_id' => 1, // FILKOM
                'kode_prodi' => 'IF',
                'nama_prodi' => 'Teknik Informatika',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fakultas_id' => 1, // FILKOM
                'kode_prodi' => 'SI',
                'nama_prodi' => 'Sistem Informasi',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fakultas_id' => 2, // FEB
                'kode_prodi' => 'MJ',
                'nama_prodi' => 'Manajemen',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fakultas_id' => 2, // FEB
                'kode_prodi' => 'AK',
                'nama_prodi' => 'Akuntansi',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fakultas_id' => 3, // FT
                'kode_prodi' => 'TE',
                'nama_prodi' => 'Teknik Elektro',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('program_studi')->insert($prodi);
    }
}
