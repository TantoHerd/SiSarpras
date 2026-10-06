{{-- resources/views/loans/request.blade.php --}}
@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')
@section('page_title', 'Ajukan Peminjaman')
@section('page_subtitle', 'Pilih barang yang ingin Anda pinjam')

@section('content')

<form method="POST" action="{{ route('loans.request.store') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Preview Barang ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Kartu Preview --}}
            <div class="card sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4">Barang Dipilih</h3>

                <div id="item-preview-empty" class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-circle bg-surface-soft mb-3">
                        <i class="fas fa-box-open text-steel text-2xl"></i>
                    </div>
                    <p class="text-body-sm text-steel">
                        Belum ada barang dipilih.<br>
                        Pilih dari daftar di kanan.
                    </p>
                </div>

                <div id="item-preview" class="hidden">
                    {{-- Foto --}}
                    <div class="aspect-square rounded-xl bg-surface-soft overflow-hidden mb-4">
                        <img id="preview-image" 
                             src="" 
                             alt="Preview"
                             class="w-full h-full object-cover hidden">
                        <div id="preview-image-placeholder" 
                             class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-box text-steel text-5xl"></i>
                        </div>
                    </div>

                    {{-- Nama --}}
                    <h4 id="preview-name" class="text-body-md-bold text-ink-deep mb-1"></h4>
                    <p id="preview-code" class="text-caption text-steel font-mono mb-3"></p>

                    {{-- Meta --}}
                    <div class="space-y-2 pt-3 border-t border-hairline-soft">
                        <div class="flex items-center gap-2 text-body-sm">
                            <i class="fas fa-tag text-steel w-4 text-xs"></i>
                            <span id="preview-category" class="text-steel">-</span>
                        </div>
                        <div class="flex items-center gap-2 text-body-sm">
                            <i class="fas fa-map-marker-alt text-steel w-4 text-xs"></i>
                            <span id="preview-location" class="text-steel">-</span>
                        </div>
                        <div class="flex items-center gap-2 text-body-sm">
                            <i class="fas fa-cubes text-steel w-4 text-xs"></i>
                            <span id="preview-quantity" class="text-steel">-</span>
                        </div>
                    </div>

                    {{-- ← BARU: Quantity untuk per_batch --}}
                    <div id="quantity-section" class="mt-4 pt-4 border-t border-hairline-soft hidden">
                        <label for="quantity" class="form-label">
                            Jumlah yang Dipinjam <span class="text-critical">*</span>
                        </label>
                        <p id="quantity-info" class="text-caption text-steel mb-2"></p>
                        <input id="quantity" 
                            type="number" 
                            name="quantity" 
                            value="{{ old('quantity', 1) }}"
                            min="1"
                            class="form-input @error('quantity') form-input-error @enderror">
                        @error('quantity')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Info Durasi --}}
                <div class="mt-4 p-3 rounded-xl bg-primary/5 border border-primary/20">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-primary mt-0.5 text-sm"></i>
                        <div>
                            <p class="text-caption-bold text-ink-deep">Durasi Peminjaman</p>
                            <p class="text-caption text-steel mt-1">
                                Maksimal <strong>{{ setting('max_loan_days', '7') }} hari</strong> sejak tanggal pinjam.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================== KOLOM KANAN: Form + Daftar Barang ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ============ FORM KEPERLUAN ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-edit text-primary"></i>
                    Detail Pengajuan
                </h3>

                <input id="quantity" 
                    type="number" 
                    name="quantity" 
                    value="{{ old('quantity', 1) }}"
                    min="1"
                    class="form-input @error('quantity') form-input-error @enderror hidden">

                {{-- Hidden input item_id --}}
                <input type="hidden" 
                       id="item_id" 
                       name="item_id" 
                       value="{{ old('item_id', $selectedItemId) }}">

                {{-- Error item_id --}}
                @error('item_id')
                    <div class="mb-4 flex items-start gap-3 p-3 rounded-xl bg-critical/10 border border-critical/20">
                        <i class="fas fa-exclamation-circle text-critical mt-0.5"></i>
                        <p class="text-body-sm text-ink">{{ $message }}</p>
                    </div>
                @enderror

                {{-- Keperluan --}}
                <div>
                    <label for="purpose" class="form-label">
                        Keperluan Peminjaman <span class="text-critical">*</span>
                    </label>
                    <textarea id="purpose" 
                              name="purpose" 
                              rows="4"
                              required
                              placeholder="Contoh: Untuk kegiatan pembelajaran di Lab Komputer kelas X IPA 1..."
                              class="form-input !h-auto resize-none @error('purpose') form-input-error @enderror">{{ old('purpose') }}</textarea>
                    @error('purpose')
                        <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Info Penting --}}
                <div class="mt-5 p-4 rounded-xl bg-warning/10 border border-warning/20">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-warning mt-0.5"></i>
                        <div>
                            <p class="text-body-sm-bold text-ink-deep">Perhatian</p>
                            <ul class="text-body-sm text-steel mt-1 space-y-1 list-disc list-inside">
                                <li>Pastikan barang dikembalikan sesuai tanggal jatuh tempo.</li>
                                <li>Kerusakan/kehilangan menjadi tanggung jawab peminjam.</li>
                                <li>Barang hanya boleh digunakan untuk keperluan sekolah.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ DAFTAR BARANG TERSEDIA ============ --}}
            <div class="card">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-body-md-bold text-ink-deep">Pilih Barang</h3>
                        <p class="text-body-sm text-steel mt-1">
                            {{ $items->count() }} barang tersedia untuk dipinjam
                        </p>
                    </div>
                </div>

                {{-- Search --}}
                <div class="relative mb-4">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm"></i>
                    <input type="text" 
                           id="search-item"
                           placeholder="Cari barang..."
                           oninput="filterItems()"
                           class="form-input pl-11">
                </div>

                @if($items->count() > 0)
                    <div id="items-list" class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[500px] overflow-y-auto pr-2">
                        @foreach($items as $item)
                            <label class="item-option cursor-pointer" 
                                   data-name="{{ strtolower($item->name) }}"
                                   data-code="{{ strtolower($item->code) }}">
                                <input type="radio" 
                                       name="item_radio" 
                                       value="{{ $item->id }}"
                                       @checked(old('item_id', $selectedItemId) == $item->id)
                                       onchange="selectItem(this)"
                                       class="sr-only">
                                <div class="item-card p-3 rounded-xl border-2 border-hairline-soft bg-canvas
                                            hover:border-hairline transition-all
                                            flex items-center gap-3">
                                    {{-- Foto --}}
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" 
                                             alt="{{ $item->name }}"
                                             class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-14 h-14 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-box text-steel"></i>
                                        </div>
                                    @endif

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-body-sm-bold text-ink-deep truncate">
                                            {{ $item->name }}
                                        </p>
                                        <p class="text-caption text-steel font-mono truncate">
                                            {{ $item->code }}
                                        </p>
                                        <p class="text-caption text-steel truncate">
                                            <i class="fas fa-map-marker-alt text-xs"></i>
                                            {{ $item->location->name ?? '-' }}
                                        </p>
                                    </div>

                                    {{-- Check --}}
                                    <div class="check-icon w-6 h-6 rounded-full border-2 border-hairline flex items-center justify-center flex-shrink-0 transition-all">
                                        <i class="fas fa-check text-white text-xs opacity-0 transition-opacity check-mark"></i>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-circle bg-surface-soft mb-3">
                            <i class="fas fa-box-open text-steel text-2xl"></i>
                        </div>
                        <p class="text-body-sm text-steel">
                            Tidak ada barang tersedia saat ini.
                        </p>
                    </div>
                @endif
            </div>

            {{-- ============ ACTION BUTTONS ============ --}}
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('loans.my') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </a>
                <button type="submit" 
                        id="submit-btn"
                        class="btn-primary"
                        disabled>
                    <i class="fas fa-paper-plane"></i>
                    Ajukan Peminjaman
                </button>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    const itemsData = @json($itemsJson);

    function selectItem(radio) {
        const itemId = radio.value;
        const item = itemsData[itemId];
        const quantitySection = document.getElementById('quantity-section');
        const quantityInput = document.getElementById('quantity');
        const quantityInfo = document.getElementById('quantity-info');

        if (item.tracking_mode === 'per_batch') {
            quantitySection.classList.remove('hidden');
            quantityInfo.textContent = `Stok tersedia: ${item.quantity} unit`;
            quantityInput.max = item.quantity;
            quantityInput.value = 1;
        } else {
            quantitySection.classList.add('hidden');
            // Pastikan tetap terkirim sebagai 1
            if (quantityInput) {
                quantityInput.value = 1;
            }
        }
        
        if (!item) return;

        // Update hidden input
        document.getElementById('item_id').value = itemId;

        // Reset semua card & check icon
        document.querySelectorAll('.item-card').forEach(card => {
            card.classList.remove('border-primary', 'bg-primary/5');
            card.classList.add('border-hairline-soft');
        });
        
        document.querySelectorAll('.check-icon').forEach(icon => {
            icon.classList.remove('bg-primary', 'border-primary');
            icon.classList.add('border-hairline');
        });
        
        document.querySelectorAll('.check-mark').forEach(mark => {
            mark.classList.add('opacity-0');
        });

        // Highlight card yang dipilih
        const card = radio.closest('label').querySelector('.item-card');
        card.classList.remove('border-hairline-soft');
        card.classList.add('border-primary', 'bg-primary/5');

        // Update check icon
        const checkIcon = radio.closest('label').querySelector('.check-icon');
        checkIcon.classList.remove('border-hairline');
        checkIcon.classList.add('bg-primary', 'border-primary');

        const checkMark = radio.closest('label').querySelector('.check-mark');
        checkMark.classList.remove('opacity-0');

        // Update preview
        document.getElementById('item-preview-empty').classList.add('hidden');
        document.getElementById('item-preview').classList.remove('hidden');

        document.getElementById('preview-name').textContent = item.name;
        document.getElementById('preview-code').textContent = item.code;
        document.getElementById('preview-category').textContent = item.category;
        document.getElementById('preview-location').textContent = item.location;
        document.getElementById('preview-quantity').textContent = item.quantity + ' unit';

        const previewImage = document.getElementById('preview-image');
        const previewPlaceholder = document.getElementById('preview-image-placeholder');

        if (item.image) {
            previewImage.src = item.image;
            previewImage.classList.remove('hidden');
            previewPlaceholder.classList.add('hidden');
        } else {
            previewImage.src = '';
            previewImage.classList.add('hidden');
            previewPlaceholder.classList.remove('hidden');
        }

        // Enable submit
        document.getElementById('submit-btn').disabled = false;
    }

    function filterItems() {
        const search = document.getElementById('search-item').value.toLowerCase();
        const options = document.querySelectorAll('.item-option');

        options.forEach(option => {
            const name = option.dataset.name || '';
            const code = option.dataset.code || '';
            
            if (name.includes(search) || code.includes(search)) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
    }

    // Auto-select jika ada old value
    document.addEventListener('DOMContentLoaded', function() {
        const checked = document.querySelector('input[name="item_radio"]:checked');
        if (checked) {
            selectItem(checked);
        }
    });
</script>
@endpush