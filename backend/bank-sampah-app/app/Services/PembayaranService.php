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
        private readonly XenditService $xenditService,
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

            $updated = $this->pembayaranRepository->update($pembayaran, $data)->load('transaksi.nasabah');

            $payoutMeta = null;
            if ($this->isPayoutStatusTransition($beforeStatus, $updated->status, $updated->metode)) {
                $payoutMeta = $this->requestPayout($updated);
            }

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
                    'payout' => $payoutMeta,
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

    public function updateStatusFromPayoutWebhook(string $referenceId, string $payoutStatus, array $payload = []): bool
    {
        return DB::transaction(function () use ($referenceId, $payoutStatus, $payload): bool {
            $pembayaranId = $this->extractPembayaranIdFromExternalId($referenceId);
            if ($pembayaranId === null) {
                return false;
            }

            $pembayaran = Pembayaran::query()->with('transaksi')->find($pembayaranId);
            if (! $pembayaran) {
                return false;
            }

            $mappedStatus = $this->mapPayoutStatus($payoutStatus);
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
                    'reference_id' => $referenceId,
                    'payout_status' => $payoutStatus,
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

    private function mapPayoutStatus(string $status): ?string
    {
        return match (strtoupper(trim($status))) {
            'ACCEPTED', 'REQUESTED' => 'diproses',
            'COMPLETED', 'SUCCEEDED', 'SUCCESS' => 'berhasil',
            'FAILED', 'CANCELLED', 'CANCELED', 'REVERSED' => 'ditolak',
            'PENDING', 'PROCESSING' => 'diproses',
            default => null,
        };
    }

    private function isPayoutStatusTransition(string $beforeStatus, string $afterStatus, ?string $metode): bool
    {
        if ($beforeStatus === 'diproses' || $afterStatus !== 'diproses') {
            return false;
        }

        $metodeValue = strtolower(trim((string) $metode));

        return str_contains($metodeValue, 'wallet')
            || str_contains($metodeValue, 'transfer')
            || str_contains($metodeValue, 'bank')
            || str_contains($metodeValue, 'disbursement')
            || str_contains($metodeValue, 'xendit')
            || str_contains($metodeValue, 'pencairan');
    }

    /**
     * @return array<string, mixed>
     */
    private function requestPayout(Pembayaran $pembayaran): array
    {
        $xenditSecretKey = (string) config('services.xendit.secret_key');
        if ($xenditSecretKey === '') {
            return [
                'mode' => 'sandbox-skip',
                'reason' => 'XENDIT_SECRET_KEY is not configured',
                'external_id' => (string) $pembayaran->id,
            ];
        }

        $nasabah = $pembayaran->transaksi?->nasabah;
        $accountNumber = (string) ($nasabah?->account_number ?? '');
        if ($accountNumber === '') {
            $accountNumber = preg_replace('/\D+/', '', (string) ($nasabah?->no_hp ?? '')) ?: '0000000000';
        }
        $accountHolderName = trim((string) ($nasabah?->account_holder_name ?? ''));
        if ($accountHolderName === '') {
            $accountHolderName = trim((string) ($nasabah?->nama ?? 'Nasabah')) ?: 'Nasabah';
        }
        $channelCode = $nasabah?->payout_channel ?: $this->resolveChannelCode($pembayaran->metode);
        $referenceId = 'payout-' . $pembayaran->id;

        $response = $this->xenditService->createPayout(
            referenceId: $referenceId,
            channelCode: $channelCode,
            accountNumber: $accountNumber,
            accountHolderName: $accountHolderName,
            amount: (float) $pembayaran->jumlah,
            currency: 'IDR',
            description: 'Pencairan saldo nasabah',
            idempotencyKey: $referenceId,
        );

        return [
            'mode' => 'xendit-live',
            'external_id' => $referenceId,
            'channel_code' => $channelCode,
            'account_number' => $accountNumber,
            'account_holder_name' => $accountHolderName,
            'response' => $response,
        ];
    }

    private function resolveChannelCode(?string $metode): string
    {
        $metodeValue = strtolower(trim((string) $metode));
        $metodeUpper = strtoupper(trim((string) $metode));

        if ($metodeUpper !== '' && str_starts_with($metodeUpper, 'ID_')) {
            return $metodeUpper;
        }

        return match (true) {
            str_contains($metodeValue, 'ovo') => 'ID_OVO',
            str_contains($metodeValue, 'dana') => 'ID_DANA',
            str_contains($metodeValue, 'gopay') => 'ID_GOPAY',
            str_contains($metodeValue, 'bni') => 'ID_BNI',
            str_contains($metodeValue, 'bri') => 'ID_BRI',
            str_contains($metodeValue, 'mandiri') => 'ID_MANDIRI',
            str_contains($metodeValue, 'bca') => 'ID_BCA',
            default => 'ID_BCA',
        };
    }
}
