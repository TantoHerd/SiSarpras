<?php
// app/Http/Requests/PortalLoanRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortalLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public
    }

    public function rules(): array
    {
        return [
            'nis'         => ['required', 'string', 'max:20'],
            'student_phone' => ['nullable', 'string', 'max:20'],
            'item_id'     => ['required', 'exists:items,id'],
            'quantity'    => ['nullable', 'integer', 'min:1'],
            'purpose'     => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required'      => 'NIS wajib diisi.',
            'item_id.required'  => 'Barang wajib dipilih.',
            'item_id.exists'    => 'Barang yang dipilih tidak valid.',
            'quantity.min'      => 'Jumlah minimal 1.',
            'purpose.required'  => 'Keperluan wajib diisi.',
            'purpose.min'       => 'Keperluan minimal 10 karakter.',
            'purpose.max'       => 'Keperluan maksimal 500 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nis' => trim((string) $this->nis),
            'quantity' => $this->filled('quantity') ? (int) $this->quantity : 1,
        ]);
    }
}