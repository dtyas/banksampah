<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Log;

/**
 * Class XenditService
 *
 * Bertanggung jawab sebagai abstraksi pihak ketiga untuk layanan Xendit.
 * Menangani komunikasi API untuk payout dan saldo Xendit.
 */
class XenditService
{
    private string $baseUrl;

    public function __construct(private readonly XenditAuthService $authService)
    {
        $this->baseUrl = rtrim((string) config('services.xendit.base_url', 'https://api.xendit.co'), '/');
    }

    /**
     * Mengirim instruksi payout ke Xendit (API v2).
     *
     * @param string $referenceId ID referensi payout (unik).
     * @param string $channelCode Kode channel payout (contoh: ID_BCA).
     * @param string $accountNumber Nomor rekening/HP tujuan.
     * @param string $accountHolderName Nama pemilik rekening.
     * @param float $amount Nominal payout.
     * @param string $currency Kode mata uang ISO 4217.
     * @param string|null $description Deskripsi payout.
     * @param string|null $idempotencyKey Kunci untuk mencegah duplikasi.
     * @return array
     * @throws \Exception
     */
    public function createPayout(
        string $referenceId,
        string $channelCode,
        string $accountNumber,
        string $accountHolderName,
        float $amount,
        string $currency = 'IDR',
        ?string $description = null,
        ?string $idempotencyKey = null,
    ): array {
        $idempotencyKey = $idempotencyKey ?: $referenceId;

        $data = [
            'reference_id' => $referenceId,
            'channel_code' => $channelCode,
            'channel_properties' => [
                'account_number' => $accountNumber,
                'account_holder_name' => $accountHolderName,
            ],
            'amount' => $amount,
            'description' => $description ?? 'Pencairan saldo nasabah',
            'currency' => $currency,
        ];

        Log::info('Mengirim payout ke Xendit', $data);
        $response = $this->authService
            ->request()
            ->withHeaders([
                'Idempotency-key' => $idempotencyKey,
            ])
            ->post($this->baseUrl . '/v2/payouts', $data);

        if (! $response->successful()) {
            throw new \Exception('Gagal mengirim payout ke Xendit.');
        }

        return $response->json();
    }

    /**
     * Mengambil saldo Xendit.
     *
     * @param array<string, string|null> $query
     * @return array
     * @throws \Exception
     */
    public function getBalance(array $query = []): array
    {
        $query = Arr::where($query, fn($value) => $value !== null && $value !== '');

        $response = $this->authService
            ->request()
            ->get($this->baseUrl . '/balance', $query);

        if (! $response->successful()) {
            throw new \Exception('Gagal mengambil saldo Xendit.');
        }

        return $response->json();
    }

    /**
     * Validasi webhook token untuk memastikan request benar-benar dari Xendit.
     *
     * @param string $callbackToken Token yang diterima dari header request Xendit.
     * @return bool True jika token valid, false jika tidak valid.
     */
    public function validateWebhookToken(string $callbackToken): bool
    {
        $expectedToken = (string) config('services.xendit.callback_token');

        return $expectedToken !== '' && hash_equals($expectedToken, $callbackToken);
    }

    /**
     * Mengambil sisa saldo cash di akun Xendit BUMDesMa.
     *
     * @return float Saldo yang tersedia di akun Xendit.
     */
    public function getAccountBalance(): float
    {
        try {
            $payload = $this->getBalance();
        } catch (\Exception) {
            return 0.0;
        }

        return (float) ($payload['balance'] ?? 0);
    }
}
