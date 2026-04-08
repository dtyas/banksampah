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

    public function updateStatusFromDisbursementCallback(string $externalId, string $disbursementStatus, array $payload = []): bool
    {
        return DB::transaction(function () use ($externalId, $disbursementStatus, $payload): bool {
            $pembayaranId = $this->extractPembayaranIdFromExternalId($externalId);
            if ($pembayaranId === null) {
                return false;
            }

            $pembayaran = Pembayaran::query()->with('transaksi')->find($pembayaranId);
            if (! $pembayaran) {
                return false;
            }

            $mappedStatus = $this->mapDisbursementStatus($disbursementStatus);
            if ($mappedStatus === null) {
                return false;
            }

            $beforeStatus = $pembayaran->status;

            $pembayaran->status = $mappedStatus;
            if ($mappedStatus === 'berhasil' && $pembayaran->verified_at === null) {
                $pembayaran->verified_at = now();
            }
            $pembayaran->save();

            $this->auditTrailService->record(
                action: 'pembayaran.webhook_updated',
                entityType: 'pembayaran',
                entityId: (int) $pembayaran->id,
                actorUserId: null,
                referenceType: 'transaksi',
                referenceId: (int) $pembayaran->transaksi_id,
                amount: (float) $pembayaran->jumlah,
                meta: [
                    'external_id' => $externalId,
                    'disbursement_status' => $disbursementStatus,
                    'before_status' => $beforeStatus,
                    'after_status' => $mappedStatus,
                    'payload' => $payload,
                ],
            );

            return true;
        });
    }

    private function extractPembayaranIdFromExternalId(string $externalId): ?int
    {
        $trimmed = trim($externalId);

        if ($trimmed === '') {
            return null;
        }

        if (ctype_digit($trimmed)) {
            return (int) $trimmed;
        }

        if (preg_match('/(\\d+)$/', $trimmed, $matches) === 1) {
            return (int) $matches[1];
        }

        return null;
    }

    private function mapDisbursementStatus(string $status): ?string
    {
        return match (strtoupper(trim($status))) {
            'COMPLETED' => 'berhasil',
            'FAILED', 'CANCELLED' => 'ditolak',
            'PENDING' => 'diproses',
            default => null,
        };
    }
}
