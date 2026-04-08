<?php

namespace App\Services;

use App\Models\Transaksi;
use App\Repositories\Contracts\TransaksiRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TransaksiService
{
    public function __construct(
        private readonly TransaksiRepositoryInterface $transaksiRepository,
        private readonly AuditTrailService $auditTrailService,
    ) {}

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
        return DB::transaction(function () use ($data): Transaksi {
            $transaksi = $this->transaksiRepository->createWithItems($data);

            $this->auditTrailService->record(
                action: 'transaksi.created',
                entityType: 'transaksi',
                entityId: (int) $transaksi->id,
                actorUserId: isset($data['user_id']) ? (int) $data['user_id'] : null,
                referenceType: 'nasabah',
                referenceId: (int) $transaksi->nasabah_id,
                amount: (float) $transaksi->total_harga,
                meta: [
                    'total_berat' => (float) $transaksi->total_berat,
                    'items_count' => count($data['items'] ?? []),
                    'tanggal' => $transaksi->tanggal,
                ],
            );

            return $transaksi;
        });
    }

    public function update(int $id, array $data): Transaksi
    {
        return DB::transaction(function () use ($id, $data): Transaksi {
            $transaksi = $this->findOrFail($id);
            $before = [
                'total_berat' => (float) $transaksi->total_berat,
                'total_harga' => (float) $transaksi->total_harga,
            ];

            $updated = $this->transaksiRepository->updateWithItems($transaksi, $data);

            $this->auditTrailService->record(
                action: 'transaksi.updated',
                entityType: 'transaksi',
                entityId: (int) $updated->id,
                actorUserId: isset($data['user_id']) ? (int) $data['user_id'] : null,
                referenceType: 'nasabah',
                referenceId: (int) $updated->nasabah_id,
                amount: (float) $updated->total_harga,
                meta: [
                    'before' => $before,
                    'after' => [
                        'total_berat' => (float) $updated->total_berat,
                        'total_harga' => (float) $updated->total_harga,
                    ],
                    'items_count' => count($data['items'] ?? []),
                ],
            );

            return $updated;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $transaksi = $this->findOrFail($id);

            $this->auditTrailService->record(
                action: 'transaksi.deleted',
                entityType: 'transaksi',
                entityId: (int) $transaksi->id,
                actorUserId: (int) $transaksi->user_id,
                referenceType: 'nasabah',
                referenceId: (int) $transaksi->nasabah_id,
                amount: (float) $transaksi->total_harga,
                meta: [
                    'total_berat' => (float) $transaksi->total_berat,
                    'tanggal' => $transaksi->tanggal,
                ],
            );

            $this->transaksiRepository->delete($transaksi);
        });
    }
}
