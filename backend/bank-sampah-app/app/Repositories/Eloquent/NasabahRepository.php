<?php

namespace App\Repositories\Eloquent;

use App\Models\Nasabah;
use App\Repositories\Contracts\NasabahRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class NasabahRepository implements NasabahRepositoryInterface
{
    public function all(): Collection
    {
        return Nasabah::query()
            ->with('user')
            ->whereHas('user', fn($query) => $query->where('role', 'nasabah'))
            ->latest()
            ->get();
    }

    public function findOrFail(int $id): Nasabah
    {
        return Nasabah::query()
            ->with('user')
            ->whereHas('user', fn($query) => $query->where('role', 'nasabah'))
            ->findOrFail($id);
    }

    public function create(array $data): Nasabah
    {
        return Nasabah::query()->create($data);
    }

    public function update(Nasabah $nasabah, array $data): Nasabah
    {
        $nasabah->update($data);

        return $nasabah->refresh();
    }

    public function delete(Nasabah $nasabah): void
    {
        $nasabah->delete();
    }
}
