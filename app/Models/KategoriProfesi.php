<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriProfesi extends Model
{
    use HasFactory;

    protected $table = 'kategori_profesi';

    protected $fillable = [
        'nama_kategori',
        'slug',
        'deskripsi',
    ];

    public function riwayatProfesi(): HasMany
    {
        return $this->hasMany(RiwayatProfesi::class, 'kategori_profesi_id');
    }
}
