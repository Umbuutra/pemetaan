<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatProfesi extends Model
{
    use HasFactory;

    protected $table = 'riwayat_profesi';

    protected $fillable = [
        'mahasiswa_id',
        'kategori_profesi_id',
        'perusahaan_id',
        'posisi_jabatan',
        'jenis_pekerjaan',
        'keselarasan_prodi',
        'pendapatan_bulanan',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_current',
        'deskripsi_pekerjaan',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function kategoriProfesi(): BelongsTo
    {
        return $this->belongsTo(KategoriProfesi::class, 'kategori_profesi_id');
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
    }
}
