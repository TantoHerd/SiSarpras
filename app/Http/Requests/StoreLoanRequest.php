<?php
// app/Http/Requests/StoreLoanRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id'     => ['required', 'exists:items,id'],
            'borrower_id' => ['nullable', 'exists:users,id'],
            'purpose'     => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Barang wajib dipilih.',
            'item_id.exists'   => 'Barang yang dipilih tidak valid.',
            'purpose.required' => 'Keperluan peminjaman wajib diisi.',
            'purpose.max'      => 'Keperluan maksimal 500 karakter.',
        ];
    }
}