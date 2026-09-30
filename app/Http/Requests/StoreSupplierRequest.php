<?php
// app/Http/Requests/StoreSupplierRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100', 'unique:suppliers,name'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:100', 'unique:suppliers,email'],
            'address'        => ['nullable', 'string', 'max:500'],
            'contact_person' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama supplier wajib diisi.',
            'name.unique'   => 'Nama supplier sudah ada.',
            'name.max'      => 'Nama supplier maksimal 100 karakter.',
            'phone.max'     => 'Telepon maksimal 20 karakter.',
            'email.email'   => 'Format email tidak valid.',
            'email.unique'  => 'Email sudah digunakan supplier lain.',
            'email.max'     => 'Email maksimal 100 karakter.',
            'address.max'   => 'Alamat maksimal 500 karakter.',
            'contact_person.max' => 'Nama PIC maksimal 100 karakter.',
        ];
    }
}