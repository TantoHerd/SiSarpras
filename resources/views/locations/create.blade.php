{{-- resources/views/locations/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Lokasi')
@section('page_title', 'Tambah Lokasi')
@section('page_subtitle', 'Buat lokasi penyimpanan barang baru')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('locations.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-map-marker-alt text-xs"></i> Lokasi Barang
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Tambah Lokasi</span>
</nav>

<form method="POST" action="{{ route('locations.store') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Preview ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card text-center sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4 text-left">Preview</h3>

                {{-- Icon Preview --}}
                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 rounded-2xl bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-primary text-4xl"></i>
                    </div>
                </div>

                {{-- Name Preview --}}
                <h4 id="preview-name" class="text-body-md-bold text-ink-deep mb-2">
                    Nama Lokasi
                </h4>

                {{-- Floor Preview --}}
                <p id="preview-floor" class="text-body-sm text-steel mb-3">
                    Lantai / Gedung
                </p>

                {{-- Description Preview --}}
                <p id="preview-description" class="text-body-sm text-steel mb-4 line-clamp-3 px-2">
                    Deskripsi lokasi akan muncul di sini.
                </p>

                {{-- Parent Info --}}
                <div id="preview-parent" class="hidden pt-4 mt-4 border-t border-hairline-soft">
                    <div class="flex items-center justify-center gap-2 text-caption text-steel">
                        <i class="fas fa-sitemap text-xs"></i>
                        <span>Sub-lokasi dari <strong id="parent-name" class="text-ink-deep">-</strong></span>
                    </div>
                </div>

                {{-- Info Box --}}
                <div class="pt-4 mt-4 border-t border-hairline-soft p-3 rounded-xl bg-primary/5 border border-primary/20 text-left">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-primary mt-0.5 text-sm"></i>
                        <div>
                            <p class="text-caption-bold text-ink-deep">Tips</p>
                            <p class="text-caption text-steel mt-1">
                                Untuk hierarki, gunakan parent. Contoh: Gedung A (parent) → Lantai 1 (child).
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
                    Informasi Lokasi
                </h3>

                <div class="space-y-5">

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="form-label">
                            Nama Lokasi <span class="text-critical">*</span>
                        </label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="Contoh: Ruang Kelas 1A, Lab Komputer"
                               required
                               oninput="updatePreview()"
                               maxlength="100"
                               class="form-input @error('name') form-input-error @enderror">
                        <p class="text-caption text-steel mt-1">
                            Nama harus unik, tidak boleh sama dengan lokasi lain.
                        </p>
                        @error('name')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Floor --}}
                    <div>
                        <label for="floor" class="form-label">
                            Lantai / Gedung
                        </label>
                        <input id="floor" 
                               type="text" 
                               name="floor" 
                               value="{{ old('floor') }}"
                               placeholder="Contoh: Lantai 1, Gedung A"
                               oninput="updatePreview()"
                               maxlength="50"
                               class="form-input @error('floor') form-input-error @enderror">
                        <p class="text-caption text-steel mt-1">
                            Opsional. Gunakan format konsisten, misal "Lantai 1" atau "Gedung A".
                        </p>
                        @error('floor')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Parent --}}
                    <div>
                        <label for="parent_id" class="form-label">
                            Lokasi Induk (Parent)
                        </label>
                        <select id="parent_id" 
                                name="parent_id" 
                                onchange="updatePreview()"
                                class="form-input @error('parent_id') form-input-error @enderror">
                            <option value="">-- Tidak Ada (Lokasi Utama) --</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" 
                                        data-name="{{ $parent->name }}"
                                        @selected(old('parent_id') == $parent->id)>
                                    {{ $parent->name }}
                                    @if($parent->floor) ({{ $parent->floor }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-caption text-steel mt-1">
                            Opsional. Pilih parent jika lokasi ini adalah bagian dari lokasi lain.
                        </p>
                        @error('parent_id')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="description" class="form-label">
                            Deskripsi
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="Deskripsi singkat tentang lokasi ini..."
                                  oninput="updatePreview()"
                                  maxlength="500"
                                  class="form-input !h-auto resize-none @error('description') form-input-error @enderror">{{ old('description') }}</textarea>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-caption text-steel">
                                Opsional, maksimal 500 karakter.
                            </p>
                            <p class="text-caption text-steel">
                                <span id="char-count">0</span>/500
                            </p>
                        </div>
                        @error('description')
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
                <a href="{{ route('locations.index') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Lokasi
                </button>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    function updatePreview() {
        const name = document.getElementById('name').value || 'Nama Lokasi';
        const floor = document.getElementById('floor').value || 'Lantai / Gedung';
        const description = document.getElementById('description').value || 'Deskripsi lokasi akan muncul di sini.';
        const parentSelect = document.getElementById('parent_id');
        const parentOption = parentSelect.options[parentSelect.selectedIndex];
        const parentName = parentOption?.dataset?.name || '';

        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-floor').textContent = floor;
        document.getElementById('preview-description').textContent = description;

        // Parent Info
        const parentInfo = document.getElementById('preview-parent');
        if (parentName) {
            document.getElementById('parent-name').textContent = parentName;
            parentInfo.classList.remove('hidden');
        } else {
            parentInfo.classList.add('hidden');
        }

        // Char count
        document.getElementById('char-count').textContent = document.getElementById('description').value.length;
    }

    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush