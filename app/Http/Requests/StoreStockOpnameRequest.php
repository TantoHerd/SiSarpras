<?php
// app/Http/Requests/StoreStockOpnameRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockOpnameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location_id' => ['required', 'exists:locations,id'],
            'note'        => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'location_id.required' => 'Lokasi wajib dipilih.',
            'location_id.exists'   => 'Lokasi tidak valid.',
            'note.max'             => 'Catatan maksimal 500 karakter.',
        ];
    }
}