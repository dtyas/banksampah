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
use App\Http\Controllers\Api\V1\XenditController;
use App\Http\Controllers\Api\V1\XenditWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('webhooks/xendit/payouts', [XenditWebhookController::class, 'handlePayoutWebhook']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('orders/store', [OrderController::class, 'store'])
            ->middleware(['permission:menu.transaksi', 'permission:operational.tambah_data']);
        Route::get('xendit/balance', [XenditController::class, 'balance'])
            ->middleware(['permission:menu.dashboard']);
        Route::get('nasabah/me/ledger', [NasabahController::class, 'ledger'])
            ->middleware(['permission:menu.pencairan_saldo']);
        Route::get('nasabah/me/transaksi', [NasabahController::class, 'transaksiMe'])
            ->middleware(['permission:menu.pencairan_saldo']);
        Route::put('nasabah/me/payout', [NasabahController::class, 'updatePayoutAccount'])
            ->middleware(['permission:menu.pencairan_saldo']);
        Route::post('pencairan-saldo/request', [PembayaranController::class, 'requestPencairanSaldo'])
            ->middleware(['permission:menu.pencairan_saldo', 'permission:operational.ajukan_pencairan_saldo']);
        Route::apiResource('nasabah', NasabahController::class)
            ->middlewareFor(['index', 'show'], 'permission:menu.nasabah')
            ->middlewareFor('store', ['permission:menu.nasabah', 'permission:operational.tambah_data'])
            ->middlewareFor('update', ['permission:menu.nasabah', 'permission:operational.ubah_data'])
            ->middlewareFor('destroy', ['permission:menu.nasabah', 'permission:operational.hapus_data']);
        Route::apiResource('kategori-sampah', KategoriSampahController::class)
            ->middlewareFor(['index', 'show'], 'permission:menu.kategori_sampah')
            ->middlewareFor('store', ['permission:menu.kategori_sampah', 'permission:operational.tambah_data'])
            ->middlewareFor('update', ['permission:menu.kategori_sampah', 'permission:operational.ubah_data'])
            ->middlewareFor('destroy', ['permission:menu.kategori_sampah', 'permission:operational.hapus_data']);
        Route::apiResource('sampah', SampahController::class)
            ->middlewareFor(['index', 'show'], 'permission:menu.sampah')
            ->middlewareFor('store', ['permission:menu.sampah', 'permission:operational.tambah_data'])
            ->middlewareFor('update', ['permission:menu.sampah', 'permission:operational.ubah_data'])
            ->middlewareFor('destroy', ['permission:menu.sampah', 'permission:operational.hapus_data']);
        Route::apiResource('pembayaran', PembayaranController::class)
            ->middlewareFor(['index', 'show'], 'permission:menu.pembayaran')
            ->middlewareFor('store', ['permission:menu.pembayaran', 'permission:operational.tambah_data'])
            ->middlewareFor('update', ['permission:menu.pembayaran', 'permission:operational.verifikasi_pembayaran'])
            ->middlewareFor('destroy', ['permission:menu.pembayaran', 'permission:operational.hapus_data']);
        Route::apiResource('transaksi', TransaksiController::class)
            ->middlewareFor(['index', 'show'], 'permission:menu.transaksi')
            ->middlewareFor('store', ['permission:menu.transaksi', 'permission:operational.tambah_data'])
            ->middlewareFor('update', ['permission:menu.transaksi', 'permission:operational.ubah_data'])
            ->middlewareFor('destroy', ['permission:menu.transaksi', 'permission:operational.hapus_data']);
        Route::apiResource('users', UserController::class)
            ->middlewareFor(['index', 'show'], 'permission:menu.user')
            ->middlewareFor('store', ['permission:menu.user', 'permission:operational.tambah_data'])
            ->middlewareFor('update', ['permission:menu.user', 'permission:operational.ubah_data'])
            ->middlewareFor('destroy', ['permission:menu.user', 'permission:operational.hapus_data']);

        Route::prefix('laporan')->group(function (): void {
            Route::get('summary', [LaporanController::class, 'summary'])->middleware('permission:menu.laporan');
            Route::get('chart', [LaporanController::class, 'chart'])->middleware('permission:menu.laporan');
            Route::get('transaksi', [LaporanController::class, 'transaksi'])->middleware('permission:menu.laporan');
        });
    });
});
