<?php

namespace App\Services;

use App\Models\Nasabah;
use App\Repositories\Contracts\NasabahRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class NasabahService
{
    public function __construct(private readonly NasabahRepositoryInterface $nasabahRepository) {}

    public function all(): Collection
    {
        return $this->nasabahRepository->all();
    }

    public function findOrFail(int $id): Nasabah
    {
        return $this->nasabahRepository->findOrFail($id);
    }

    public function create(array $data): Nasabah
    {
        $nasabah = $this->nasabahRepository->create($data);

        return $nasabah;
    }

    public function update(int $id, array $data): Nasabah
    {
        $nasabah = $this->findOrFail($id);
        $updated = $this->nasabahRepository->update($nasabah, $data);

        return $updated;
    }

    public function delete(int $id): void
    {
        $nasabah = $this->findOrFail($id);
        $this->nasabahRepository->delete($nasabah);
    }
}
