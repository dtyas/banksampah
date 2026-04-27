<?php

namespace App\Repositories\Eloquent;

use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use App\Repositories\Contracts\TransaksiRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TransaksiRepository implements TransaksiRepositoryInterface
{
    public function allWithRelations(): Collection
    {
        return Transaksi::query()
            ->with(['user', 'nasabah', 'detailTransaksi.sampah', 'pembayaran'])
            ->latest()
            ->get();
    }

    public function allByNasabahWithRelations(int $nasabahId): Collection
    {
        return Transaksi::query()
            ->with(['user', 'nasabah', 'detailTransaksi.sampah', 'pembayaran'])
            ->where('nasabah_id', $nasabahId)
            ->latest()
            ->get();
    }

    public function findWithRelationsOrFail(int $id): Transaksi
    {
        return Transaksi::query()
            ->with(['user', 'nasabah', 'detailTransaksi.sampah', 'pembayaran'])
            ->findOrFail($id);
    }

    public function createWithItems(array $data): Transaksi
    {
        $items = $data['items'];
        $totalBerat = collect($items)->sum('berat');
        $totalHarga = collect($items)->sum('subtotal');

        $transaksi = Transaksi::query()->create([
            'user_id' => $data['user_id'],
            'nasabah_id' => $data['nasabah_id'],
            'tanggal' => $data['tanggal'],
            'total_berat' => $totalBerat,
            'total_harga' => $totalHarga,
        ]);

        foreach ($items as $item) {
            // Support legacy data: allow missing sampah_id
            $detailData = [
                'transaksi_id' => $transaksi->id,
                'berat' => $item['berat'],
                'subtotal' => $item['subtotal'],
            ];
            if (isset($item['sampah_id']) && $item['sampah_id']) {
                $detailData['sampah_id'] = $item['sampah_id'];
            }
            DetailTransaksi::query()->create($detailData);
        }

        return $this->findWithRelationsOrFail((int) $transaksi->id);
    }

    public function updateWithItems(Transaksi $transaksi, array $data): Transaksi
    {
        $items = $data['items'];
        $totalBerat = collect($items)->sum('berat');
        $totalHarga = collect($items)->sum('subtotal');

        $transaksi->update([
            'user_id' => $data['user_id'],
            'nasabah_id' => $data['nasabah_id'],
            'tanggal' => $data['tanggal'],
            'total_berat' => $totalBerat,
            'total_harga' => $totalHarga,
        ]);

        $transaksi->detailTransaksi()->delete();

        foreach ($items as $item) {
            // Support legacy data: allow missing sampah_id
            $detailData = [
                'transaksi_id' => $transaksi->id,
                'berat' => $item['berat'],
                'subtotal' => $item['subtotal'],
            ];
            if (isset($item['sampah_id']) && $item['sampah_id']) {
                $detailData['sampah_id'] = $item['sampah_id'];
            }
            DetailTransaksi::query()->create($detailData);
        }

        return $this->findWithRelationsOrFail((int) $transaksi->id);
    }

    public function delete(Transaksi $transaksi): void
    {
        $transaksi->delete();
    }
}
