<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFundingSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('funding_source')->id;

        return [
            'code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('funding_sources', 'code')->ignore($id),
            ],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('funding_sources', 'name')->ignore($id),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex'  => 'Kode hanya boleh berisi huruf kapital, angka, dan tanda hubung (-).',
            'code.unique' => 'Kode sumber dana sudah digunakan.',
            'name.unique' => 'Nama sumber dana sudah digunakan.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper(trim((string) $this->code)),
        ]);
    }
}