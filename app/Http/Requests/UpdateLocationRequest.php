<?php
// app/Http/Requests/UpdateLocationRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locationId = $this->route('location')->id;

        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('locations', 'name')->ignore($locationId),
            ],
            'floor'       => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'parent_id'   => [
                'nullable',
                'exists:locations,id',
                Rule::notIn([$locationId]), // Cegah parent = diri sendiri
            ],
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
            'parent_id.not_in' => 'Lokasi tidak dapat menjadi parent untuk dirinya sendiri.',
        ];
    }
}