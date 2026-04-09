<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Class XenditService
 *
 * Bertanggung jawab sebagai abstraksi pihak ketiga untuk layanan Xendit.
 * Menangani komunikasi API untuk proses Disbursement (Pencairan Dana)
 * sesuai dengan kebutuhan fitur pencairan saldo nasabah pada Proposal.
 */
class XenditService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.xendit.base_url', 'https://api.xendit.co'), '/');
    }

    /**
     * Mengirim instruksi pencairan dana ke akun E-Wallet atau Bank Nasabah.
     *
     * @param string $externalId ID unik transaksi dari database lokal (ID Pencairan).
     * @param float $amount Nominal saldo yang akan dicairkan.
     * @param string $channelCode Kode bank atau provider e-wallet (OVO, DANA, dll).
     * @param string $accountNumber Nomor rekening atau nomor HP tujuan. (diinput admin/petugas)
     * @return array Response dari API Xendit berupa status dan ID referensi.
     * @throws \Exception Jika terjadi kesalahan koneksi atau saldo Xendit tidak cukup.
     */
    public function sendDisbursement(string $externalId, float $amount, string $channelCode, string $accountNumber): array
    {
        $response = Http::withBasicAuth(config('services.xendit.api_key'), '')
            ->post($this->baseUrl . '/disbursements', [
                'external_id' => $externalId,
                'amount' => $amount,
                'channel_code' => $channelCode,
                'account_number' => $accountNumber,
                'description' => 'Pencairan saldo nasabah',
            ]);

        if (! $response->successful()) {
            throw new \Exception('Gagal mengirim disbursement ke Xendit.');
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
        $response = Http::withBasicAuth(config('services.xendit.api_key'), '')
            ->get($this->baseUrl . '/balance');

        if (! $response->successful()) {
            return 0.0;
        }

        $payload = $response->json();

        return (float) ($payload['balance'] ?? 0);
    }
}
