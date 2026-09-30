<?php
// app/Http/Requests/ReturnLoanRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'condition_note' => ['nullable', 'string', 'max:500'],
            'is_fine_paid'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'condition_note.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}