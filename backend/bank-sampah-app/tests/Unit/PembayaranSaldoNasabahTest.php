<?php

namespace Tests\Unit;

use App\Models\Nasabah;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\PembayaranService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembayaranSaldoNasabahTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_saldo_nasabah(): void
    {
        $user = User::factory()->create(['role' => 'nasabah']);
        $nasabah = Nasabah::factory()->create(['user_id' => $user->id]);

        $transaksiA = Transaksi::query()->create([
            'user_id' => $user->id,
            'nasabah_id' => $nasabah->id,
            'tanggal' => '2026-04-09',
            'total_berat' => 5,
            'total_harga' => 60000,
        ]);

        $transaksiB = Transaksi::query()->create([
            'user_id' => $user->id,
            'nasabah_id' => $nasabah->id,
            'tanggal' => '2026-04-10',
            'total_berat' => 3,
            'total_harga' => 40000,
        ]);

        $otherNasabah = Nasabah::factory()->create();
        $otherTransaksi = Transaksi::query()->create([
            'user_id' => $user->id,
            'nasabah_id' => $otherNasabah->id,
            'tanggal' => '2026-04-10',
            'total_berat' => 2,
            'total_harga' => 20000,
        ]);

        Pembayaran::query()->create([
            'transaksi_id' => $transaksiA->id,
            'jumlah' => 30000,
            'metode' => 'BCA',
            'status' => 'berhasil',
            'tanggal' => '2026-04-10',
        ]);

        Pembayaran::query()->create([
            'transaksi_id' => $transaksiB->id,
            'jumlah' => 10000,
            'metode' => 'BCA',
            'status' => 'menunggu',
            'tanggal' => '2026-04-10',
        ]);

        Pembayaran::query()->create([
            'transaksi_id' => $otherTransaksi->id,
            'jumlah' => 20000,
            'metode' => 'BCA',
            'status' => 'berhasil',
            'tanggal' => '2026-04-10',
        ]);

        $service = $this->app->make(PembayaranService::class);
        $saldo = $service->calculateSaldoNasabah($nasabah->id);

        $this->assertSame(100000.0, $saldo['total_setoran']);
        $this->assertSame(30000.0, $saldo['total_pencairan_berhasil']);
        $this->assertSame(10000.0, $saldo['total_pencairan_pending']);
        $this->assertSame(60000.0, $saldo['saldo_tersedia']);
    }
}
