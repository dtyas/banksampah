<?php

namespace App\Services;

use App\Models\Transaksi;
use App\Repositories\Contracts\TransaksiRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TransaksiService
{
    public function __construct(private readonly TransaksiRepositoryInterface $transaksiRepository) {}

    public function all(): Collection
    {
        return $this->transaksiRepository->allWithRelations();
    }

    public function findOrFail(int $id): Transaksi
    {
        return $this->transaksiRepository->findWithRelationsOrFail($id);
    }

    public function create(array $data): Transaksi
    {
        $transaksi = $this->transaksiRepository->createWithItems($data);

        return $transaksi;
    }

    public function update(int $id, array $data): Transaksi
    {
        $transaksi = $this->findOrFail($id);
        $updated = $this->transaksiRepository->updateWithItems($transaksi, $data);

        return $updated;
    }

    public function delete(int $id): void
    {
        $transaksi = $this->findOrFail($id);
        $this->transaksiRepository->delete($transaksi);
    }
}
