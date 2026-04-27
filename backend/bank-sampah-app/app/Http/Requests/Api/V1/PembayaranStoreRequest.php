<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class PembayaranStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nasabah_id' => 'nullable|exists:nasabah,id',
            'transaksi_id' => 'required|exists:transaksi,id',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|string|max:255',
            'status' => 'required|in:menunggu,diverifikasi,diproses,berhasil,ditolak',
            'tanggal' => 'required|date',
        ];
    }
}
