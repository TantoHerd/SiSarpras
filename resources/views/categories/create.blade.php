{{-- resources/views/categories/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('page_title', 'Tambah Kategori')
@section('page_subtitle', 'Buat kategori baru untuk klasifikasi barang')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('categories.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-tags text-xs"></i> Kategori Barang
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Tambah Kategori</span>
</nav>

<form method="POST" action="{{ route('categories.store') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Preview ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card text-center sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4 text-left">Preview</h3>

                {{-- Icon Preview --}}
                <div class="flex justify-center mb-4">
                    <div id="icon-preview" 
                         class="w-24 h-24 rounded-2xl bg-primary/10 flex items-center justify-center">
                        <i id="icon-preview-i" class="fas fa-boxes text-primary text-4xl"></i>
                    </div>
                </div>

                {{-- Name Preview --}}
                <h4 id="preview-name" class="text-body-md-bold text-ink-deep mb-2">
                    Nama Kategori
                </h4>

                {{-- Description Preview --}}
                <p id="preview-description" class="text-body-sm text-steel mb-4 line-clamp-3 px-2">
                    Deskripsi kategori akan muncul di sini.
                </p>

                {{-- Category Count Info --}}
                <div class="pt-4 mt-4 border-t border-hairline-soft">
                    <div class="flex items-center justify-center gap-2 text-caption text-steel">
                        <i class="fas fa-info-circle text-xs"></i>
                        <span>Kategori baru akan kosong (0 barang)</span>
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
                    Informasi Kategori
                </h3>

                <div class="space-y-5">

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="form-label">
                            Nama Kategori <span class="text-critical">*</span>
                        </label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="Contoh: Elektronik, Mebel, ATK"
                               required
                               oninput="updatePreview()"
                               maxlength="100"
                               class="form-input @error('name') form-input-error @enderror">
                        <p class="text-caption text-steel mt-1">
                            Nama harus unik, tidak boleh sama dengan kategori lain.
                        </p>
                        @error('name')
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
                                  placeholder="Deskripsi singkat tentang kategori ini..."
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

            {{-- ============ ICON PICKER ============ --}}
            <div class="card">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-body-md-bold text-ink-deep flex items-center gap-2">
                        <i class="fas fa-icons text-primary"></i>
                        Pilih Icon
                    </h3>
                    <p class="text-caption text-steel">
                        Klik untuk memilih
                    </p>
                </div>

                <input type="hidden" id="icon" name="icon" value="{{ old('icon', 'fa-boxes') }}">

                @php
                    // Daftar icon Font Awesome yang umum untuk kategori barang
                    $iconOptions = [
                        // Elektronik & Digital
                        ['class' => 'fa-laptop', 'label' => 'Laptop'],
                        ['class' => 'fa-desktop', 'label' => 'Komputer'],
                        ['class' => 'fa-mobile-alt', 'label' => 'Mobile'],
                        ['class' => 'fa-print', 'label' => 'Printer'],
                        ['class' => 'fa-keyboard', 'label' => 'Keyboard'],
                        ['class' => 'fa-mouse', 'label' => 'Mouse'],
                        ['class' => 'fa-tv', 'label' => 'TV'],
                        ['class' => 'fa-camera', 'label' => 'Kamera'],
                        
                        // Audio Visual
                        ['class' => 'fa-video', 'label' => 'Video'],
                        ['class' => 'fa-microphone', 'label' => 'Audio'],
                        ['class' => 'fa-headphones', 'label' => 'Headphone'],
                        ['class' => 'fa-volume-up', 'label' => 'Speaker'],
                        
                        // Mebel & Furnitur
                        ['class' => 'fa-chair', 'label' => 'Kursi'],
                        ['class' => 'fa-couch', 'label' => 'Sofa'],
                        ['class' => 'fa-bed', 'label' => 'Tempat Tidur'],
                        ['class' => 'fa-table', 'label' => 'Meja'],
                        
                        // Laboratorium
                        ['class' => 'fa-flask', 'label' => 'Lab'],
                        ['class' => 'fa-microscope', 'label' => 'Mikroskop'],
                        ['class' => 'fa-vial', 'label' => 'Tabung'],
                        ['class' => 'fa-dna', 'label' => 'Biologi'],
                        
                        // Olahraga
                        ['class' => 'fa-futbol', 'label' => 'Sepakbola'],
                        ['class' => 'fa-basketball-ball', 'label' => 'Basket'],
                        ['class' => 'fa-volleyball-ball', 'label' => 'Voli'],
                        ['class' => 'fa-running', 'label' => 'Atletik'],
                        
                        // ATK
                        ['class' => 'fa-pen', 'label' => 'Pulpen'],
                        ['class' => 'fa-pencil-alt', 'label' => 'Pensil'],
                        ['class' => 'fa-book', 'label' => 'Buku'],
                        ['class' => 'fa-file-alt', 'label' => 'Kertas'],
                        ['class' => 'fa-clipboard', 'label' => 'Papan'],
                        
                        // Bangunan & Umum
                        ['class' => 'fa-building', 'label' => 'Bangunan'],
                        ['class' => 'fa-tools', 'label' => 'Peralatan'],
                        ['class' => 'fa-boxes', 'label' => 'Umum'],
                        ['class' => 'fa-box', 'label' => 'Kotak'],
                        ['class' => 'fa-archive', 'label' => 'Arsip'],
                        ['class' => 'fa-utensils', 'label' => 'Kantin'],
                        ['class' => 'fa-music', 'label' => 'Musik'],
                    ];
                @endphp

                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2" id="icon-picker">
                    @foreach($iconOptions as $icon)
                        <button type="button"
                                data-icon="{{ $icon['class'] }}"
                                title="{{ $icon['label'] }}"
                                onclick="selectIcon('{{ $icon['class'] }}')"
                                class="icon-option aspect-square rounded-xl border-2 border-hairline-soft bg-canvas
                                       hover:border-primary hover:bg-primary/5 transition-all
                                       flex items-center justify-center group
                                       {{ old('icon', 'fa-boxes') === $icon['class'] ? 'border-primary bg-primary/5' : '' }}">
                            <i class="fas {{ $icon['class'] }} text-steel group-hover:text-primary text-lg transition-colors
                                      {{ old('icon', 'fa-boxes') === $icon['class'] ? 'text-primary' : '' }}"></i>
                        </button>
                    @endforeach
                </div>

                @error('icon')
                    <p class="mt-3 text-body-sm text-critical-strong flex items-center gap-1.5">
                        <i class="fas fa-exclamation-circle text-xs"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- ============ ACTION ============ --}}
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('categories.index') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Kategori
                </button>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    const currentIcon = '{{ old('icon', 'fa-boxes') }}';

    function selectIcon(iconClass) {
        // Update hidden input
        document.getElementById('icon').value = iconClass;

        // Update highlight semua icon-option
        document.querySelectorAll('.icon-option').forEach(btn => {
            btn.classList.remove('border-primary', 'bg-primary/5');
            btn.classList.add('border-hairline-soft');
            
            const icon = btn.querySelector('i');
            icon.classList.remove('text-primary');
            icon.classList.add('text-steel');
        });

        // Highlight yang dipilih
        const selected = document.querySelector(`.icon-option[data-icon="${iconClass}"]`);
        if (selected) {
            selected.classList.remove('border-hairline-soft');
            selected.classList.add('border-primary', 'bg-primary/5');
            
            const icon = selected.querySelector('i');
            icon.classList.remove('text-steel');
            icon.classList.add('text-primary');
        }

        updatePreview();
    }

    function updatePreview() {
        const name = document.getElementById('name').value || 'Nama Kategori';
        const description = document.getElementById('description').value || 'Deskripsi kategori akan muncul di sini.';
        const iconClass = document.getElementById('icon').value || 'fa-boxes';

        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-description').textContent = description;

        // Update icon preview
        const previewIcon = document.getElementById('icon-preview-i');
        previewIcon.className = `fas ${iconClass} text-primary text-4xl`;

        // Update char count
        document.getElementById('char-count').textContent = document.getElementById('description').value.length;
    }

    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush