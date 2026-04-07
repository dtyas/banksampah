<?php

namespace App\Repositories\Contracts;

use App\Models\KategoriSampah;
use Illuminate\Database\Eloquent\Collection;

interface KategoriSampahRepositoryInterface
{
    public function all(): Collection;

    public function findOrFail(int $id): KategoriSampah;

    public function create(array $data): KategoriSampah;

    public function update(KategoriSampah $kategoriSampah, array $data): KategoriSampah;

    public function delete(KategoriSampah $kategoriSampah): void;
}
