<?php
// app/Http/Requests/UpdateCategoryRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')->id;

        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'description'           => ['nullable', 'string', 'max:500'],
            'icon'                  => ['nullable', 'string', 'max:50'],
            'default_tracking_mode' => ['required', 'in:per_unit,per_batch'],
            'allow_student_loan' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori sudah ada.',
            'name.max'      => 'Nama kategori maksimal 100 karakter.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'icon.max'      => 'Icon maksimal 50 karakter.',
            'default_tracking_mode.required' => 'Mode tracking wajib dipilih.',
            'default_tracking_mode.in'       => 'Mode tracking tidak valid.',  
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->filled('icon')) {
            $this->merge(['icon' => 'fa-boxes']);
        }

        if (!$this->filled('default_tracking_mode')) {       
            $this->merge(['default_tracking_mode' => 'per_unit']);
        }

        $this->merge([
            'allow_student_loan' => $this->boolean('allow_student_loan'),
        ]);
    }
}