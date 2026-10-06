<?php
// app/Http/Requests/UpdateStudentRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('student')->id;

        return [
            'nis'       => ['required', 'string', 'max:20', Rule::unique('students', 'nis')->ignore($id)],
            'name'      => ['required', 'string', 'max:100'],
            'class'     => ['required', 'string', 'max:20'],
            'phone'     => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required'   => 'NIS wajib diisi.',
            'nis.unique'     => 'NIS sudah terdaftar.',
            'name.required'  => 'Nama siswa wajib diisi.',
            'class.required' => 'Kelas wajib diisi.',
            'phone.regex'    => 'Format nomor HP tidak valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nis'   => trim((string) $this->nis),
            'name'  => trim((string) $this->name),
            'class' => trim((string) $this->class),
        ]);
    }
}