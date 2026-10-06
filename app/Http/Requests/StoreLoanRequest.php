<?php
// app/Http/Requests/StoreLoanRequest.php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemId = $this->input('item_id');
        $item = $itemId ? Item::with('category')->find($itemId) : null;

        // Tentukan aturan quantity berdasarkan mode tracking item
        $quantityRules = ['nullable', 'integer', 'min:1'];

        if ($item && $item->isPerBatch()) {
            // Per batch: max = stok tersedia
            $quantityRules[] = 'max:' . $item->quantity;
        } elseif ($item && $item->isPerUnit()) {
            // Per unit: hanya boleh 1
            $quantityRules = ['nullable', 'integer', 'in:1'];
        }

        return [
            'item_id'     => ['required', 'exists:items,id'],
            'quantity'    => $quantityRules,  // ← BARU
            'borrower_id' => ['nullable', 'exists:users,id'],
            'purpose'     => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Barang wajib dipilih.',
            'item_id.exists'   => 'Barang yang dipilih tidak valid.',
            'quantity.min'     => 'Jumlah minimal 1.',
            'quantity.max'     => 'Jumlah melebihi stok tersedia.',
            'quantity.in'      => 'Barang per unit hanya bisa dipinjam 1 unit.',
            'purpose.required' => 'Keperluan peminjaman wajib diisi.',
            'purpose.max'      => 'Keperluan maksimal 500 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Default quantity = 1 kalau tidak diisi
        if (!$this->filled('quantity')) {
            $this->merge(['quantity' => 1]);
        }
    }
}