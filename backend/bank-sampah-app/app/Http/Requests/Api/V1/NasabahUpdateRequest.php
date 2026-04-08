<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Nasabah;
use App\Services\AccessControlService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NasabahUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $nasabahId = (int) $this->route('nasabah');
        $nasabah = Nasabah::query()->find($nasabahId);

        return [
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($nasabah?->user_id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'nullable|in:Aktif,Inactive',
            'menu_access' => 'nullable|array',
            'menu_access.*' => ['string', Rule::in(AccessControlService::MENU_OPTIONS)],
            'operational_access' => 'nullable|array',
            'operational_access.*' => ['string', Rule::in(AccessControlService::OPERATIONAL_OPTIONS)],
        ];
    }
}
