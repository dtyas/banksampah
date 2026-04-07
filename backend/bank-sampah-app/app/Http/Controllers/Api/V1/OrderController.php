<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\TransaksiResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint API untuk fitur contoh Store Order.
 *
 * Informasi endpoint:
 * - Method: POST
 * - URL: /api/v1/orders/store
 * - Middleware: auth:sanctum
 *
 * Alur kerja singkat:
 * - Controller hanya menerima request dan validasi dasar.
 * - Proses bisnis utama dikerjakan oleh OrderService.
 * - Response dikirim dalam format JSON standar API.
 */
class OrderController extends ApiController
{
    /**
     * @param OrderService $orderService Service yang menangani aturan bisnis Store Order.
     */
    public function __construct(private readonly OrderService $orderService) {}

    /**
     * Menyimpan order baru.
     *
     * @param Request $request Data order dari client (user, nasabah, item, diskon).
     * @return JsonResponse Response JSON berisi status proses, pesan, data order, dan ringkasan perhitungan.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nasabah_id' => 'required|exists:nasabah,id',
            'tanggal' => 'nullable|date',
            'diskon_persen' => 'nullable|numeric|min:0|max:50',
            'items' => 'required|array|min:1',
            'items.*.sampah_id' => 'required|exists:sampah,id',
            'items.*.berat' => 'required|numeric|min:0.01',
        ]);

        $result = $this->orderService->storeOrder($validated);

        return $this->successResponse('Order berhasil disimpan', [
            'order' => new TransaksiResource($result['transaksi']),
            'ringkasan' => $result['ringkasan'],
        ], 201);
    }
}
