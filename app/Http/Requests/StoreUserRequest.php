<?php
// app/Http/Requests/StoreUserRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'role_id'   => ['required', 'exists:roles,id'],
            'nip'       => ['nullable', 'string', 'max:50', 'unique:users,nip'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Nama wajib diisi.',
            'name.max'        => 'Nama maksimal 100 karakter.',
            'email.required'  => 'Email wajib diisi.',
            'email.email'     => 'Format email tidak valid.',
            'email.unique'    => 'Email sudah digunakan user lain.',
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists'  => 'Role yang dipilih tidak valid.',
            'nip.unique'      => 'NIP sudah digunakan user lain.',
            'nip.max'         => 'NIP maksimal 50 karakter.',
            'phone.max'       => 'Telepon maksimal 20 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Default is_active = true
        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }

        // Normalize nip
        if ($this->filled('nip') === false) {
            $this->merge(['nip' => null]);
        }
    }
}