<?php

namespace App\Repositories\Contracts;

use App\Models\Nasabah;
use Illuminate\Database\Eloquent\Collection;

interface NasabahRepositoryInterface
{
    public function all(): Collection;

    public function findOrFail(int $id): Nasabah;

    public function create(array $data): Nasabah;

    public function update(Nasabah $nasabah, array $data): Nasabah;

    public function delete(Nasabah $nasabah): void;
}
