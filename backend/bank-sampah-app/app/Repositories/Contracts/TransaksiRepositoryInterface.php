<?php

namespace App\Repositories\Contracts;

use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Collection;

interface TransaksiRepositoryInterface
{
    public function allWithRelations(): Collection;

    public function findWithRelationsOrFail(int $id): Transaksi;

    public function createWithItems(array $data): Transaksi;

    public function updateWithItems(Transaksi $transaksi, array $data): Transaksi;

    public function delete(Transaksi $transaksi): void;
}
