<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_profesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('kategori_profesi_id')->constrained('kategori_profesi')->cascadeOnDelete();
            $table->foreignId('perusahaan_id')->nullable()->constrained('perusahaan')->nullOnDelete();
            $table->string('posisi_jabatan');
            $table->enum('jenis_pekerjaan', ['full_time', 'part_time', 'freelance', 'wirausaha', 'internship'])->default('full_time');
            $table->enum('keselarasan_prodi', ['selaras', 'kurang_selaras', 'tidak_selaras'])->default('selaras');
            $table->decimal('pendapatan_bulanan', 12, 2)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('is_current')->default(true);
            $table->text('deskripsi_pekerjaan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_profesi');
    }
};
