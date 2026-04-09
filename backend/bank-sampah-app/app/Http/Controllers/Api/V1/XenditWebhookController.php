<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\PembayaranService;
use App\Services\XenditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class XenditWebhookController
 *
 * Endpoint publik untuk menerima notifikasi payout dari Xendit.
 */
class XenditWebhookController extends ApiController
{
    public function __construct(
        private readonly XenditService $xenditService,
        private readonly PembayaranService $pembayaranService,
    ) {}

    /**
     * Handle webhook payout dari Xendit.
     */
    public function handlePayoutWebhook(Request $request): JsonResponse
    {
        $callbackToken = (string) $request->header('x-callback-token', '');

        if (! $this->xenditService->validateWebhookToken($callbackToken)) {
            return $this->errorResponse('Unauthorized callback token', null, 401);
        }

        $validated = $request->validate([
            'event' => 'required|string',
            'data.reference_id' => 'required|string',
            'data.status' => 'nullable|string',
        ]);

        $referenceId = (string) data_get($validated, 'data.reference_id');
        $status = (string) (data_get($validated, 'data.status') ?: $this->mapEventToStatus($validated['event']));

        $result = $this->pembayaranService->updateStatusFromPayoutWebhook(
            referenceId: $referenceId,
            payoutStatus: $status,
            payload: $request->all(),
        );

        if (! $result) {
            return $this->successResponse('Webhook received (no matching pembayaran)');
        }

        return $this->successResponse('Webhook received');
    }

    private function mapEventToStatus(string $event): string
    {
        return match ($event) {
            'payout.succeeded' => 'SUCCEEDED',
            'payout.failed' => 'FAILED',
            'payout.reversed' => 'REVERSED',
            default => 'REQUESTED',
        };
    }
}
