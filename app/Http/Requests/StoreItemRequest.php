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
            'name'                      => ['required', 'string', 'max:200'],
            'category_id'               => ['required', 'exists:categories,id'],
            'location_id'               => ['required', 'exists:locations,id'],
            'supplier_id'               => ['nullable', 'exists:suppliers,id'],
            'funding_source_id'         => ['nullable', 'exists:funding_sources,id'],
            'unit_id'                   => ['required', 'exists:units,id'],
            'tracking_mode'             => ['nullable', 'in:per_unit,per_batch'],  // ← BARU
            'brand'                     => ['nullable', 'string', 'max:100'],
            'type'                      => ['nullable', 'string', 'max:100'],
            'serial_number'             => ['nullable', 'string', 'max:100', Rule::unique('items', 'serial_number')],
            'purchase_year'             => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'price'                     => ['nullable', 'numeric', 'min:0'],
            'condition'                 => ['required', Rule::enum(ItemConditionEnum::class)],
            'quantity'                  => ['required', 'integer', 'min:1'],
            'image'                     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'item_type'                 => ['required', 'in:asset,consumable'],
            'min_stock'                 => ['nullable', 'integer', 'min:0', 'required_if:item_type,consumable'],
            'warranty_start_date'       => ['nullable', 'date'],
            'warranty_duration_months'  => ['nullable', 'integer', 'min:0', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                 => 'Nama barang wajib diisi.',
            'category_id.required'          => 'Kategori wajib dipilih.',
            'category_id.exists'            => 'Kategori yang dipilih tidak valid.',
            'location_id.required'          => 'Lokasi wajib dipilih.',
            'location_id.exists'            => 'Lokasi yang dipilih tidak valid.',
            'unit.required'                 => 'Satuan barang wajib diisi.',
            'serial_number.unique'          => 'Nomor seri sudah terdaftar di barang lain.',
            'purchase_year.min'             => 'Tahun perolehan tidak valid.',
            'purchase_year.max'             => 'Tahun perolehan tidak boleh melebihi tahun depan.',
            'price.min'                     => 'Harga tidak boleh negatif.',
            'condition.required'            => 'Kondisi barang wajib dipilih.',
            'quantity.min'                  => 'Jumlah minimal 1.',
            'image.image'                   => 'File harus berupa gambar.',
            'image.mimes'                   => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'image.max'                     => 'Ukuran gambar maksimal 2MB.',
            'tracking_mode.in'              => 'Mode tracking tidak valid.',
            'item_type.required'            => 'Tipe item wajib dipilih.',
            'item_type.in'                  => 'Tipe item tidak valid.',
            'min_stock.integer'             => 'Minimal stok harus berupa angka.',
            'min_stock.min'                 => 'Minimal stok tidak boleh negatif.',
            'min_stock.required_if'         => 'Minimal stok wajib diisi untuk barang habis pakai.',
            'warranty_duration_months.max'  => 'Durasi garansi maksimal 120 bulan (10 tahun).',
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

        // Kalau tidak ada item_type, default 'asset'
        if (!$this->filled('item_type')) {
            $this->merge(['item_type' => 'asset']);
        }

        // Kalau item_type asset, kosongkan min_stock
        if ($this->input('item_type') === 'asset') {
            $this->merge(['min_stock' => null]);
        }
    }
}