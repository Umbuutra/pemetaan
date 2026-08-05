<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'user_id',
        'program_studi_id',
        'kelurahan_id',
        'nim',
        'nama_lengkap',
        'angkatan',
        'status_kuliah',
        'alamat_detail',
        'latitude',
        'longitude',
        'no_hp',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id');
    }

    public function riwayatProfesi(): HasMany
    {
        return $this->hasMany(RiwayatProfesi::class, 'mahasiswa_id');
    }

    public function riwayatProfesiAktif()
    {
        return $this->hasOne(RiwayatProfesi::class, 'mahasiswa_id')->where('is_current', true);
    }
}
