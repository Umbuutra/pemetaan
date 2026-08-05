<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PemetaanController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Main Pemetaan Dashboard View
Route::get('/', [PemetaanController::class, 'index'])->name('pemetaan.index');

// Protected Routes (Perlu Login)
Route::middleware(['auth'])->group(function () {
    // Form Profil Mandiri & Tracer Study (Mahasiswa / Alumni)
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::post('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
});

// API Endpoints for Dashboard & Map Data
Route::get('/api/pemetaan/data', [PemetaanController::class, 'getMapData'])->name('api.pemetaan.data');

// API Endpoints for Dependent Dropdown Wilayah
Route::prefix('api/wilayah')->group(function () {
    Route::get('/provinsi', [WilayahController::class, 'getProvinsi']);
    Route::get('/kabupaten/{provinsi_id}', [WilayahController::class, 'getKabupaten']);
    Route::get('/kecamatan/{kabupaten_id}', [WilayahController::class, 'getKecamatan']);
    Route::get('/kelurahan/{kecamatan_id}', [WilayahController::class, 'getKelurahan']);
});
