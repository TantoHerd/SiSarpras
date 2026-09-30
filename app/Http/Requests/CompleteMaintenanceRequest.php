<?php
// app/Http/Requests/CompleteMaintenanceRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'completion_note' => ['nullable', 'string', 'max:500'],
            'final_cost'      => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'completion_note.max' => 'Catatan maksimal 500 karakter.',
            'final_cost.min'      => 'Biaya tidak boleh negatif.',
        ];
    }
}