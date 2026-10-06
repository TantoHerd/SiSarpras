{{-- resources/views/funding-sources/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Sumber Dana')
@section('page_title', 'Edit Sumber Dana')
@section('page_subtitle', 'Perbarui informasi sumber dana')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('funding-sources.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-money-bill-wave text-xs"></i> Sumber Dana
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">{{ $fundingSource->code }}</span>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Edit</span>
</nav>

<form method="POST" action="{{ route('funding-sources.update', $fundingSource->id) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: PREVIEW ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card text-center sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4 text-left">Preview</h3>

                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 rounded-2xl bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-primary text-4xl"></i>
                    </div>
                </div>

                <div class="mb-3">
                    <span id="preview-code" class="badge-info font-mono text-sm">
                        {{ $fundingSource->code }}
                    </span>
                </div>

                <h4 id="preview-name" class="text-body-md-bold text-ink-deep mb-2">
                    {{ $fundingSource->name }}
                </h4>

                <p id="preview-description" class="text-body-sm text-steel mb-3 line-clamp-2">
                    {{ $fundingSource->description ?? 'Belum ada deskripsi' }}
                </p>

                {{-- Status Badge --}}
                <div class="mb-4">
                    <span id="preview-status" class="{{ $fundingSource->is_active ? 'badge-success' : 'badge-neutral' }}">
                        {{ $fundingSource->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
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
                                {{ $itemCount }} barang
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Warning Kalau Dipakai Item --}}
                @if($itemCount > 0)
                    <div class="mt-4 p-3 rounded-xl bg-warning/5 border border-warning/20 text-left">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle text-warning mt-0.5 text-sm"></i>
                            <div>
                                <p class="text-caption-bold text-ink-deep">Perhatian</p>
                                <p class="text-caption text-steel mt-1">
                                    Sumber dana ini digunakan oleh <strong>{{ $itemCount }} barang</strong>.
                                    Tidak dapat dihapus selama masih dipakai.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- ==================== KOLOM KANAN: FORM ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-info-circle text-primary"></i>
                    Informasi Sumber Dana
                </h3>

                <div class="space-y-5">

                    {{-- Kode --}}
                    <div>
                        <label for="code" class="form-label">
                            Kode <span class="text-critical">*</span>
                        </label>
                        <input id="code" 
                               type="text" 
                               name="code" 
                               value="{{ old('code', $fundingSource->code) }}"
                               required
                               oninput="updatePreview()"
                               maxlength="20"
                               placeholder="Contoh: BOS"
                               class="form-input font-mono uppercase @error('code') form-input-error @enderror"
                               style="text-transform: uppercase;">
                        <p class="mt-1 text-caption text-steel">
                            Hanya huruf kapital, angka, dan tanda hubung (-). Maks 20 karakter.
                        </p>
                        @error('code')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="form-label">
                            Nama Sumber Dana <span class="text-critical">*</span>
                        </label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name', $fundingSource->name) }}"
                               required
                               oninput="updatePreview()"
                               maxlength="100"
                               placeholder="Contoh: Bantuan Operasional Sekolah"
                               class="form-input @error('name') form-input-error @enderror">
                        @error('name')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  oninput="updatePreview()"
                                  maxlength="500"
                                  placeholder="Keterangan tambahan tentang sumber dana ini..."
                                  class="form-input !h-auto resize-none @error('description') form-input-error @enderror">{{ old('description', $fundingSource->description) }}</textarea>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-caption text-steel">
                                Opsional, maksimal 500 karakter.
                            </p>
                            <p class="text-caption text-steel">
                                <span id="char-count">{{ strlen(old('description', $fundingSource->description ?? '')) }}</span>/500
                            </p>
                        </div>
                        @error('description')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Status Aktif --}}
                    <div>
                        <label class="form-label">Status</label>
                        <label class="flex items-start gap-3 p-4 rounded-xl border border-hairline-soft hover:bg-surface-soft/50 cursor-pointer transition-colors">
                            <input type="hidden" name="is_active" value="0">
                            <input id="is_active" 
                                   type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   @checked(old('is_active', $fundingSource->is_active))
                                   onchange="updatePreview()"
                                   class="mt-0.5 w-5 h-5 rounded border-hairline-soft text-primary focus:ring-primary">
                            <div class="flex-1">
                                <p class="text-body-sm-bold text-ink-deep">Aktif</p>
                                <p class="text-caption text-steel mt-0.5">
                                    Nonaktifkan jika sumber dana ini sudah tidak dipakai. Data historis barang tetap aman.
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
                        onclick="openDeleteModal()"
                        class="btn-danger">
                    <i class="fas fa-trash"></i>
                    Hapus Sumber Dana
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('funding-sources.index') }}" class="btn-ghost">
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
        <div id="delete-icon-bg" class="flex items-center justify-center w-14 h-14 rounded-circle mx-auto mb-4">
            <i id="delete-icon" class="text-2xl"></i>
        </div>
        <h3 id="delete-title" class="text-heading-sm text-ink-deep text-center mb-2">Hapus Sumber Dana?</h3>
        <p id="delete-message" class="text-body-sm text-steel text-center mb-6"></p>

        <form id="delete-form" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="btn-ghost flex-1">
                Batal
            </button>
            <button id="delete-submit-btn" type="submit" class="flex-1">
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ==================== LIVE PREVIEW ====================
    function updatePreview() {
        const code = document.getElementById('code').value || 'KODE';
        const name = document.getElementById('name').value || 'Nama Sumber Dana';
        const description = document.getElementById('description').value || 'Belum ada deskripsi';
        const isActive = document.getElementById('is_active').checked;

        document.getElementById('preview-code').textContent = code.toUpperCase();
        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-description').textContent = description;
        document.getElementById('char-count').textContent = document.getElementById('description').value.length;

        const statusEl = document.getElementById('preview-status');
        if (isActive) {
            statusEl.className = 'badge-success';
            statusEl.textContent = 'Aktif';
        } else {
            statusEl.className = 'badge-neutral';
            statusEl.textContent = 'Nonaktif';
        }
    }

    // ==================== DELETE MODAL (dinamis) ====================
    function openDeleteModal() {
        const itemCount = {{ $itemCount }};
        const name = @json($fundingSource->name);
        const id = {{ $fundingSource->id }};

        const iconBg = document.getElementById('delete-icon-bg');
        const icon = document.getElementById('delete-icon');
        const title = document.getElementById('delete-title');
        const message = document.getElementById('delete-message');
        const submitBtn = document.getElementById('delete-submit-btn');
        const form = document.getElementById('delete-form');

        if (itemCount > 0) {
            iconBg.className = 'flex items-center justify-center w-14 h-14 rounded-circle bg-warning/10 mx-auto mb-4';
            icon.className = 'fas fa-exclamation-triangle text-warning text-2xl';
            title.textContent = 'Tidak Dapat Dihapus';
            message.innerHTML = `Sumber dana <strong class="text-ink-deep">${name}</strong> masih digunakan oleh <strong class="text-critical">${itemCount} barang</strong>. Nonaktifkan saja atau ubah sumber dana barang terlebih dahulu.`;

            submitBtn.className = 'btn-ghost flex-1';
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Mengerti';
            submitBtn.type = 'button';
            submitBtn.onclick = () => document.getElementById('delete-modal').classList.add('hidden');
            form.action = '#';
        } else {
            iconBg.className = 'flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4';
            icon.className = 'fas fa-exclamation-triangle text-critical text-2xl';
            title.textContent = 'Hapus Sumber Dana?';
            message.innerHTML = `Sumber dana <strong class="text-ink-deep">${name}</strong> akan dihapus dari sistem.`;

            submitBtn.className = 'btn-danger flex-1';
            submitBtn.innerHTML = '<i class="fas fa-trash"></i> Ya, Hapus';
            submitBtn.type = 'submit';
            submitBtn.onclick = null;
            form.action = `/funding-sources/${id}`;
        }

        document.getElementById('delete-modal').classList.remove('hidden');
    }
</script>
@endpush