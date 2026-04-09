<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\XenditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class XenditController extends ApiController
{
    /**
     * @param XenditService $xenditService
     */
    public function __construct(private readonly XenditService $xenditService) {}

    /**
     * Mengambil saldo Xendit untuk dashboard admin/petugas.
     */
    public function balance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account_type' => 'nullable|in:CASH,HOLDING',
            'currency' => 'nullable|in:IDR,PHP,USD,VND,THB,MYR,SGD,EUR,GBP,HKD,AUD',
            'at_timestamp' => 'nullable|date',
        ]);

        try {
            $payload = $this->xenditService->getBalance($validated);
        } catch (\Exception $exception) {
            return $this->errorResponse('Gagal mengambil saldo Xendit', [
                'message' => $exception->getMessage(),
            ], 502);
        }

        return $this->successResponse('Saldo Xendit berhasil diambil', $payload);
    }
}
