<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perusahaan extends Model
{
    use HasFactory;

    protected $table = 'perusahaan';

    protected $fillable = [
        'nama_perusahaan',
        'bidang_industri',
        'alamat',
        'kota',
        'provinsi',
        'negara',
        'latitude',
        'longitude',
    ];

    public function riwayatProfesi(): HasMany
    {
        return $this->hasMany(RiwayatProfesi::class, 'perusahaan_id');
    }
}
