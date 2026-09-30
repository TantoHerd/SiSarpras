{{-- resources/views/locations/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Lokasi')
@section('page_title', 'Edit Lokasi')
@section('page_subtitle', 'Perbarui informasi lokasi')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('locations.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-map-marker-alt text-xs"></i> Lokasi Barang
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <a href="{{ route('locations.show', $location->id) }}" class="hover:text-primary">
        {{ $location->name }}
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Edit</span>
</nav>

<form method="POST" action="{{ route('locations.update', $location->id) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI ==================== --}}
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
                    {{ $location->name }}
                </h4>

                {{-- Floor Preview --}}
                <p id="preview-floor" class="text-body-sm text-steel mb-3">
                    {{ $location->floor ?? 'Lantai / Gedung' }}
                </p>

                {{-- Description Preview --}}
                <p id="preview-description" class="text-body-sm text-steel mb-4 line-clamp-3 px-2">
                    {{ $location->description ?? 'Deskripsi lokasi akan muncul di sini.' }}
                </p>

                {{-- Parent Info --}}
                <div id="preview-parent" class="{{ $location->parent ? '' : 'hidden' }} pt-4 mt-4 border-t border-hairline-soft">
                    <div class="flex items-center justify-center gap-2 text-caption text-steel">
                        <i class="fas fa-sitemap text-xs"></i>
                        <span>Sub-lokasi dari <strong id="parent-name" class="text-ink-deep">{{ $location->parent->name ?? '-' }}</strong></span>
                    </div>
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
                                {{ $location->items_count ?? $location->items()->count() }} barang
                            </p>
                        </div>
                    </div>
                    @php $childCount = $location->children()->count(); @endphp
                    @if($childCount > 0)
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-sitemap text-steel text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-caption text-steel">Sub-Lokasi</p>
                                <p class="text-body-sm-bold text-ink-deep">
                                    {{ $childCount }} lokasi
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Warning Kalau Punya Barang --}}
                @php $itemCount = $location->items()->count(); @endphp
                @if($itemCount > 0)
                    <div class="mt-4 p-3 rounded-xl bg-warning/5 border border-warning/20 text-left">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle text-warning mt-0.5 text-sm"></i>
                            <div>
                                <p class="text-caption-bold text-ink-deep">Perhatian</p>
                                <p class="text-caption text-steel mt-1">
                                    Lokasi ini digunakan oleh <strong>{{ $itemCount }} barang</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- ==================== KOLOM KANAN ==================== --}}
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
                               value="{{ old('name', $location->name) }}"
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

                    {{-- Floor --}}
                    <div>
                        <label for="floor" class="form-label">
                            Lantai / Gedung
                        </label>
                        <input id="floor" 
                               type="text" 
                               name="floor" 
                               value="{{ old('floor', $location->floor) }}"
                               oninput="updatePreview()"
                               maxlength="50"
                               class="form-input @error('floor') form-input-error @enderror">
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
                                        @selected(old('parent_id', $location->parent_id) == $parent->id)>
                                    {{ $parent->name }}
                                    @if($parent->floor) ({{ $parent->floor }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-caption text-steel mt-1">
                            Pilih parent jika lokasi ini adalah bagian dari lokasi lain. Kosongkan untuk jadikan lokasi utama.
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
                                  oninput="updatePreview()"
                                  maxlength="500"
                                  class="form-input !h-auto resize-none @error('description') form-input-error @enderror">{{ old('description', $location->description) }}</textarea>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-caption text-steel">
                                Opsional, maksimal 500 karakter.
                            </p>
                            <p class="text-caption text-steel">
                                <span id="char-count">{{ strlen(old('description', $location->description ?? '')) }}</span>/500
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
            <div class="flex flex-wrap items-center justify-between gap-3">
                {{-- Delete --}}
                <button type="button" 
                        onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="btn-danger">
                    <i class="fas fa-trash"></i>
                    Hapus Lokasi
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('locations.index') }}" class="btn-ghost">
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
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus Lokasi?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Lokasi <strong class="text-ink-deep">{{ $location->name }}</strong> akan dihapus.
        </p>
        <form method="POST" action="{{ route('locations.destroy', $location->id) }}" class="flex gap-3">
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
        const name = document.getElementById('name').value || 'Nama Lokasi';
        const floor = document.getElementById('floor').value || 'Lantai / Gedung';
        const description = document.getElementById('description').value || 'Deskripsi lokasi akan muncul di sini.';
        const parentSelect = document.getElementById('parent_id');
        const parentOption = parentSelect.options[parentSelect.selectedIndex];
        const parentName = parentOption?.dataset?.name || '';

        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-floor').textContent = floor;
        document.getElementById('preview-description').textContent = description;

        const parentInfo = document.getElementById('preview-parent');
        if (parentName) {
            document.getElementById('parent-name').textContent = parentName;
            parentInfo.classList.remove('hidden');
        } else {
            parentInfo.classList.add('hidden');
        }

        document.getElementById('char-count').textContent = document.getElementById('description').value.length;
    }
</script>
@endpush