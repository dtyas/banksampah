<?php

namespace App\Repositories\Eloquent;

use App\Models\Pembayaran;
use App\Repositories\Contracts\PembayaranRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PembayaranRepository implements PembayaranRepositoryInterface
{
    public function allWithTransaksi(): Collection
    {
        return Pembayaran::query()->with(['transaksi', 'verifier'])->latest()->get();
    }

    public function findWithTransaksiOrFail(int $id): Pembayaran
    {
        return Pembayaran::query()->with(['transaksi', 'verifier'])->findOrFail($id);
    }

    public function create(array $data): Pembayaran
    {
        return Pembayaran::query()->create($data);
    }

    public function update(Pembayaran $pembayaran, array $data): Pembayaran
    {
        $pembayaran->update($data);

        return $pembayaran->refresh();
    }

    public function delete(Pembayaran $pembayaran): void
    {
        $pembayaran->delete();
    }
}
