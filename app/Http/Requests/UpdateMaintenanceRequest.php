<?php
// app/Http/Requests/UpdateMaintenanceRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'maintenance_date'      => ['required', 'date'],
            'type'                  => ['required', Rule::in(['rutin', 'perbaikan'])],
            'cost'                  => ['nullable', 'numeric', 'min:0'],
            'description'           => ['nullable', 'string', 'max:1000'],
            'technician'            => ['nullable', 'string', 'max:100'],
            'next_maintenance_date' => ['nullable', 'date', 'after:maintenance_date'],
        ];
    }

    public function messages(): array
    {
        return [
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
        if (!$this->filled('cost')) {
            $this->merge(['cost' => 0]);
        }
    }
}