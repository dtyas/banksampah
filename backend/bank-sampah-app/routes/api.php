<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\KategoriSampahController;
use App\Http\Controllers\Api\V1\LaporanController;
use App\Http\Controllers\Api\V1\NasabahController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PembayaranController;
use App\Http\Controllers\Api\V1\SampahController;
use App\Http\Controllers\Api\V1\TransaksiController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('orders/store', [OrderController::class, 'store']);

        Route::apiResource('nasabah', NasabahController::class);
        Route::apiResource('kategori-sampah', KategoriSampahController::class);
        Route::apiResource('sampah', SampahController::class);
        Route::apiResource('pembayaran', PembayaranController::class);
        Route::apiResource('transaksi', TransaksiController::class);
        Route::apiResource('users', UserController::class);

        Route::prefix('laporan')->group(function (): void {
            Route::get('summary', [LaporanController::class, 'summary']);
            Route::get('chart', [LaporanController::class, 'chart']);
            Route::get('transaksi', [LaporanController::class, 'transaksi']);
        });
    });
});
