{{-- resources/views/categories/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Kategori')
@section('page_title', 'Edit Kategori')
@section('page_subtitle', 'Perbarui informasi kategori')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('categories.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-tags text-xs"></i> Kategori Barang
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <a href="{{ route('categories.show', $category->id) }}" class="hover:text-primary">
        {{ $category->name }}
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Edit</span>
</nav>

<form method="POST" action="{{ route('categories.update', $category->id) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Preview + Info ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card text-center sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4 text-left">Preview</h3>

                {{-- Icon Preview --}}
                <div class="flex justify-center mb-4">
                    <div id="icon-preview" 
                         class="w-24 h-24 rounded-2xl bg-primary/10 flex items-center justify-center">
                        <i id="icon-preview-i" 
                           class="fas {{ old('icon', $category->icon ?? 'fa-boxes') }} text-primary text-4xl"></i>
                    </div>
                </div>

                {{-- Name Preview --}}
                <h4 id="preview-name" class="text-body-md-bold text-ink-deep mb-2">
                    {{ $category->name }}
                </h4>

                {{-- Description Preview --}}
                <p id="preview-description" class="text-body-sm text-steel mb-4 line-clamp-3 px-2">
                    {{ $category->description ?? 'Deskripsi kategori akan muncul di sini.' }}
                </p>

                {{-- Category Info --}}
                <div class="pt-4 mt-4 border-t border-hairline-soft space-y-3 text-left">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-boxes text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Jumlah Barang</p>
                            <p class="text-body-sm-bold text-ink-deep">
                                {{ $category->items_count ?? $category->items()->count() }} barang
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Dibuat</p>
                            <p class="text-body-sm-bold text-ink-deep">
                                {{ $category->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Warning Kalau Punya Barang --}}
                @php $itemCount = $category->items()->count(); @endphp
                @if($itemCount > 0)
                    <div class="mt-4 p-3 rounded-xl bg-warning/5 border border-warning/20 text-left">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle text-warning mt-0.5 text-sm"></i>
                            <div>
                                <p class="text-caption-bold text-ink-deep">Perhatian</p>
                                <p class="text-caption text-steel mt-1">
                                    Kategori ini digunakan oleh <strong>{{ $itemCount }} barang</strong>. 
                                    Perubahan nama akan mempengaruhi tampilan di barang-barang tersebut.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
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
                               value="{{ old('name', $category->name) }}"
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
                                  class="form-input !h-auto resize-none @error('description') form-input-error @enderror">{{ old('description', $category->description) }}</textarea>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-caption text-steel">
                                Opsional, maksimal 500 karakter.
                            </p>
                            <p class="text-caption text-steel">
                                <span id="char-count">{{ strlen(old('description', $category->description ?? '')) }}</span>/500
                            </p>
                        </div>
                        @error('description')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ← BARU: Mode Tracking --}}
                    <div>
                        <label class="form-label">
                            Mode Tracking Default <span class="text-critical">*</span>
                        </label>
                        <p class="text-caption text-steel mb-3">
                            Digunakan sebagai default untuk item baru di kategori ini.
                            @if($category->items_count > 0)
                                <span class="text-warning font-bold">
                                    ⚠️ Kategori ini sudah memiliki {{ $category->items_count }} barang. Perubahan hanya berlaku untuk barang baru.
                                </span>
                            @endif
                        </p>

                        <div class="space-y-3">
                            {{-- Per Unit --}}
                            <label class="cursor-pointer block">
                                <input type="radio" 
                                    name="default_tracking_mode" 
                                    value="per_unit"
                                    @checked(old('default_tracking_mode', $category->default_tracking_mode) === 'per_unit')
                                    onchange="updateTrackingPreview()"
                                    class="tracking-input sr-only">
                                <div class="tracking-card p-4 rounded-xl border-2 transition-all
                                            {{ old('default_tracking_mode', $category->default_tracking_mode) === 'per_unit' ? 'border-primary bg-primary/5' : 'border-hairline-soft bg-canvas hover:border-hairline' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-fingerprint text-primary"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-body-sm-bold text-ink-deep">Per Unit</p>
                                                <span class="text-caption text-primary font-bold">Direkomendasikan untuk aset unik</span>
                                            </div>
                                            <p class="text-caption text-steel leading-relaxed">
                                                1 barang = 1 kode unik. Cocok untuk: <strong>Elektronik, Mebel, Lab, Olahraga, Audio Visual</strong>.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            {{-- Per Batch --}}
                            <label class="cursor-pointer block">
                                <input type="radio" 
                                    name="default_tracking_mode" 
                                    value="per_batch"
                                    @checked(old('default_tracking_mode', $category->default_tracking_mode) === 'per_batch')
                                    onchange="updateTrackingPreview()"
                                    class="tracking-input sr-only">
                                <div class="tracking-card p-4 rounded-xl border-2 transition-all
                                            {{ old('default_tracking_mode', $category->default_tracking_mode) === 'per_batch' ? 'border-warning bg-warning/5' : 'border-hairline-soft bg-canvas hover:border-hairline' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-layer-group text-warning"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-body-sm-bold text-ink-deep">Per Batch</p>
                                                <span class="text-caption text-warning font-bold">Untuk barang habis pakai</span>
                                            </div>
                                            <p class="text-caption text-steel leading-relaxed">
                                                1 barang = N quantity (1 kode untuk grup). Cocok untuk: <strong>ATK, Bangunan, Alat Rumah Tangga</strong>.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        @error('default_tracking_mode')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ← BARU: Portal Siswa --}}
                    <div>
                        <label class="form-label">
                            Akses Portal Siswa
                        </label>
                        <p class="text-caption text-steel mb-3">
                            Aktifkan jika barang di kategori ini boleh dipinjam oleh siswa melalui portal.
                            @if($category->items_count > 0)
                                <span class="text-warning font-bold">
                                    ⚠️ Perubahan hanya berlaku untuk tampilan portal, tidak mengubah data barang.
                                </span>
                            @endif
                        </p>

                        <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors
                                    {{ old('allow_student_loan', $category->allow_student_loan) ? 'border-primary bg-primary/5' : 'border-hairline-soft bg-canvas hover:border-hairline' }}"
                            id="portal-toggle-card">
                            <input type="hidden" name="allow_student_loan" value="0">
                            <input type="checkbox" 
                                name="allow_student_loan" 
                                value="1"
                                id="allow-student-loan"
                                @checked(old('allow_student_loan', $category->allow_student_loan))
                                onchange="togglePortalCard(this)"
                                class="mt-0.5 w-5 h-5 rounded border-hairline-soft text-primary focus:ring-primary">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-body-sm-bold text-ink-deep">Boleh Dipinjam Siswa</p>
                                    <span class="badge-info text-xs">
                                        <i class="fas fa-user-graduate mr-1"></i>
                                        Portal
                                    </span>
                                </div>
                                <p class="text-caption text-steel">
                                    Kalau aktif, barang-barang di kategori ini akan muncul di <strong>portal siswa</strong> (<code>/portal</code>) dan bisa dipinjam secara mandiri.
                                </p>
                            </div>
                        </label>

                        @error('allow_student_loan')
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

                <input type="hidden" id="icon" name="icon" value="{{ old('icon', $category->icon ?? 'fa-boxes') }}">

                @php
                    $iconOptions = [
                        ['class' => 'fa-laptop', 'label' => 'Laptop'],
                        ['class' => 'fa-desktop', 'label' => 'Komputer'],
                        ['class' => 'fa-mobile-alt', 'label' => 'Mobile'],
                        ['class' => 'fa-print', 'label' => 'Printer'],
                        ['class' => 'fa-keyboard', 'label' => 'Keyboard'],
                        ['class' => 'fa-mouse', 'label' => 'Mouse'],
                        ['class' => 'fa-tv', 'label' => 'TV'],
                        ['class' => 'fa-camera', 'label' => 'Kamera'],
                        ['class' => 'fa-video', 'label' => 'Video'],
                        ['class' => 'fa-microphone', 'label' => 'Audio'],
                        ['class' => 'fa-headphones', 'label' => 'Headphone'],
                        ['class' => 'fa-volume-up', 'label' => 'Speaker'],
                        ['class' => 'fa-chair', 'label' => 'Kursi'],
                        ['class' => 'fa-couch', 'label' => 'Sofa'],
                        ['class' => 'fa-bed', 'label' => 'Tempat Tidur'],
                        ['class' => 'fa-table', 'label' => 'Meja'],
                        ['class' => 'fa-flask', 'label' => 'Lab'],
                        ['class' => 'fa-microscope', 'label' => 'Mikroskop'],
                        ['class' => 'fa-vial', 'label' => 'Tabung'],
                        ['class' => 'fa-dna', 'label' => 'Biologi'],
                        ['class' => 'fa-futbol', 'label' => 'Sepakbola'],
                        ['class' => 'fa-basketball-ball', 'label' => 'Basket'],
                        ['class' => 'fa-volleyball-ball', 'label' => 'Voli'],
                        ['class' => 'fa-running', 'label' => 'Atletik'],
                        ['class' => 'fa-pen', 'label' => 'Pulpen'],
                        ['class' => 'fa-pencil-alt', 'label' => 'Pensil'],
                        ['class' => 'fa-book', 'label' => 'Buku'],
                        ['class' => 'fa-file-alt', 'label' => 'Kertas'],
                        ['class' => 'fa-clipboard', 'label' => 'Papan'],
                        ['class' => 'fa-building', 'label' => 'Bangunan'],
                        ['class' => 'fa-tools', 'label' => 'Peralatan'],
                        ['class' => 'fa-boxes', 'label' => 'Umum'],
                        ['class' => 'fa-box', 'label' => 'Kotak'],
                        ['class' => 'fa-archive', 'label' => 'Arsip'],
                        ['class' => 'fa-utensils', 'label' => 'Kantin'],
                        ['class' => 'fa-music', 'label' => 'Musik'],
                    ];
                    $selectedIcon = old('icon', $category->icon ?? 'fa-boxes');
                @endphp

                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2" id="icon-picker">
                    @foreach($iconOptions as $icon)
                        <button type="button"
                                data-icon="{{ $icon['class'] }}"
                                title="{{ $icon['label'] }}"
                                onclick="selectIcon('{{ $icon['class'] }}')"
                                class="icon-option aspect-square rounded-xl border-2 transition-all
                                       flex items-center justify-center group
                                       {{ $selectedIcon === $icon['class'] ? 'border-primary bg-primary/5' : 'border-hairline-soft bg-canvas hover:border-primary hover:bg-primary/5' }}">
                            <i class="fas {{ $icon['class'] }} text-lg transition-colors
                                      {{ $selectedIcon === $icon['class'] ? 'text-primary' : 'text-steel group-hover:text-primary' }}"></i>
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
            <div class="flex flex-wrap items-center justify-between gap-3">
                {{-- Delete --}}
                <button type="button" 
                        onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="btn-danger">
                    <i class="fas fa-trash"></i>
                    Hapus Kategori
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('categories.index') }}" class="btn-ghost">
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
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus Kategori?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Kategori <strong class="text-ink-deep">{{ $category->name }}</strong> akan dihapus.
        </p>
        <form method="POST" action="{{ route('categories.destroy', $category->id) }}" class="flex gap-3">
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
    function selectIcon(iconClass) {
        document.getElementById('icon').value = iconClass;

        document.querySelectorAll('.icon-option').forEach(btn => {
            btn.classList.remove('border-primary', 'bg-primary/5');
            btn.classList.add('border-hairline-soft');
            
            const icon = btn.querySelector('i');
            icon.classList.remove('text-primary');
            icon.classList.add('text-steel');
        });

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

        const previewIcon = document.getElementById('icon-preview-i');
        previewIcon.className = `fas ${iconClass} text-primary text-4xl`;

        document.getElementById('char-count').textContent = document.getElementById('description').value.length;
    }

    function updateTrackingPreview() {
        const inputs = document.querySelectorAll('.tracking-input');
        
        inputs.forEach(input => {
            const card = input.closest('label').querySelector('.tracking-card');
            const icon = card.querySelector('div > div:first-child');
            
            if (input.checked) {
                if (input.value === 'per_unit') {
                    card.classList.add('border-primary', 'bg-primary/5');
                    card.classList.remove('border-hairline-soft', 'bg-canvas', 'border-warning', 'bg-warning/5');
                } else {
                    card.classList.add('border-warning', 'bg-warning/5');
                    card.classList.remove('border-hairline-soft', 'bg-canvas', 'border-primary', 'bg-primary/5');
                }
            } else {
                card.classList.remove('border-primary', 'bg-primary/5', 'border-warning', 'bg-warning/5');
                card.classList.add('border-hairline-soft', 'bg-canvas');
            }
        });
    }

    function togglePortalCard(checkbox) {
        const card = document.getElementById('portal-toggle-card');
        if (!card) return;
        
        if (checkbox.checked) {
            card.classList.add('border-primary', 'bg-primary/5');
            card.classList.remove('border-hairline-soft', 'bg-canvas');
        } else {
            card.classList.remove('border-primary', 'bg-primary/5');
            card.classList.add('border-hairline-soft', 'bg-canvas');
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        updateTrackingPreview();
        // ... init lain
    });

    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush