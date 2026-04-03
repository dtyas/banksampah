<?php

use App\Http\Controllers\NasabahController;
use App\Http\Controllers\KategoriSampahController;
use App\Http\Controllers\SampahController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::apiResource('nasabah', NasabahController::class);
Route::apiResource('kategori-sampah', KategoriSampahController::class);
Route::apiResource('sampah', SampahController::class);
Route::apiResource('pembayaran', PembayaranController::class);
Route::apiResource('transaksi', TransaksiController::class);




