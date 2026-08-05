<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FakultasSeeder extends Seeder
{
    public function run(): void
    {
        $fakultas = [
            ['kode_fakultas' => 'FILKOM', 'nama_fakultas' => 'Fakultas Ilmu Komputer', 'created_at' => now(), 'updated_at' => now()],
            ['kode_fakultas' => 'FEB', 'nama_fakultas' => 'Fakultas Ekonomi & Bisnis', 'created_at' => now(), 'updated_at' => now()],
            ['kode_fakultas' => 'FT', 'nama_fakultas' => 'Fakultas Teknik', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('fakultas')->insert($fakultas);
    }
}
