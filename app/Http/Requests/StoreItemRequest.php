<?php
// app/Http/Requests/StoreItemRequest.php

namespace App\Http\Requests;

use App\Enums\ItemConditionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:200'],
            'category_id'   => ['required', 'exists:categories,id'],
            'location_id'   => ['required', 'exists:locations,id'],
            'supplier_id'   => ['nullable', 'exists:suppliers,id'],
            'brand'         => ['nullable', 'string', 'max:100'],
            'type'          => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:100', 'unique:items,serial_number'],
            'purchase_year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'condition'     => ['required', Rule::enum(ItemConditionEnum::class)],
            'quantity'      => ['required', 'integer', 'min:1'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'Nama barang wajib diisi.',
            'category_id.required'  => 'Kategori wajib dipilih.',
            'category_id.exists'    => 'Kategori yang dipilih tidak valid.',
            'location_id.required'  => 'Lokasi wajib dipilih.',
            'location_id.exists'    => 'Lokasi yang dipilih tidak valid.',
            'serial_number.unique'  => 'Nomor seri sudah terdaftar di barang lain.',
            'purchase_year.min'     => 'Tahun perolehan tidak valid.',
            'purchase_year.max'     => 'Tahun perolehan tidak boleh melebihi tahun depan.',
            'price.min'             => 'Harga tidak boleh negatif.',
            'condition.required'    => 'Kondisi barang wajib dipilih.',
            'quantity.min'          => 'Jumlah minimal 1.',
            'image.image'           => 'File harus berupa gambar.',
            'image.mimes'           => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'image.max'             => 'Ukuran gambar maksimal 2MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalize serial_number (null jika kosong)
        if (empty($this->serial_number)) {
            $this->merge(['serial_number' => null]);
        }

        // Default condition
        if (empty($this->condition)) {
            $this->merge(['condition' => 'baik']);
        }

        // Default quantity
        if (empty($this->quantity)) {
            $this->merge(['quantity' => 1]);
        }
    }
}