<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class PencairanSaldoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaksi_id' => 'required|exists:transaksi,id',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|string|max:255',
            'tanggal' => 'nullable|date',
        ];
    }
}
