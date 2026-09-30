<?php
// app/Http/Requests/UpdateUserRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'      => ['required', 'string', 'max:100'],
            'email'     => [
                'required', 'string', 'email', 'max:100',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'role_id'   => ['required', 'exists:roles,id'],
            'nip'       => [
                'nullable', 'string', 'max:50',
                Rule::unique('users', 'nip')->ignore($userId),
            ],
            'phone'     => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Nama wajib diisi.',
            'email.required'  => 'Email wajib diisi.',
            'email.email'     => 'Format email tidak valid.',
            'email.unique'    => 'Email sudah digunakan user lain.',
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists'  => 'Role yang dipilih tidak valid.',
            'nip.unique'      => 'NIP sudah digunakan user lain.',
            'phone.max'       => 'Telepon maksimal 20 karakter.',
        ];
    }
}