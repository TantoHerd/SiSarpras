<?php
// app/Http/Requests/StoreMaintenanceRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id'               => ['required', 'exists:items,id'],
            'maintenance_date'      => ['required', 'date'],
            'type'                  => ['required', Rule::in(['rutin', 'perbaikan'])],
            'is_completed'          => ['nullable', 'boolean'],  // ← TAMBAH INI
            'cost'                  => ['nullable', 'numeric', 'min:0'],
            'description'           => ['nullable', 'string', 'max:1000'],
            'technician'            => ['nullable', 'string', 'max:100'],
            'next_maintenance_date' => ['nullable', 'date', 'after:maintenance_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required'           => 'Barang wajib dipilih.',
            'item_id.exists'             => 'Barang yang dipilih tidak valid.',
            'maintenance_date.required'  => 'Tanggal perawatan wajib diisi.',
            'maintenance_date.date'      => 'Format tanggal tidak valid.',
            'type.required'              => 'Jenis perawatan wajib dipilih.',
            'type.in'                    => 'Jenis perawatan tidak valid.',
            'cost.min'                   => 'Biaya tidak boleh negatif.',
            'description.max'            => 'Deskripsi maksimal 1000 karakter.',
            'technician.max'             => 'Nama teknisi maksimal 100 karakter.',
            'next_maintenance_date.after' => 'Jadwal berikutnya harus setelah tanggal perawatan ini.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Default biaya 0
        if (!$this->filled('cost')) {
            $this->merge(['cost' => 0]);
        }
    }
}