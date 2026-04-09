<?php

namespace Tests\Unit;

use App\Models\Nasabah;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\PembayaranService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembayaranServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_status_from_payout_succeeded(): void
    {
        $user = User::factory()->create();
        $nasabah = Nasabah::factory()->create();

        $transaksi = Transaksi::query()->create([
            'user_id' => $user->id,
            'nasabah_id' => $nasabah->id,
            'tanggal' => '2026-04-09',
            'total_berat' => 10,
            'total_harga' => 100000,
        ]);

        $pembayaran = Pembayaran::query()->create([
            'transaksi_id' => $transaksi->id,
            'jumlah' => 50000,
            'metode' => 'BCA',
            'status' => 'diproses',
            'tanggal' => '2026-04-09',
        ]);

        $service = $this->app->make(PembayaranService::class);

        $result = $service->updateStatusFromPayoutWebhook(
            referenceId: 'payout-' . $pembayaran->id,
            payoutStatus: 'SUCCEEDED',
            payload: [],
        );

        $this->assertTrue($result);

        $pembayaran->refresh();
        $this->assertSame('berhasil', $pembayaran->status);
        $this->assertNotNull($pembayaran->verified_at);
    }

    public function test_update_status_from_payout_failed(): void
    {
        $user = User::factory()->create();
        $nasabah = Nasabah::factory()->create();

        $transaksi = Transaksi::query()->create([
            'user_id' => $user->id,
            'nasabah_id' => $nasabah->id,
            'tanggal' => '2026-04-09',
            'total_berat' => 5,
            'total_harga' => 45000,
        ]);

        $pembayaran = Pembayaran::query()->create([
            'transaksi_id' => $transaksi->id,
            'jumlah' => 20000,
            'metode' => 'BCA',
            'status' => 'diproses',
            'tanggal' => '2026-04-09',
        ]);

        $service = $this->app->make(PembayaranService::class);

        $result = $service->updateStatusFromPayoutWebhook(
            referenceId: (string) $pembayaran->id,
            payoutStatus: 'FAILED',
            payload: [],
        );

        $this->assertTrue($result);

        $pembayaran->refresh();
        $this->assertSame('ditolak', $pembayaran->status);
    }
}
