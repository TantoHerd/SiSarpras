{{-- resources/views/suppliers/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Supplier')
@section('page_title', 'Tambah Supplier')
@section('page_subtitle', 'Buat data supplier/vendor baru')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('suppliers.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-truck text-xs"></i> Supplier / Vendor
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Tambah Supplier</span>
</nav>

<form method="POST" action="{{ route('suppliers.store') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Preview ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card text-center sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4 text-left">Preview</h3>

                {{-- Icon Preview --}}
                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 rounded-2xl bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-truck text-primary text-4xl"></i>
                    </div>
                </div>

                {{-- Name Preview --}}
                <h4 id="preview-name" class="text-body-md-bold text-ink-deep mb-2">
                    Nama Supplier
                </h4>

                {{-- Contact Person Preview --}}
                <p id="preview-pic" class="text-body-sm text-steel mb-3">
                    Contact Person
                </p>

                {{-- Kontak Preview --}}
                <div class="space-y-1 mb-4">
                    <p id="preview-phone" class="text-caption text-steel">
                        Telepon
                    </p>
                    <p id="preview-email" class="text-caption text-steel truncate">
                        email@domain.com
                    </p>
                </div>

                {{-- Info Box --}}
                <div class="pt-4 mt-4 border-t border-hairline-soft p-3 rounded-xl bg-primary/5 border border-primary/20 text-left">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-primary mt-0.5 text-sm"></i>
                        <div>
                            <p class="text-caption-bold text-ink-deep">Tips</p>
                            <p class="text-caption text-steel mt-1">
                                Isi <strong>Contact Person</strong> agar mudah menghubungi PIC saat ada masalah barang.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================== KOLOM KANAN: Form ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ============ FORM INFO ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-info-circle text-primary"></i>
                    Informasi Supplier
                </h3>

                <div class="space-y-5">

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="form-label">
                            Nama Supplier <span class="text-critical">*</span>
                        </label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="Contoh: PT. Elektronik Jaya"
                               required
                               oninput="updatePreview()"
                               maxlength="100"
                               class="form-input @error('name') form-input-error @enderror">
                        <p class="text-caption text-steel mt-1">
                            Nama supplier/vendor harus unik.
                        </p>
                        @error('name')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Contact Person --}}
                    <div>
                        <label for="contact_person" class="form-label">
                            Contact Person (PIC)
                        </label>
                        <input id="contact_person" 
                               type="text" 
                               name="contact_person" 
                               value="{{ old('contact_person') }}"
                               placeholder="Contoh: Bpk. Andi"
                               oninput="updatePreview()"
                               maxlength="100"
                               class="form-input @error('contact_person') form-input-error @enderror">
                        <p class="text-caption text-steel mt-1">
                            Nama orang yang bisa dihubungi dari pihak supplier.
                        </p>
                        @error('contact_person')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Phone + Email --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="phone" class="form-label">Telepon</label>
                            <input id="phone" 
                                   type="text" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   placeholder="021-5555001"
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

                        <div>
                            <label for="email" class="form-label">Email</label>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   placeholder="sales@supplier.com"
                                   oninput="updatePreview()"
                                   maxlength="100"
                                   class="form-input @error('email') form-input-error @enderror">
                            @error('email')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label for="address" class="form-label">Alamat</label>
                        <textarea id="address" 
                                  name="address" 
                                  rows="3"
                                  placeholder="Alamat lengkap supplier..."
                                  oninput="updatePreview()"
                                  maxlength="500"
                                  class="form-input !h-auto resize-none @error('address') form-input-error @enderror">{{ old('address') }}</textarea>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-caption text-steel">
                                Opsional, maksimal 500 karakter.
                            </p>
                            <p class="text-caption text-steel">
                                <span id="char-count">0</span>/500
                            </p>
                        </div>
                        @error('address')
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
                <a href="{{ route('suppliers.index') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Supplier
                </button>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    function updatePreview() {
        const name = document.getElementById('name').value || 'Nama Supplier';
        const pic = document.getElementById('contact_person').value || 'Contact Person';
        const phone = document.getElementById('phone').value || 'Telepon';
        const email = document.getElementById('email').value || 'email@domain.com';

        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-pic').textContent = pic;
        document.getElementById('preview-phone').textContent = phone;
        document.getElementById('preview-email').textContent = email;
        document.getElementById('char-count').textContent = document.getElementById('address').value.length;
    }

    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush