{{-- resources/views/students/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Siswa')
@section('page_title', 'Tambah Siswa')
@section('page_subtitle', 'Daftarkan siswa baru untuk portal peminjaman')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('students.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-user-graduate text-xs"></i> Data Siswa
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Tambah Siswa</span>
</nav>

<form method="POST" action="{{ route('students.store') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Preview ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card text-center sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4 text-left">Preview</h3>

                {{-- Icon Avatar --}}
                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 rounded-2xl bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-user-graduate text-primary text-4xl"></i>
                    </div>
                </div>

                {{-- Nama --}}
                <h4 id="preview-name" class="text-body-md-bold text-ink-deep mb-2">
                    Nama Siswa
                </h4>

                {{-- NIS --}}
                <p id="preview-nis" class="text-caption text-steel font-mono mb-2">
                    NIS: -
                </p>

                {{-- Kelas Badge --}}
                <div class="mb-4">
                    <span id="preview-class" class="badge-info">Kelas</span>
                </div>

                {{-- Phone --}}
                <p id="preview-phone" class="text-body-sm text-steel mb-4">
                    <i class="fas fa-phone text-xs"></i> -
                </p>

                {{-- Info Box --}}
                <div class="pt-4 mt-4 border-t border-hairline-soft">
                    <div class="flex items-start gap-2 text-left">
                        <i class="fas fa-info-circle text-primary mt-0.5 text-sm"></i>
                        <div>
                            <p class="text-caption-bold text-ink-deep">Portal Peminjaman</p>
                            <p class="text-caption text-steel mt-1">
                                Siswa dapat mengakses portal peminjaman menggunakan NIS dan No HP.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================== KOLOM KANAN: Form ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-info-circle text-primary"></i>
                    Informasi Siswa
                </h3>

                <div class="space-y-5">

                    {{-- NIS --}}
                    <div>
                        <label for="nis" class="form-label">
                            NIS <span class="text-critical">*</span>
                        </label>
                        <input id="nis" 
                               type="text" 
                               name="nis" 
                               value="{{ old('nis') }}"
                               placeholder="Contoh: 2024001"
                               required
                               oninput="updatePreview()"
                               maxlength="20"
                               class="form-input font-mono @error('nis') form-input-error @enderror">
                        <p class="text-caption text-steel mt-1">
                            NIS harus unik, tidak boleh sama dengan siswa lain.
                        </p>
                        @error('nis')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="form-label">
                            Nama Lengkap <span class="text-critical">*</span>
                        </label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="Contoh: Ahmad Fauzi"
                               required
                               oninput="updatePreview()"
                               maxlength="100"
                               class="form-input @error('name') form-input-error @enderror">
                        @error('name')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Kelas + Phone --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="class" class="form-label">
                                Kelas <span class="text-critical">*</span>
                            </label>
                            <input id="class" 
                                   type="text" 
                                   name="class" 
                                   value="{{ old('class') }}"
                                   placeholder="Contoh: X IPA 1"
                                   required
                                   oninput="updatePreview()"
                                   maxlength="20"
                                   list="class-list"
                                   class="form-input @error('class') form-input-error @enderror">
                            <datalist id="class-list">
                                <option value="X IPA 1">
                                <option value="X IPA 2">
                                <option value="X IPS 1">
                                <option value="X IPS 2">
                                <option value="XI IPA 1">
                                <option value="XI IPA 2">
                                <option value="XI IPS 1">
                                <option value="XI IPS 2">
                                <option value="XII IPA 1">
                                <option value="XII IPA 2">
                                <option value="XII IPS 1">
                                <option value="XII IPS 2">
                            </datalist>
                            @error('class')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="form-label">
                                No HP
                                <span class="text-caption text-steel font-normal ml-1">(opsional)</span>
                            </label>
                            <input id="phone" 
                                   type="text" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   placeholder="Contoh: 081234567890"
                                   oninput="updatePreview()"
                                   maxlength="20"
                                   class="form-input @error('phone') form-input-error @enderror">
                            <p class="text-caption text-steel mt-1">
                                Untuk verifikasi di portal.
                            </p>
                            @error('phone')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Status Aktif --}}
                    <div>
                        <label class="form-label">Status</label>
                        <label class="flex items-start gap-3 p-4 rounded-xl border border-hairline-soft hover:bg-surface-soft/50 cursor-pointer transition-colors">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   @checked(old('is_active', true))
                                   class="mt-0.5 w-5 h-5 rounded border-hairline-soft text-primary focus:ring-primary">
                            <div class="flex-1">
                                <p class="text-body-sm-bold text-ink-deep">Aktif</p>
                                <p class="text-caption text-steel mt-0.5">
                                    Siswa aktif dapat mengakses portal peminjaman.
                                </p>
                            </div>
                        </label>
                        @error('is_active')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ============ ACTION ============ --}}
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('students.index') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Siswa
                </button>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    function updatePreview() {
        const name = document.getElementById('name').value || 'Nama Siswa';
        const nis = document.getElementById('nis').value || '-';
        const kelas = document.getElementById('class').value || 'Kelas';
        const phone = document.getElementById('phone').value || '-';

        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-nis').textContent = 'NIS: ' + nis;
        document.getElementById('preview-class').textContent = kelas;
        document.getElementById('preview-phone').innerHTML = '<i class="fas fa-phone text-xs"></i> ' + phone;
    }

    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush