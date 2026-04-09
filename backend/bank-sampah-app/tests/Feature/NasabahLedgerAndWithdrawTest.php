<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\AccessControlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NasabahLedgerAndWithdrawTest extends TestCase
{
    use RefreshDatabase;

    public function test_nasabah_can_view_ledger(): void
    {
        $user = User::factory()->create([
            'role' => 'nasabah',
            'menu_access' => ['Pencairan Saldo'],
            'operational_access' => ['Ajukan Pencairan Saldo'],
        ]);
        $nasabah = Nasabah::factory()->create([
            'user_id' => $user->id,
            'account_number' => '000000000099',
            'account_holder_name' => 'Nasabah Test',
        ]);

        $service = $this->app->make(AccessControlService::class);
        $service->syncUserAccess($user, $user->menu_access, $user->operational_access);

        Transaksi::query()->create([
            'user_id' => $user->id,
            'nasabah_id' => $nasabah->id,
            'tanggal' => '2026-04-09',
            'total_berat' => 2,
            'total_harga' => 20000,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/nasabah/me/ledger');

        $response->assertOk();
        $response->assertJsonPath('data.nasabah.id', $nasabah->id);
        $response->assertJsonPath('data.saldo.total_setoran', 20000);
    }

    public function test_withdraw_requires_account_and_balance(): void
    {
        $user = User::factory()->create([
            'role' => 'nasabah',
            'menu_access' => ['Pencairan Saldo'],
            'operational_access' => ['Ajukan Pencairan Saldo'],
        ]);
        $nasabah = Nasabah::factory()->create([
            'user_id' => $user->id,
            'account_number' => null,
            'account_holder_name' => null,
        ]);

        $service = $this->app->make(AccessControlService::class);
        $service->syncUserAccess($user, $user->menu_access, $user->operational_access);

        $transaksi = Transaksi::query()->create([
            'user_id' => $user->id,
            'nasabah_id' => $nasabah->id,
            'tanggal' => '2026-04-09',
            'total_berat' => 2,
            'total_harga' => 50000,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/pencairan-saldo/request', [
            'transaksi_id' => $transaksi->id,
            'jumlah' => 10000,
            'metode' => 'ID_BCA',
            'tanggal' => '2026-04-09',
        ]);

        $response->assertStatus(422);

        $nasabah->update([
            'account_number' => '000000000099',
            'account_holder_name' => 'Nasabah Test',
        ]);

        $response = $this->postJson('/api/v1/pencairan-saldo/request', [
            'transaksi_id' => $transaksi->id,
            'jumlah' => 60000,
            'metode' => 'ID_BCA',
            'tanggal' => '2026-04-09',
        ]);

        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/pencairan-saldo/request', [
            'transaksi_id' => $transaksi->id,
            'jumlah' => 30000,
            'metode' => 'ID_BCA',
            'tanggal' => '2026-04-09',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('pembayaran', [
            'transaksi_id' => $transaksi->id,
            'jumlah' => 30000,
            'status' => 'menunggu',
        ]);

        $pending = Pembayaran::query()->where('transaksi_id', $transaksi->id)->first();
        $this->assertNotNull($pending);
    }
}
