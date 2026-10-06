<?php
// app/Http/Requests/UpdateSettingRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        return $user && $user->isAdmin();
    }

    public function rules(): array
    {
        $group = $this->input('group_name', 'school');
        $rules = [];

        switch ($group) {
            case 'school':
                $rules = [
                    'school_name'     => ['required', 'string', 'max:150'],
                    'school_address'  => ['required', 'string', 'max:500'],
                    'school_phone'    => ['required', 'string', 'max:30'],
                    'school_email'    => ['required', 'email', 'max:100'],
                    'school_npsn'     => ['nullable', 'string', 'max:30'],
                    'school_website'  => ['nullable', 'url', 'max:150'],
                    'school_logo'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                ];
                break;

            case 'headmaster':
                $rules = [
                    'headmaster_name' => ['nullable', 'string', 'max:150'],
                    'headmaster_nip'  => ['nullable', 'string', 'max:50'],
                ];
                break;

            case 'preference':
                $rules = [
                    'app_name'          => ['required', 'string', 'max:100'],
                    'app_short_name'    => ['required', 'string', 'max:30'],
                    'app_version'       => ['required', 'string', 'max:20'],
                    'timezone'          => ['required', 'string', 'max:50'],
                    'date_format'       => ['required', 'in:d-m-Y,Y-m-d,d/m/Y,j F Y'],
                    'currency_symbol'   => ['required', 'string', 'max:10'],
                    'currency_position' => ['required', 'in:before,after'],
                ];
                break;

            case 'loan':
                $rules = [
                    'max_loan_days'      => ['required', 'integer', 'min:1', 'max:365'],
                    'max_loan_per_user'  => ['required', 'integer', 'min:1', 'max:50'],
                    'is_fine_active'     => ['nullable', 'boolean'],
                    'fine_per_day'       => ['required_if:is_fine_active,1', 'nullable', 'integer', 'min:0'],
                ];
                break;

            case 'portal':
                $rules = [
                    'portal_siswa_enabled'      => ['nullable', 'boolean'],
                    'portal_siswa_max_loans'    => ['required', 'integer', 'min:1', 'max:20'],
                    'portal_siswa_max_days'     => ['required', 'integer', 'min:1', 'max:30'],
                    'portal_siswa_welcome_text' => ['nullable', 'string', 'max:500'],
                ];
                break;
        }

        // Tambahkan field global
        $rules['group_name'] = ['required', 'string', 'in:school,headmaster,preference,loan,portal'];
        $rules['redirect_tab'] = ['nullable', 'string', 'in:school,headmaster,preference,loan,portal'];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'school_name.required'    => 'Nama sekolah wajib diisi.',
            'school_address.required' => 'Alamat sekolah wajib diisi.',
            'school_phone.required'   => 'Telepon sekolah wajib diisi.',
            'school_email.required'   => 'Email sekolah wajib diisi.',
            'school_email.email'      => 'Format email sekolah tidak valid.',
            'school_website.url'      => 'Format URL website tidak valid.',
            'school_logo.image'       => 'Logo harus berupa gambar.',
            'school_logo.mimes'       => 'Logo harus berformat jpg, jpeg, png, atau webp.',
            'school_logo.max'         => 'Ukuran logo maksimal 2MB.',
            
            'app_name.required'       => 'Nama aplikasi wajib diisi.',
            'app_short_name.required' => 'Singkatan aplikasi wajib diisi.',
            'timezone.required'       => 'Timezone wajib diisi.',
            
            'max_loan_days.required'  => 'Batas hari peminjaman wajib diisi.',
            'max_loan_days.min'       => 'Batas hari peminjaman minimal 1 hari.',
            'max_loan_per_user.required' => 'Batas pinjam per user wajib diisi.',
            'fine_per_day.required_if'   => 'Denda per hari wajib diisi jika denda diaktifkan.',

            'portal_siswa_max_loans.required' => 'Batas pinjaman siswa wajib diisi.',
            'portal_siswa_max_loans.min'      => 'Batas pinjaman minimal 1.',
            'portal_siswa_max_loans.max'      => 'Batas pinjaman maksimal 20.',
            'portal_siswa_max_days.required'  => 'Durasi pinjam siswa wajib diisi.',
            'portal_siswa_max_days.min'       => 'Durasi pinjam minimal 1 hari.',
            'portal_siswa_max_days.max'       => 'Durasi pinjam maksimal 30 hari.',
            'portal_siswa_welcome_text.max'   => 'Teks sambutan maksimal 500 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Konversi checkbox "is_fine_active"
        if ($this->has('is_fine_active')) {
            $this->merge([
                'is_fine_active' => $this->boolean('is_fine_active') ? 1 : 0,
            ]);
        } else {
            $this->merge(['is_fine_active' => 0]);
        }
    }
}