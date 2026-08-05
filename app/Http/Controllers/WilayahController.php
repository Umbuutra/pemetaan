<?php

namespace App\Http\Controllers;

use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Provinsi;
use Illuminate\Http\JsonResponse;

class WilayahController extends Controller
{
    public function getProvinsi(): JsonResponse
    {
        $provinsi = Provinsi::orderBy('nama_provinsi')->get();
        return response()->json($provinsi);
    }

    public function getKabupaten(int $provinsi_id): JsonResponse
    {
        $kabupaten = Kabupaten::where('provinsi_id', $provinsi_id)->orderBy('nama_kabupaten')->get();
        return response()->json($kabupaten);
    }

    public function getKecamatan(int $kabupaten_id): JsonResponse
    {
        $kecamatan = Kecamatan::where('kabupaten_id', $kabupaten_id)->orderBy('nama_kecamatan')->get();
        return response()->json($kecamatan);
    }

    public function getKelurahan(int $kecamatan_id): JsonResponse
    {
        $kelurahan = Kelurahan::where('kecamatan_id', $kecamatan_id)->orderBy('nama_kelurahan')->get();
        return response()->json($kelurahan);
    }
}
