{{-- resources/views/items/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Barang')
@section('page_title', 'Tambah Barang')
@section('page_subtitle', 'Daftarkan barang baru ke dalam sistem inventaris')

@section('content')

<form method="POST" 
      action="{{ route('items.store') }}" 
      enctype="multipart/form-data"
      class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Upload Foto + Kode ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Kartu Foto Barang --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-4">Foto Barang</h3>
                
                <div class="flex flex-col items-center">
                    {{-- Preview Container --}}
                    <div id="image-preview-container" 
                         class="w-full aspect-square rounded-2xl bg-surface-soft border-2 border-dashed border-hairline 
                                flex items-center justify-center overflow-hidden mb-4 transition-colors">
                        
                        {{-- Placeholder (default) --}}
                        <div id="image-placeholder" class="text-center">
                            <i class="fas fa-image text-steel text-4xl mb-2"></i>
                            <p class="text-caption text-steel">Belum ada foto</p>
                        </div>

                        {{-- Preview Image (hidden default) --}}
                        <img id="image-preview" 
                             src="" 
                             alt="Preview"
                             class="w-full h-full object-cover hidden">
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
                        Pilih Foto
                    </label>

                    <button type="button" 
                            id="remove-image-btn"
                            onclick="removeImage()"
                            class="hidden text-caption-bold text-critical hover:underline mt-2">
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

            {{-- Kartu Preview Kode Barang --}}
            <div class="card bg-primary/5 border-primary/20">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-barcode text-primary"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-caption-bold text-steel uppercase tracking-wider mb-1">
                            Kode Barang (Otomatis)
                        </p>
                        <p id="code-preview" 
                           class="text-body-md-bold text-primary font-mono">
                            Pilih kategori dahulu...
                        </p>
                        <p class="text-caption text-steel mt-1">
                            Format: KATEGORI-TAHUN-BULAN-URUTAN
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================== KOLOM KANAN: Form Detail ==================== --}}
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
                               value="{{ old('name') }}"
                               placeholder="Contoh: Laptop Asus TUF Gaming"
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
                                    onchange="updateCodePreview()"
                                    class="form-input @error('category_id') form-input-error @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" 
                                            @selected(old('category_id') == $cat->id)>
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
                                            @selected(old('location_id') == $loc->id)>
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
                                            @selected(old('supplier_id') == $sup->id)>
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
                                   value="{{ old('quantity', 1) }}"
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
                </div>
            </div>

            {{-- ============ DETAIL SPESIFIKASI ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-cube text-primary"></i>
                    Detail Spesifikasi
                </h3>

                <div class="space-y-5">
                    {{-- Merk + Tipe --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="brand" class="form-label">Merk</label>
                            <input id="brand" 
                                   type="text" 
                                   name="brand" 
                                   value="{{ old('brand') }}"
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
                                   value="{{ old('type') }}"
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

                    {{-- Serial Number --}}
                    <div>
                        <label for="serial_number" class="form-label">Nomor Seri</label>
                        <input id="serial_number" 
                               type="text" 
                               name="serial_number" 
                               value="{{ old('serial_number') }}"
                               placeholder="Contoh: SN-12345-ABCDE"
                               class="form-input @error('serial_number') form-input-error @enderror">
                        <p class="text-caption text-steel mt-1">
                            Kosongkan jika tidak ada.
                        </p>
                        @error('serial_number')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Tahun + Harga --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="purchase_year" class="form-label">Tahun Perolehan</label>
                            <input id="purchase_year" 
                                   type="number" 
                                   name="purchase_year" 
                                   value="{{ old('purchase_year', date('Y')) }}"
                                   min="1900"
                                   max="{{ date('Y') + 1 }}"
                                   placeholder="{{ date('Y') }}"
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
                                   value="{{ old('price') }}"
                                   min="0"
                                   step="1000"
                                   placeholder="0"
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
                    Kondisi Awal
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach(\App\Enums\ItemConditionEnum::cases() as $condition)
                        @php
                            $isChecked = old('condition', 'baik') === $condition->value;
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
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('items.index') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Barang
                </button>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    // ============ Preview Foto ============
    function previewImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validasi ukuran (2MB)
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

    // ============ Hapus Foto ============
    function removeImage() {
        document.getElementById('image').value = '';
        document.getElementById('image-preview').src = '';
        document.getElementById('image-preview').classList.add('hidden');
        document.getElementById('image-placeholder').classList.remove('hidden');
        document.getElementById('remove-image-btn').classList.add('hidden');
    }

    // ============ Preview Kode Otomatis ============
    function updateCodePreview() {
        const categoryId = document.getElementById('category_id').value;
        const codePreview = document.getElementById('code-preview');

        if (!categoryId) {
            codePreview.textContent = 'Pilih kategori dahulu...';
            codePreview.classList.add('text-steel');
            codePreview.classList.remove('text-primary');
            return;
        }

        // Ambil nama kategori
        const categoryName = document.querySelector(`#category_id option[value="${categoryId}"]`).textContent.trim();
        
        // Generate prefix (4 huruf pertama, uppercase)
        const prefix = categoryName.replace(/[^A-Za-z]/g, '').substring(0, 4).toUpperCase();
        
        // Tanggal
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        
        // Tampilkan (urutan akan di-generate di server)
        codePreview.textContent = `${prefix}-${year}-${month}-???`;
        codePreview.classList.remove('text-steel');
        codePreview.classList.add('text-primary');
    }

    // Jalankan saat load jika ada old value
    document.addEventListener('DOMContentLoaded', function() {
        updateCodePreview();
    });

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

    // Jalankan saat load (untuk old value)
    document.addEventListener('DOMContentLoaded', function() {
        highlightCondition();
        updateCodePreview();
    });
</script>
@endpush