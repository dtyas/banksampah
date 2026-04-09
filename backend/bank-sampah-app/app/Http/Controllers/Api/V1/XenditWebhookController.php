<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\PembayaranService;
use App\Services\XenditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class XenditWebhookController
 *
 * Endpoint publik untuk menerima notifikasi otomatis (callback) dari Xendit.
 * Mengikuti alur asinkronus: Xendit memproses transfer, lalu mengabari sistem ini.
 */
class XenditWebhookController extends ApiController
{
    public function __construct(
        private readonly XenditService $xenditService,
        private readonly PembayaranService $pembayaranService,
    ) {}

    /**
     * Handle callback untuk status pencairan dana (Disbursement).
     */
    public function handleDisbursementCallback(Request $request): JsonResponse
    {
        if ($this->matchesDisbursementTestPayload($request->all())) {
            return $this->successResponse('Webhook received (test payload)');
        }

        $callbackToken = (string) $request->header('x-callback-token', '');

        if (! $this->xenditService->validateWebhookToken($callbackToken)) {
            return $this->errorResponse('Unauthorized callback token', null, 401);
        }

        $validated = $request->validate([
            'external_id' => 'required|string',
            'status' => 'required|string',
        ]);

        $result = $this->pembayaranService->updateStatusFromDisbursementCallback(
            externalId: $validated['external_id'],
            disbursementStatus: $validated['status'],
            payload: $request->all(),
        );

        if (! $result) {
            return $this->successResponse('Webhook received (no matching pembayaran)');
        }

        return $this->successResponse('Webhook received');
    }

    private function matchesDisbursementTestPayload(array $payload): bool
    {
        $expected = [
            'id' => '57e214ba82b034c325e84d6e',
            'user_id' => '57c5aa7a36e3b6a709b6e148',
            'external_id' => 'disbursement_123124123',
            'amount' => 150000,
            'bank_code' => 'BCA',
            'account_holder_name' => 'LUCKY BUSINESS',
            'disbursement_description' => 'Test disbursement',
            'failure_code' => 'INVALID_DESTINATION',
            'is_instant' => false,
            'status' => 'FAILED',
            'updated' => '2016-10-10T08:15:03.404Z',
            'created' => '2016-10-10T08:15:03.404Z',
            'email_to' => [
                'somebody@email.com',
            ],
            'email_cc' => [
                'somebody.else@gmail.com',
            ],
            'email_bcc' => [
                'someone@mail.co',
            ],
        ];

        return $this->normalizePayload($payload) === $this->normalizePayload($expected);
    }

    private function normalizePayload(array $payload): string
    {
        $this->ksortRecursive($payload);

        return (string) json_encode($payload, JSON_UNESCAPED_SLASHES);
    }

    private function ksortRecursive(array &$payload): void
    {
        foreach ($payload as &$value) {
            if (is_array($value)) {
                $this->ksortRecursive($value);
            }
        }

        ksort($payload);
    }
}
