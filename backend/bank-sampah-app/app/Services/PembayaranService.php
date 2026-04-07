<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Repositories\Contracts\PembayaranRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PembayaranService
{
    public function __construct(private readonly PembayaranRepositoryInterface $pembayaranRepository) {}

    public function all(): Collection
    {
        return Cache::remember($this->cacheKeyAll(), $this->cacheTtl(), function (): Collection {
            return $this->pembayaranRepository->allWithTransaksi();
        });
    }

    public function findOrFail(int $id): Pembayaran
    {
        return Cache::remember($this->cacheKeyById($id), $this->cacheTtl(), function () use ($id): Pembayaran {
            return $this->pembayaranRepository->findWithTransaksiOrFail($id);
        });
    }

    public function create(array $data): Pembayaran
    {
        $pembayaran = $this->pembayaranRepository->create($data)->load('transaksi');
        $this->bustCache();

        return $pembayaran;
    }

    public function update(int $id, array $data): Pembayaran
    {
        $pembayaran = $this->findOrFail($id);
        $updated = $this->pembayaranRepository->update($pembayaran, $data)->load('transaksi');
        $this->bustCache($id);

        return $updated;
    }

    public function delete(int $id): void
    {
        $pembayaran = $this->findOrFail($id);
        $this->pembayaranRepository->delete($pembayaran);
        $this->bustCache($id);
    }

    private function cacheTtl(): int
    {
        return (int) env('CACHE_TTL_SECONDS', 300);
    }

    private function cacheKeyAll(): string
    {
        return 'pembayaran.all';
    }

    private function cacheKeyById(int $id): string
    {
        return 'pembayaran.' . $id;
    }

    private function bustCache(?int $id = null): void
    {
        Cache::forget($this->cacheKeyAll());

        if ($id !== null) {
            Cache::forget($this->cacheKeyById($id));
        }
    }
}
