{{-- resources/views/students/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Siswa')
@section('page_title', 'Edit Siswa')
@section('page_subtitle', 'Perbarui data siswa')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('students.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-user-graduate text-xs"></i> Data Siswa
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <a href="{{ route('students.show', $student->id) }}" class="hover:text-primary">
        {{ $student->name }}
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Edit</span>
</nav>

<form method="POST" action="{{ route('students.update', $student->id) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Preview ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card text-center sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4 text-left">Preview</h3>

                {{-- Avatar --}}
                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 rounded-2xl bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-user-graduate text-primary text-4xl"></i>
                    </div>
                </div>

                <h4 id="preview-name" class="text-body-md-bold text-ink-deep mb-2">
                    {{ $student->name }}
                </h4>

                <p id="preview-nis" class="text-caption text-steel font-mono mb-2">
                    NIS: {{ $student->nis }}
                </p>

                <div class="mb-4">
                    <span id="preview-class" class="badge-info">{{ $student->class }}</span>
                </div>

                <p id="preview-phone" class="text-body-sm text-steel mb-4">
                    <i class="fas fa-phone text-xs"></i> {{ $student->phone ?? '-' }}
                </p>

                {{-- Info Box --}}
                <div class="pt-4 mt-4 border-t border-hairline-soft space-y-3 text-left">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Terdaftar</p>
                            <p class="text-body-sm-bold text-ink-deep">
                                {{ $student->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Terakhir Diubah</p>
                            <p class="text-body-sm-bold text-ink-deep">
                                {{ $student->updated_at->diffForHumans() }}
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
                               value="{{ old('nis', $student->nis) }}"
                               required
                               oninput="updatePreview()"
                               maxlength="20"
                               class="form-input font-mono @error('nis') form-input-error @enderror">
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
                               value="{{ old('name', $student->name) }}"
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
                                   value="{{ old('class', $student->class) }}"
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
                                   value="{{ old('phone', $student->phone) }}"
                                   oninput="updatePreview()"
                                   maxlength="20"
                                   class="form-input @error('phone') form-input-error @enderror">
                            @error('phone')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="form-label">Status</label>
                        <label class="flex items-start gap-3 p-4 rounded-xl border border-hairline-soft hover:bg-surface-soft/50 cursor-pointer transition-colors">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   @checked(old('is_active', $student->is_active))
                                   class="mt-0.5 w-5 h-5 rounded border-hairline-soft text-primary focus:ring-primary">
                            <div class="flex-1">
                                <p class="text-body-sm-bold text-ink-deep">Aktif</p>
                                <p class="text-caption text-steel mt-0.5">
                                    Nonaktifkan jika siswa sudah lulus / pindah / tidak aktif.
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
            <div class="flex flex-wrap items-center justify-between gap-3">
                <button type="button" 
                        onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="btn-danger">
                    <i class="fas fa-trash"></i>
                    Hapus Siswa
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('students.index') }}" class="btn-ghost">
                        <i class="fas fa-arrow-left"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>

{{-- ==================== MODAL DELETE ==================== --}}
<div id="delete-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus Siswa?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Siswa <strong class="text-ink-deep">{{ $student->name }}</strong> akan dihapus dari sistem.
        </p>
        <form method="POST" action="{{ route('students.destroy', $student->id) }}" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-danger flex-1">
                <i class="fas fa-trash"></i> Ya, Hapus
            </button>
        </form>
    </div>
</div>

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