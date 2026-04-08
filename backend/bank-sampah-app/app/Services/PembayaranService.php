<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Repositories\Contracts\PembayaranRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PembayaranService
{
    public function __construct(private readonly PembayaranRepositoryInterface $pembayaranRepository) {}

    public function all(): Collection
    {
        return $this->pembayaranRepository->allWithTransaksi();
    }

    public function findOrFail(int $id): Pembayaran
    {
        return $this->pembayaranRepository->findWithTransaksiOrFail($id);
    }

    public function create(array $data): Pembayaran
    {
        $pembayaran = $this->pembayaranRepository->create($data)->load('transaksi');

        return $pembayaran;
    }

    public function update(int $id, array $data): Pembayaran
    {
        $pembayaran = $this->findOrFail($id);
        $updated = $this->pembayaranRepository->update($pembayaran, $data)->load('transaksi');

        return $updated;
    }

    public function delete(int $id): void
    {
        $pembayaran = $this->findOrFail($id);
        $this->pembayaranRepository->delete($pembayaran);
    }
}
