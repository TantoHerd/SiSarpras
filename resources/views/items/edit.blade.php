{{-- resources/views/items/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Barang')
@section('page_title', 'Edit Barang')
@section('page_subtitle', 'Perbarui informasi barang inventaris')

@section('content')

<form method="POST" 
      id="item-edit-form"
      action="{{ route('items.update', $item->id) }}" 
      enctype="multipart/form-data"
      class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Kartu Foto Barang --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-4">Foto Barang</h3>
                
                <div class="flex flex-col items-center">
                    {{-- Preview Container --}}
                    <div id="image-preview-container" 
                         class="w-full aspect-square rounded-2xl bg-surface-soft border-2 border-dashed border-hairline 
                                flex items-center justify-center overflow-hidden mb-4 transition-colors">
                        
                        @if($item->image)
                            {{-- Preview foto lama --}}
                            <img id="image-preview" 
                                 src="{{ asset('storage/' . $item->image) }}" 
                                 alt="{{ $item->name }}"
                                 class="w-full h-full object-cover">
                            <div id="image-placeholder" class="text-center hidden">
                                <i class="fas fa-image text-steel text-4xl mb-2"></i>
                                <p class="text-caption text-steel">Belum ada foto</p>
                            </div>
                        @else
                            {{-- Placeholder --}}
                            <img id="image-preview" 
                                 src="" 
                                 alt="Preview"
                                 class="w-full h-full object-cover hidden">
                            <div id="image-placeholder" class="text-center">
                                <i class="fas fa-image text-steel text-4xl mb-2"></i>
                                <p class="text-caption text-steel">Belum ada foto</p>
                            </div>
                        @endif
                    </div>

                    {{-- Upload Button --}}
                    <input type="file" 
                           id="image" 
                           name="image"
                           accept="image/*"
                           onchange="previewImage(event)"
                           class="hidden">
                    
                    <label for="image" 
                           class="btn-ghost w-full cursor-pointer justify-center">
                        <i class="fas fa-upload"></i>
                        {{ $item->image ? 'Ganti Foto' : 'Pilih Foto' }}
                    </label>

                    <button type="button" 
                            id="remove-image-btn"
                            onclick="openRemoveImageModal()"
                            class="{{ $item->image ? '' : 'hidden' }} text-caption-bold text-critical hover:underline mt-2">
                        <i class="fas fa-times"></i> Hapus Foto
                    </button>

                    <p class="text-caption text-steel text-center mt-3">
                        JPG, PNG, WEBP. Maks 2MB.
                    </p>
                </div>

                @error('image')
                    <p class="mt-3 text-body-sm text-critical-strong flex items-center gap-1.5">
                        <i class="fas fa-exclamation-circle text-xs"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Kartu Kode Barang (Read-Only) --}}
            <div class="card bg-primary/5 border-primary/20">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-barcode text-primary"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-caption-bold text-steel uppercase tracking-wider mb-1">
                            Kode Barang
                        </p>
                        <p class="text-body-md-bold text-primary font-mono">
                            {{ $item->code }}
                        </p>
                        <p class="text-caption text-steel mt-1">
                            Kode tidak dapat diubah.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Kartu Info Perubahan --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-4">Info Perubahan</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Dibuat Oleh</p>
                            <p class="text-body-sm-bold text-ink-deep truncate">
                                {{ $item->creator->name ?? '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Terakhir Diubah</p>
                            <p class="text-body-sm-bold text-ink-deep truncate">
                                {{ $item->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================== KOLOM KANAN ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ============ INFORMASI UTAMA ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-info-circle text-primary"></i>
                    Informasi Utama
                </h3>

                <div class="space-y-5">
                    {{-- Nama Barang --}}
                    <div>
                        <label for="name" class="form-label">
                            Nama Barang <span class="text-critical">*</span>
                        </label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name', $item->name) }}"
                               required
                               class="form-input @error('name') form-input-error @enderror">
                        @error('name')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Kategori + Lokasi --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="category_id" class="form-label">
                                Kategori <span class="text-critical">*</span>
                            </label>
                            <select id="category_id" 
                                    name="category_id" 
                                    required
                                    onchange="updateTrackingInfo()"
                                    class="form-input @error('category_id') form-input-error @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" 
                                            data-tracking-mode="{{ $cat->default_tracking_mode }}"
                                            @selected(old('category_id', $item->category_id) == $cat->id)>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="location_id" class="form-label">
                                Lokasi <span class="text-critical">*</span>
                            </label>
                            <select id="location_id" 
                                    name="location_id" 
                                    required
                                    class="form-input @error('location_id') form-input-error @enderror">
                                <option value="">Pilih Lokasi</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" 
                                            @selected(old('location_id', $item->location_id) == $loc->id)>
                                        {{ $loc->name }} 
                                        @if($loc->floor) ({{ $loc->floor }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Supplier + Jumlah --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select id="supplier_id" 
                                    name="supplier_id" 
                                    class="form-input @error('supplier_id') form-input-error @enderror">
                                <option value="">Pilih Supplier (Opsional)</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" 
                                            @selected(old('supplier_id', $item->supplier_id) == $sup->id)>
                                        {{ $sup->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="quantity" class="form-label">
                                Jumlah <span class="text-critical">*</span>
                            </label>
                            <input id="quantity" 
                                   type="number" 
                                   name="quantity" 
                                   value="{{ old('quantity', $item->quantity) }}"
                                   min="1"
                                   required
                                   class="form-input @error('quantity') form-input-error @enderror">
                            @error('quantity')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Sumber Dana --}}
                    <div>
                        <label for="funding_source_id" class="form-label">
                            Sumber Dana
                            <span class="text-caption text-steel font-normal ml-1">(opsional)</span>
                        </label>
                        <select id="funding_source_id" 
                                name="funding_source_id" 
                                class="form-input @error('funding_source_id') form-input-error @enderror">
                            <option value="">-- Pilih Sumber Dana --</option>
                            @foreach($fundingSources as $source)
                                <option value="{{ $source->id }}" 
                                        @selected(old('funding_source_id', $item->funding_source_id ?? null) == $source->id)>
                                    {{ $source->code }} - {{ $source->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('funding_source_id')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ← BARU: Mode Tracking --}}
                    <div>
                        <label class="form-label">
                            Mode Tracking
                        </label>
                        <p class="text-caption text-steel mb-3">
                            Tentukan bagaimana barang ini dilacak.
                            <span id="category-tracking-hint" class="text-primary font-bold"></span>
                        </p>

                        <div class="space-y-3">
                            {{-- Ikuti Kategori --}}
                            <label class="cursor-pointer block">
                                <input type="radio" 
                                    name="tracking_mode" 
                                    value=""
                                    @checked(old('tracking_mode', $item->tracking_mode ?? '') === '')
                                    onchange="highlightTracking()"
                                    class="tracking-input sr-only">
                                <div class="tracking-card p-4 rounded-xl border-2 transition-all
                                            {{ old('tracking_mode', $item->tracking_mode ?? '') === '' ? 'border-primary bg-primary/5' : 'border-hairline-soft bg-canvas hover:border-hairline' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-check-circle text-primary"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-body-sm-bold text-ink-deep">Ikuti Kategori</p>
                                                <span class="text-caption text-primary font-bold">Direkomendasikan</span>
                                            </div>
                                            <p class="text-caption text-steel leading-relaxed">
                                                Pakai default dari kategori terpilih.
                                                <span id="tracking-mode-detail" class="text-ink-deep font-bold"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            {{-- Per Unit --}}
                            <label class="cursor-pointer block">
                                <input type="radio" 
                                    name="tracking_mode" 
                                    value="per_unit"
                                    @checked(old('tracking_mode', $item->tracking_mode) === 'per_unit')
                                    onchange="highlightTracking()"
                                    class="tracking-input sr-only">
                                <div class="tracking-card p-4 rounded-xl border-2 transition-all
                                            {{ old('tracking_mode', $item->tracking_mode) === 'per_unit' ? 'border-cobalt bg-cobalt/5' : 'border-hairline-soft bg-canvas hover:border-hairline' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-cobalt/10 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-fingerprint text-cobalt"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-body-sm-bold text-ink-deep mb-1">Per Unit (Override)</p>
                                            <p class="text-caption text-steel leading-relaxed">
                                                1 barang = 1 kode unik. Cocok untuk: <strong>Elektronik, Mebel, Lab</strong>.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            {{-- Per Batch --}}
                            <label class="cursor-pointer block">
                                <input type="radio" 
                                    name="tracking_mode" 
                                    value="per_batch"
                                    @checked(old('tracking_mode', $item->tracking_mode) === 'per_batch')
                                    onchange="highlightTracking()"
                                    class="tracking-input sr-only">
                                <div class="tracking-card p-4 rounded-xl border-2 transition-all
                                            {{ old('tracking_mode', $item->tracking_mode) === 'per_batch' ? 'border-warning bg-warning/5' : 'border-hairline-soft bg-canvas hover:border-hairline' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-layer-group text-warning"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-body-sm-bold text-ink-deep mb-1">Per Batch (Override)</p>
                                            <p class="text-caption text-steel leading-relaxed">
                                                1 barang = N quantity. Cocok untuk: <strong>ATK, Bangunan, Alat RT</strong>.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        @error('tracking_mode')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============ DETAIL SPESIFIKASI ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-cube text-primary"></i>
                    Detail Spesifikasi
                </h3>

                <div class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="brand" class="form-label">Merk</label>
                            <input id="brand" 
                                   type="text" 
                                   name="brand" 
                                   value="{{ old('brand', $item->brand) }}"
                                   placeholder="Contoh: Asus"
                                   class="form-input @error('brand') form-input-error @enderror">
                            @error('brand')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="form-label">Tipe</label>
                            <input id="type" 
                                   type="text" 
                                   name="type" 
                                   value="{{ old('type', $item->type) }}"
                                   placeholder="Contoh: TUF Gaming FX505"
                                   class="form-input @error('type') form-input-error @enderror">
                            @error('type')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="serial_number" class="form-label">Nomor Seri</label>
                        <input id="serial_number" 
                               type="text" 
                               name="serial_number" 
                               value="{{ old('serial_number', $item->serial_number) }}"
                               placeholder="Contoh: SN-12345-ABCDE"
                               class="form-input @error('serial_number') form-input-error @enderror">
                        @error('serial_number')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="purchase_year" class="form-label">Tahun Perolehan</label>
                            <input id="purchase_year" 
                                   type="number" 
                                   name="purchase_year" 
                                   value="{{ old('purchase_year', $item->purchase_year) }}"
                                   min="1900"
                                   max="{{ date('Y') + 1 }}"
                                   class="form-input @error('purchase_year') form-input-error @enderror">
                            @error('purchase_year')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="form-label">Harga Beli (Rp)</label>
                            <input id="price" 
                                   type="number" 
                                   name="price" 
                                   value="{{ old('price', $item->price) }}"
                                   min="0"
                                   step="1000"
                                   class="form-input @error('price') form-input-error @enderror">
                            @error('price')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ KONDISI BARANG ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-primary"></i>
                    Kondisi Barang
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach(\App\Enums\ItemConditionEnum::cases() as $condition)
                        @php
                            $isChecked = old('condition', $item->condition->value) === $condition->value;
                            $icon = match($condition->value) {
                                'baik' => 'fa-check-circle text-success',
                                'rusak_ringan' => 'fa-exclamation-triangle text-warning',
                                'rusak_berat' => 'fa-times-circle text-critical',
                                default => 'fa-circle text-steel',
                            };
                        @endphp

                        <label class="cursor-pointer condition-option">
                            <input type="radio" 
                                   name="condition" 
                                   value="{{ $condition->value }}"
                                   @checked($isChecked)
                                   onchange="highlightCondition()"
                                   class="condition-input sr-only">
                            <div class="condition-card p-4 rounded-xl border-2 bg-canvas
                                        hover:border-hairline transition-all text-center
                                        {{ $isChecked ? 'border-primary bg-primary/5' : 'border-hairline-soft' }}">
                                <i class="fas {{ $icon }} text-2xl mb-2"></i>
                                <p class="text-body-sm-bold text-ink-deep">{{ $condition->label() }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('condition')
                    <p class="mt-3 text-body-sm text-critical-strong flex items-center gap-1.5">
                        <i class="fas fa-exclamation-circle text-xs"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- ============ ACTION BUTTONS ============ --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                {{-- Tombol Hapus (Kiri) --}}
                <button type="button" 
                        onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="btn-danger">
                    <i class="fas fa-trash"></i>
                    Hapus Barang
                </button>

                {{-- Tombol Simpan & Batal (Kanan) --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('items.index') }}" class="btn-ghost">
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

{{-- ==================== MODAL HAPUS ==================== --}}
<div id="delete-modal" 
     class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">

        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>

        <h3 class="text-heading-sm text-ink-deep text-center mb-2">
            Hapus Barang Ini?
        </h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Barang <strong class="text-ink-deep">{{ $item->name }}</strong> akan dihapus dari sistem.
            Data tidak akan hilang permanen dan dapat dipulihkan oleh Admin.
        </p>

        <form method="POST" action="{{ route('items.destroy', $item->id) }}" class="flex gap-3">
            @csrf
            @method('DELETE')

            <button type="button" 
                    onclick="document.getElementById('delete-modal').classList.add('hidden')"
                    class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-danger flex-1">
                <i class="fas fa-trash"></i>
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

{{-- ==================== MODAL HAPUS FOTO ==================== --}}
<div id="remove-image-modal" 
     class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">

        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-warning/10 mx-auto mb-4">
            <i class="fas fa-image text-warning text-2xl"></i>
        </div>

        <h3 class="text-heading-sm text-ink-deep text-center mb-2">
            Hapus Foto Barang?
        </h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Foto akan dihapus permanen dari sistem setelah Anda klik <strong>Simpan Perubahan</strong>.
            Tindakan ini tidak dapat dibatalkan.
        </p>

        <div class="flex gap-3">
            <button type="button" 
                    onclick="document.getElementById('remove-image-modal').classList.add('hidden')"
                    class="btn-ghost flex-1">
                Batal
            </button>
            <button type="button" 
                    onclick="confirmRemoveImage()"
                    class="btn-danger flex-1">
                <i class="fas fa-trash"></i>
                Ya, Hapus Foto
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ============ Preview Foto ============
    function previewImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('image-placeholder');
            const removeBtn = document.getElementById('remove-image-btn');
            
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            removeBtn.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    // ============ Hapus Foto (dengan Modal) ============
    function openRemoveImageModal() {
        document.getElementById('remove-image-modal').classList.remove('hidden');
    }

    function confirmRemoveImage() {
        document.getElementById('image').value = '';
        document.getElementById('image-preview').src = '';
        document.getElementById('image-preview').classList.add('hidden');
        document.getElementById('image-placeholder').classList.remove('hidden');
        document.getElementById('remove-image-btn').classList.add('hidden');
        
        // Hapus input lama jika ada
        const existing = document.querySelector('input[name="remove_image"]');
        if (existing) existing.remove();
        
        // Tambahkan input hidden ke FORM EDIT (pakai ID)
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_image';
        input.value = '1';
        document.getElementById('item-edit-form').appendChild(input);
        
        document.getElementById('remove-image-modal').classList.add('hidden');
        
        // Debug: log untuk memastikan
        console.log('remove_image input added:', document.querySelector('input[name="remove_image"]'));
    }

    // ============ Highlight Kondisi ============
    function highlightCondition() {
        const inputs = document.querySelectorAll('.condition-input');
        
        inputs.forEach(input => {
            const card = input.closest('label').querySelector('.condition-card');
            
            if (input.checked) {
                card.classList.add('border-primary', 'bg-primary/5');
                card.classList.remove('border-hairline-soft');
            } else {
                card.classList.remove('border-primary', 'bg-primary/5');
                card.classList.add('border-hairline-soft');
            }
        });
    }

    // ============ Highlight Tracking Card ============
    function highlightTracking() {
        const inputs = document.querySelectorAll('.tracking-input');
        
        inputs.forEach(input => {
            const card = input.closest('label').querySelector('.tracking-card');
            
            // Reset semua
            card.classList.remove('border-primary', 'bg-primary/5', 'border-cobalt', 'bg-cobalt/5', 'border-warning', 'bg-warning/5');
            card.classList.add('border-hairline-soft');
            
            if (input.checked) {
                card.classList.remove('border-hairline-soft');
                
                if (input.value === '') {
                    card.classList.add('border-primary', 'bg-primary/5');
                } else if (input.value === 'per_unit') {
                    card.classList.add('border-cobalt', 'bg-cobalt/5');
                } else if (input.value === 'per_batch') {
                    card.classList.add('border-warning', 'bg-warning/5');
                }
            }
        });
    }

    // Jalankan saat load (untuk old value)
    document.addEventListener('DOMContentLoaded', function() {
        highlightCondition();
        highlightTracking();      
        updateTrackingInfo();     
    });
</script>
@endpush