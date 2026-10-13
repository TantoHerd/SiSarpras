<?php
// app/Http/Requests/StoreStockRequestRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purpose'        => ['required', 'string', 'min:10', 'max:500'],
            'requester_type' => ['required', 'in:guru,staf,petugas'],
            
            'items'                     => ['required', 'array', 'min:1', 'max:5'],
            'items.*.item_id'           => ['required', 'exists:items,id'],
            'items.*.quantity'          => ['required', 'integer', 'min:1', 'max:10'],
            'items.*.note'              => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'purpose.required'        => 'Keperluan wajib diisi.',
            'purpose.min'             => 'Keperluan minimal 10 karakter.',
            'purpose.max'             => 'Keperluan maksimal 500 karakter.',
            'requester_type.required' => 'Tipe pemohon wajib diisi.',
            'requester_type.in'       => 'Tipe pemohon tidak valid.',
            
            'items.required'          => 'Minimal 1 barang harus dipilih.',
            'items.array'             => 'Format barang tidak valid.',
            'items.min'               => 'Minimal 1 barang harus dipilih.',
            'items.max'               => 'Maksimal 5 jenis barang per permintaan.',
            
            'items.*.item_id.required'  => 'Barang wajib dipilih.',
            'items.*.item_id.exists'    => 'Barang yang dipilih tidak valid.',
            'items.*.quantity.required' => 'Jumlah wajib diisi.',
            'items.*.quantity.integer'  => 'Jumlah harus berupa angka.',
            'items.*.quantity.min'      => 'Jumlah minimal 1.',
            'items.*.quantity.max'      => 'Jumlah maksimal 10 per barang.',
            'items.*.note.max'          => 'Catatan maksimal 200 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Trim purpose
        if ($this->filled('purpose')) {
            $this->merge([
                'purpose' => trim($this->purpose),
            ]);
        }
    }
}