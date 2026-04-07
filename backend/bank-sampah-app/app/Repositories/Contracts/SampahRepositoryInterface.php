<?php

namespace App\Repositories\Contracts;

use App\Models\Sampah;
use Illuminate\Database\Eloquent\Collection;

interface SampahRepositoryInterface
{
    public function allWithKategori(): Collection;

    public function findWithKategoriOrFail(int $id): Sampah;

    public function create(array $data): Sampah;

    public function update(Sampah $sampah, array $data): Sampah;

    public function delete(Sampah $sampah): void;
}
