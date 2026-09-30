<?php
// app/Http/Requests/StoreLocationRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:100', 'unique:locations,name'],
            'floor'       => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'parent_id'   => ['nullable', 'exists:locations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lokasi wajib diisi.',
            'name.unique'   => 'Nama lokasi sudah ada.',
            'name.max'      => 'Nama lokasi maksimal 100 karakter.',
            'floor.max'     => 'Lantai/Gedung maksimal 50 karakter.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'parent_id.exists' => 'Lokasi induk tidak valid.',
        ];
    }
}