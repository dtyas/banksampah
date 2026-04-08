<?php

namespace App\Http\Requests\Api\V1;

use App\Services\AccessControlService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['super_admin', 'petugas', 'nasabah'])],
            'status' => ['nullable', Rule::in(['Aktif', 'Inactive'])],
            'menu_access' => 'nullable|array',
            'menu_access.*' => ['string', Rule::in(AccessControlService::MENU_OPTIONS)],
            'operational_access' => 'nullable|array',
            'operational_access.*' => ['string', Rule::in(AccessControlService::OPERATIONAL_OPTIONS)],
        ];
    }
}
