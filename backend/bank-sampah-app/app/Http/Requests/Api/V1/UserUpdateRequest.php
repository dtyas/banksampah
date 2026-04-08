<?php

namespace App\Http\Requests\Api\V1;

use App\Services\AccessControlService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = (int) $this->route('user');

        return [
            'nama' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => 'sometimes|required|string|min:8|confirmed',
            'role' => ['sometimes', 'required', Rule::in(['super_admin', 'petugas', 'nasabah'])],
            'status' => ['sometimes', 'required', Rule::in(['Aktif', 'Inactive'])],
            'menu_access' => 'sometimes|array',
            'menu_access.*' => ['string', Rule::in(AccessControlService::MENU_OPTIONS)],
            'operational_access' => 'sometimes|array',
            'operational_access.*' => ['string', Rule::in(AccessControlService::OPERATIONAL_OPTIONS)],
        ];
    }
}
