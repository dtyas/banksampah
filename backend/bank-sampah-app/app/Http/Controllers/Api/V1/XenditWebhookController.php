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
            return $this->errorResponse('Pembayaran tidak ditemukan dari external_id', null, 404);
        }

        return $this->successResponse('Webhook received');
    }
}
