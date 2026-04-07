<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\PembayaranResource;
use App\Services\PembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PembayaranController extends ApiController
{
    public function __construct(private readonly PembayaranService $pembayaranService) {}

    public function index(): JsonResponse
    {
        $data = PembayaranResource::collection($this->pembayaranService->all());

        return $this->successResponse('Data pembayaran berhasil diambil', $data);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|string|max:255',
            'status' => 'required|in:menunggu,diverifikasi,diproses,berhasil,ditolak',
            'tanggal' => 'required|date',
        ]);

        $pembayaran = $this->pembayaranService->create($validated);

        return $this->successResponse('Data pembayaran berhasil ditambahkan', new PembayaranResource($pembayaran), 201);
    }

    public function show(int $id): JsonResponse
    {
        $pembayaran = $this->pembayaranService->findOrFail($id);

        return $this->successResponse('Detail pembayaran berhasil diambil', new PembayaranResource($pembayaran));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|string|max:255',
            'status' => 'required|in:menunggu,diverifikasi,diproses,berhasil,ditolak',
            'tanggal' => 'required|date',
        ]);

        $pembayaran = $this->pembayaranService->update($id, $validated);

        return $this->successResponse('Data pembayaran berhasil diupdate', new PembayaranResource($pembayaran));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->pembayaranService->delete($id);

        return $this->successResponse('Data pembayaran berhasil dihapus');
    }
}
