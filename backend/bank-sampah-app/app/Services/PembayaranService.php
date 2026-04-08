<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Repositories\Contracts\PembayaranRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PembayaranService
{
    public function __construct(
        private readonly PembayaranRepositoryInterface $pembayaranRepository,
        private readonly AuditTrailService $auditTrailService,
    ) {}

    public function all(): Collection
    {
        return $this->pembayaranRepository->allWithTransaksi();
    }

    public function findOrFail(int $id): Pembayaran
    {
        return $this->pembayaranRepository->findWithTransaksiOrFail($id);
    }

    public function create(array $data): Pembayaran
    {
        return DB::transaction(function () use ($data): Pembayaran {
            $actorUserIdFromPayload = $data['actor_user_id'] ?? null;
            unset($data['actor_user_id']);

            $pembayaran = $this->pembayaranRepository->create($data)->load('transaksi');

            $actorUserId = $actorUserIdFromPayload !== null
                ? (int) $actorUserIdFromPayload
                : ($pembayaran->transaksi?->user_id !== null
                ? (int) $pembayaran->transaksi->user_id
                : null);

            $this->auditTrailService->record(
                action: 'pembayaran.created',
                entityType: 'pembayaran',
                entityId: (int) $pembayaran->id,
                actorUserId: $actorUserId,
                referenceType: 'transaksi',
                referenceId: (int) $pembayaran->transaksi_id,
                amount: (float) $pembayaran->jumlah,
                meta: [
                    'status' => $pembayaran->status,
                    'metode' => $pembayaran->metode,
                    'tanggal' => $pembayaran->tanggal,
                ],
            );

            return $pembayaran;
        });
    }

    public function update(int $id, array $data): Pembayaran
    {
        return DB::transaction(function () use ($id, $data): Pembayaran {
            $actorUserIdFromPayload = $data['actor_user_id'] ?? null;
            unset($data['actor_user_id']);

            $pembayaran = $this->findOrFail($id);
            $beforeStatus = $pembayaran->status;
            $beforeJumlah = (float) $pembayaran->jumlah;

            if (($data['status'] ?? null) === 'diverifikasi') {
                $data['verified_at'] = now();
                if ($actorUserIdFromPayload !== null) {
                    $data['verified_by'] = (int) $actorUserIdFromPayload;
                }
            }

            $updated = $this->pembayaranRepository->update($pembayaran, $data)->load('transaksi');

            $actorUserId = $actorUserIdFromPayload !== null
                ? (int) $actorUserIdFromPayload
                : ($updated->transaksi?->user_id !== null
                ? (int) $updated->transaksi->user_id
                : null);

            $this->auditTrailService->record(
                action: 'pembayaran.updated',
                entityType: 'pembayaran',
                entityId: (int) $updated->id,
                actorUserId: $actorUserId,
                referenceType: 'transaksi',
                referenceId: (int) $updated->transaksi_id,
                amount: (float) $updated->jumlah,
                meta: [
                    'before' => [
                        'status' => $beforeStatus,
                        'jumlah' => $beforeJumlah,
                    ],
                    'after' => [
                        'status' => $updated->status,
                        'jumlah' => (float) $updated->jumlah,
                        'verified_at' => $updated->verified_at,
                        'verified_by' => $updated->verified_by,
                    ],
                    'metode' => $updated->metode,
                    'tanggal' => $updated->tanggal,
                ],
            );

            return $updated;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $pembayaran = $this->findOrFail($id);
            $pembayaran->loadMissing('transaksi');

            $actorUserId = $pembayaran->transaksi?->user_id !== null
                ? (int) $pembayaran->transaksi->user_id
                : null;

            $this->auditTrailService->record(
                action: 'pembayaran.deleted',
                entityType: 'pembayaran',
                entityId: (int) $pembayaran->id,
                actorUserId: $actorUserId,
                referenceType: 'transaksi',
                referenceId: (int) $pembayaran->transaksi_id,
                amount: (float) $pembayaran->jumlah,
                meta: [
                    'status' => $pembayaran->status,
                    'metode' => $pembayaran->metode,
                    'tanggal' => $pembayaran->tanggal,
                ],
            );

            $this->pembayaranRepository->delete($pembayaran);
        });
    }
}
