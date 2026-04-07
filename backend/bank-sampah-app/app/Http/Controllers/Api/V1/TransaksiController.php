<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\TransaksiResource;
use App\Services\TransaksiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransaksiController extends ApiController
{
    public function __construct(private readonly TransaksiService $transaksiService) {}

    public function index(): JsonResponse
    {
        $data = TransaksiResource::collection($this->transaksiService->all());

        return $this->successResponse('Data transaksi berhasil diambil', $data);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nasabah_id' => 'required|exists:nasabah,id',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.sampah_id' => 'required|exists:sampah,id',
            'items.*.berat' => 'required|numeric|min:0.01',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        $transaksi = $this->transaksiService->create($validated);

        return $this->successResponse('Transaksi berhasil ditambahkan', new TransaksiResource($transaksi), 201);
    }

    public function show(int $id): JsonResponse
    {
        $transaksi = $this->transaksiService->findOrFail($id);

        return $this->successResponse('Detail transaksi berhasil diambil', new TransaksiResource($transaksi));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nasabah_id' => 'required|exists:nasabah,id',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.sampah_id' => 'required|exists:sampah,id',
            'items.*.berat' => 'required|numeric|min:0.01',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        $transaksi = $this->transaksiService->update($id, $validated);

        return $this->successResponse('Transaksi berhasil diupdate', new TransaksiResource($transaksi));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->transaksiService->delete($id);

        return $this->successResponse('Transaksi berhasil dihapus');
    }
}
