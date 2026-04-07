<?php

namespace App\Repositories\Eloquent;

use App\Models\DetailTransaksi;
use App\Models\Sampah;
use App\Models\Transaksi;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Implementasi akses data Order menggunakan Eloquent.
 *
 * Fokus class ini:
 * - Menjalankan query database.
 * - Menjaga proses simpan header + detail tetap aman dengan transaksi DB.
 * - Tidak berisi aturan bisnis (aturan bisnis ada di Service).
 */
class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Mengambil data sampah yang dipakai di order.
     *
     * Query yang digunakan:
     * - Eloquent whereIn pada tabel sampah.
     *
     * @param array<int, int> $sampahIds
     * @return Collection<int, Sampah>
     */
    public function findSampahByIds(array $sampahIds): Collection
    {
        return Sampah::query()
            ->whereIn('id', $sampahIds)
            ->get();
    }

    /**
     * Menyimpan order ke tabel transaksi dan detail_transaksi.
     *
     * Query yang digunakan:
     * - DB::transaction untuk memastikan data header dan detail selalu konsisten.
     * - Eloquent create untuk transaksi dan detail_transaksi.
     *
     * @param array<string, mixed> $payload
     * @return Transaksi
     */
    public function storeOrder(array $payload): Transaksi
    {
        return DB::transaction(function () use ($payload): Transaksi {
            /** @var array<int, array<string, mixed>> $items */
            $items = $payload['items'];

            $transaksi = Transaksi::query()->create([
                'user_id' => $payload['user_id'],
                'nasabah_id' => $payload['nasabah_id'],
                'tanggal' => $payload['tanggal'],
                'total_berat' => $payload['total_berat'],
                'total_harga' => $payload['total_harga_setelah_diskon'],
            ]);

            foreach ($items as $item) {
                DetailTransaksi::query()->create([
                    'transaksi_id' => $transaksi->id,
                    'sampah_id' => $item['sampah_id'],
                    'berat' => $item['berat'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return Transaksi::query()
                ->with(['user', 'nasabah', 'detailTransaksi.sampah', 'pembayaran'])
                ->findOrFail($transaksi->id);
        });
    }
}
