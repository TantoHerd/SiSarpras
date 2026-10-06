{{-- resources/views/loans/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Peminjaman Baru')
@section('page_title', 'Peminjaman Baru')
@section('page_subtitle', 'Input peminjaman secara manual')

@section('content')

<form method="POST" action="{{ route('loans.store') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Preview ==================== --}}
        <div class="lg:col-span-1">
            <div class="card sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4">Preview</h3>

                <div id="preview-empty" class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-circle bg-surface-soft mb-3">
                        <i class="fas fa-hand-holding text-steel text-2xl"></i>
                    </div>
                    <p class="text-body-sm text-steel">
                        Belum ada data.<br>
                        Isi form di kanan.
                    </p>
                </div>

                <div id="preview-content" class="hidden">
                    <div class="space-y-3 pt-2">
                        <div>
                            <p class="text-caption text-steel">Barang</p>
                            <p id="prev-item" class="text-body-sm-bold text-ink-deep">-</p>
                        </div>
                        <div>
                            <p class="text-caption text-steel">Peminjam</p>
                            <p id="prev-borrower" class="text-body-sm-bold text-ink-deep">-</p>
                        </div>
                        <div>
                            <p class="text-caption text-steel">Keperluan</p>
                            <p id="prev-purpose" class="text-body-sm text-ink">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== KOLOM KANAN: Form ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ============ FORM ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-primary"></i>
                    Data Peminjaman
                </h3>

                <div class="space-y-5">

                    {{-- Peminjam --}}
                    <div>
                        <label for="borrower_id" class="form-label">
                            Peminjam <span class="text-critical">*</span>
                        </label>
                        <select id="borrower_id" 
                                name="borrower_id" 
                                required
                                onchange="updatePreview()"
                                class="form-input @error('borrower_id') form-input-error @enderror">
                            <option value="">-- Pilih Peminjam --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('borrower_id') == $user->id)>
                                    {{ $user->name }} — NIP: {{ $user->nip ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('borrower_id')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Barang --}}
                    <div>
                        <label for="item_select" class="form-label">
                            Barang <span class="text-critical">*</span>
                        </label>
                        <select id="item_select" 
                                name="item_id" 
                                required
                                onchange="updatePreview(); updateQuantityField()"
                                class="form-input @error('item_id') form-input-error @enderror">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                                    {{ $item->name }} — {{ $item->code }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ← BARU: Jumlah --}}
                    <div id="quantity-field" class="hidden">
                        <label for="quantity" class="form-label">
                            Jumlah <span class="text-critical">*</span>
                        </label>
                        <div id="quantity-info" class="mb-2 p-3 rounded-xl bg-primary/5 border border-primary/20">
                            <p class="text-caption text-steel">
                                <i class="fas fa-info-circle text-primary"></i>
                                <span id="quantity-info-text">-</span>
                            </p>
                        </div>
                        <input id="quantity" 
                            type="number" 
                            name="quantity" 
                            value="{{ old('quantity', 1) }}"
                            min="1"
                            oninput="updatePreview()"
                            class="form-input @error('quantity') form-input-error @enderror">
                        @error('quantity')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ← BARU: Info per unit --}}
                    <div id="quantity-locked" class="hidden p-3 rounded-xl bg-surface-soft border border-hairline-soft">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-lock text-steel mt-0.5 text-sm"></i>
                            <div>
                                <p class="text-body-sm-bold text-ink-deep">Jumlah: 1 unit</p>
                                <p class="text-caption text-steel mt-0.5">
                                    Barang ini Per Unit — 1 barang = 1 kode unik, hanya bisa dipinjam 1 unit.
                                </p>
                                <input type="hidden" name="quantity" value="1">
                            </div>
                        </div>
                    </div>

                    {{-- Keperluan --}}
                    <div>
                        <label for="purpose" class="form-label">
                            Keperluan <span class="text-critical">*</span>
                        </label>
                        <textarea id="purpose" 
                                  name="purpose" 
                                  rows="4"
                                  required
                                  oninput="updatePreview()"
                                  placeholder="Contoh: Untuk kegiatan presentasi di aula..."
                                  class="form-input !h-auto resize-none @error('purpose') form-input-error @enderror">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ============ INFO DURASI ============ --}}
            <div class="card bg-primary/5 border-primary/20">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clock text-primary"></i>
                    </div>
                    <div>
                        <p class="text-body-sm-bold text-ink-deep">Durasi Peminjaman</p>
                        <p class="text-body-sm text-steel mt-1">
                            Sistem otomatis menghitung tanggal jatuh tempo:
                            <strong>{{ setting('max_loan_days', 7) }} hari</strong> sejak peminjaman dibuat.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ============ ACTION ============ --}}
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('loans.index') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Peminjaman
                </button>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    const borrowerData = @json($borrowerData);
    const itemData = @json($itemData);
    const itemDataFull = @json($itemData);

    function updatePreview() {
        const borrowerId = document.getElementById('borrower_id').value;
        const itemId = document.getElementById('item_select').value;
        const purpose = document.getElementById('purpose').value;
        const quantityInput = document.getElementById('quantity');
        const quantity = quantityInput && !quantityInput.closest('.hidden') 
            ? quantityInput.value 
            : 1;

        if (!borrowerId && !itemId && !purpose) {
            document.getElementById('preview-empty').classList.remove('hidden');
            document.getElementById('preview-content').classList.add('hidden');
            return;
        }

        document.getElementById('preview-empty').classList.add('hidden');
        document.getElementById('preview-content').classList.remove('hidden');

        document.getElementById('prev-borrower').textContent = borrowerId ? borrowerData[borrowerId] : '-';
        document.getElementById('prev-item').textContent = itemId ? itemData[itemId] : '-';
        document.getElementById('prev-purpose').textContent = purpose || '-';
        document.getElementById('prev-item').textContent = itemId 
            ? itemData[itemId] + (quantity > 1 ? ` (${quantity} unit)` : '')
            : '-';
    }

    function updateQuantityField() {
        const itemId = document.getElementById('item_select').value;
        const quantityField = document.getElementById('quantity-field');
        const quantityLocked = document.getElementById('quantity-locked');
        const infoText = document.getElementById('quantity-info-text');
        const quantityInput = document.getElementById('quantity');

        // Reset
        quantityField.classList.add('hidden');
        quantityLocked.classList.add('hidden');

        if (!itemId || !itemDataFull[itemId]) {
            return;
        }

        const item = itemDataFull[itemId];
        const mode = item.tracking_mode;
        const stock = item.quantity;

        if (mode === 'per_batch') {
            // Tampilkan input quantity
            quantityField.classList.remove('hidden');
            quantityLocked.classList.add('hidden');

            infoText.textContent = `Barang ini Per Batch — stok tersedia: ${stock} unit`;
            
            quantityInput.max = stock;
            quantityInput.value = Math.min(parseInt(quantityInput.value) || 1, stock);
        } else {
            // Per unit: kunci quantity
            quantityField.classList.add('hidden');
            quantityLocked.classList.remove('hidden');
        }
    }

    // Panggil saat load
    document.addEventListener('DOMContentLoaded', function() {
        updateQuantityField();
        updatePreview();
    });
</script>
@endpush