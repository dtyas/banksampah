<?php

namespace App\Repositories\Contracts;

use App\Models\Pembayaran;
use Illuminate\Database\Eloquent\Collection;

interface PembayaranRepositoryInterface
{
    public function allWithTransaksi(): Collection;

    public function findWithTransaksiOrFail(int $id): Pembayaran;

    public function create(array $data): Pembayaran;

    public function update(Pembayaran $pembayaran, array $data): Pembayaran;

    public function delete(Pembayaran $pembayaran): void;
}
