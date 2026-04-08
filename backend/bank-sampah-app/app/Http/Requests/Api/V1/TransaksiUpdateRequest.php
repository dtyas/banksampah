<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class TransaksiUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'nasabah_id' => 'required|exists:nasabah,id',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.sampah_id' => 'required|exists:sampah,id',
            'items.*.berat' => 'required|numeric|min:0.01',
            'items.*.subtotal' => 'required|numeric|min:0',
        ];
    }
}
