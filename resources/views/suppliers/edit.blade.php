{{-- resources/views/suppliers/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Supplier')
@section('page_title', 'Edit Supplier')
@section('page_subtitle', 'Perbarui informasi supplier')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('suppliers.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-truck text-xs"></i> Supplier / Vendor
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <a href="{{ route('suppliers.show', $supplier->id) }}" class="hover:text-primary">
        {{ $supplier->name }}
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Edit</span>
</nav>

<form method="POST" action="{{ route('suppliers.update', $supplier->id) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card text-center sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4 text-left">Preview</h3>

                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 rounded-2xl bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-truck text-primary text-4xl"></i>
                    </div>
                </div>

                <h4 id="preview-name" class="text-body-md-bold text-ink-deep mb-2">
                    {{ $supplier->name }}
                </h4>

                <p id="preview-pic" class="text-body-sm text-steel mb-3">
                    {{ $supplier->contact_person ?? 'Contact Person' }}
                </p>

                <div class="space-y-1 mb-4">
                    <p id="preview-phone" class="text-caption text-steel">
                        {{ $supplier->phone ?? 'Telepon' }}
                    </p>
                    <p id="preview-email" class="text-caption text-steel truncate">
                        {{ $supplier->email ?? 'email@domain.com' }}
                    </p>
                </div>

                {{-- Info Box --}}
                <div class="pt-4 mt-4 border-t border-hairline-soft space-y-3 text-left">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-boxes text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Jumlah Barang</p>
                            <p class="text-body-sm-bold text-ink-deep">
                                {{ $supplier->items_count ?? $supplier->items()->count() }} barang
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Warning Kalau Punya Barang --}}
                @php $itemCount = $supplier->items()->count(); @endphp
                @if($itemCount > 0)
                    <div class="mt-4 p-3 rounded-xl bg-warning/5 border border-warning/20 text-left">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle text-warning mt-0.5 text-sm"></i>
                            <div>
                                <p class="text-caption-bold text-ink-deep">Perhatian</p>
                                <p class="text-caption text-steel mt-1">
                                    Supplier ini terkait dengan <strong>{{ $itemCount }} barang</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- ==================== KOLOM KANAN ==================== --}}
        <div class="lg:col-span-2 space-y-6">

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
                               value="{{ old('name', $supplier->name) }}"
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

                    {{-- PIC --}}
                    <div>
                        <label for="contact_person" class="form-label">Contact Person (PIC)</label>
                        <input id="contact_person" 
                               type="text" 
                               name="contact_person" 
                               value="{{ old('contact_person', $supplier->contact_person) }}"
                               oninput="updatePreview()"
                               maxlength="100"
                               class="form-input @error('contact_person') form-input-error @enderror">
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
                                   value="{{ old('phone', $supplier->phone) }}"
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
                                   value="{{ old('email', $supplier->email) }}"
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
                                  oninput="updatePreview()"
                                  maxlength="500"
                                  class="form-input !h-auto resize-none @error('address') form-input-error @enderror">{{ old('address', $supplier->address) }}</textarea>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-caption text-steel">
                                Opsional, maksimal 500 karakter.
                            </p>
                            <p class="text-caption text-steel">
                                <span id="char-count">{{ strlen(old('address', $supplier->address ?? '')) }}</span>/500
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
            <div class="flex flex-wrap items-center justify-between gap-3">
                <button type="button" 
                        onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="btn-danger">
                    <i class="fas fa-trash"></i>
                    Hapus Supplier
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('suppliers.index') }}" class="btn-ghost">
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
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus Supplier?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Supplier <strong class="text-ink-deep">{{ $supplier->name }}</strong> akan dihapus.
        </p>
        <form method="POST" action="{{ route('suppliers.destroy', $supplier->id) }}" class="flex gap-3">
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
</script>
@endpush