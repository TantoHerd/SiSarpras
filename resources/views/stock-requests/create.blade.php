{{-- resources/views/stock-requests/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Permintaan BHP Baru')
@section('page_title', 'Permintaan BHP Baru')
@section('page_subtitle', 'Ajukan permintaan barang habis pakai')

@section('content')

<form method="POST" action="{{ route('stock-requests.store') }}" class="space-y-6" id="request-form">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Preview ==================== --}}
        <div class="lg:col-span-1">
            <div class="card sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4">Ringkasan</h3>

                <div id="summary-empty" class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-circle bg-surface-soft mb-3">
                        <i class="fas fa-clipboard-list text-steel text-2xl"></i>
                    </div>
                    <p class="text-body-sm text-steel">
                        Belum ada item.<br>
                        Tambahkan barang di kanan.
                    </p>
                </div>

                <div id="summary-content" class="hidden">
                    <div class="space-y-3">
                        <div>
                            <p class="text-caption text-steel">Total Item</p>
                            <p class="text-heading-sm text-ink-deep">
                                <span id="summary-count">0</span> jenis
                            </p>
                        </div>
                        <div>
                            <p class="text-caption text-steel">Total Unit</p>
                            <p class="text-heading-sm text-primary">
                                <span id="summary-qty">0</span> unit
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== KOLOM KANAN: Form ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Flash --}}
            @if(session('error'))
                <div class="flex items-start gap-3 p-4 rounded-xl bg-critical/10 border border-critical/20">
                    <i class="fas fa-exclamation-circle text-critical text-lg mt-0.5"></i>
                    <p class="text-body-sm text-ink">{{ session('error') }}</p>
                </div>
            @endif

            {{-- ============ FORM ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-info-circle text-primary"></i>
                    Detail Permintaan
                </h3>

                <div class="space-y-5">

                    {{-- Requester Type --}}
                    <div>
                        <label for="requester_type" class="form-label">
                            Sebagai <span class="text-critical">*</span>
                        </label>
                        <select id="requester_type" 
                                name="requester_type" 
                                required
                                class="form-input @error('requester_type') form-input-error @enderror">
                            <option value="guru" @selected(old('requester_type', 'guru') === 'guru')>
                                Guru
                            </option>
                            <option value="staf" @selected(old('requester_type') === 'staf')>
                                Staf
                            </option>
                            <option value="petugas" @selected(old('requester_type') === 'petugas')>
                                Petugas
                            </option>
                        </select>
                        @error('requester_type')
                            <p class="mt-2 text-body-sm text-critical-strong">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Purpose --}}
                    <div>
                        <label for="purpose" class="form-label">
                            Keperluan <span class="text-critical">*</span>
                        </label>
                        <textarea id="purpose" 
                                  name="purpose" 
                                  rows="3"
                                  required
                                  minlength="10"
                                  maxlength="500"
                                  placeholder="Contoh: Untuk praktikum Biologi kelas X IPA 1..."
                                  class="form-input !h-auto resize-none @error('purpose') form-input-error @enderror">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <p class="mt-2 text-body-sm text-critical-strong">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ============ DAFTAR ITEM ============ --}}
            <div class="card">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-body-md-bold text-ink-deep">Barang yang Diminta</h3>
                        <p class="text-body-sm text-steel mt-1">
                            Maksimal 5 jenis barang, 10 unit per barang.
                        </p>
                    </div>
                    <button type="button" 
                            onclick="addItemRow()" 
                            class="btn-ghost !py-2 !px-3 !text-xs">
                        <i class="fas fa-plus"></i>
                        Tambah
                    </button>
                </div>

                @error('items')
                    <div class="mb-4 flex items-start gap-2 p-3 rounded-lg bg-critical/10 border border-critical/20">
                        <i class="fas fa-exclamation-circle text-critical mt-0.5 text-sm"></i>
                        <p class="text-body-sm text-critical-strong">{{ $message }}</p>
                    </div>
                @enderror

                <div id="items-container" class="space-y-3"></div>

                {{-- Empty State --}}
                <div id="items-empty" class="py-8 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-circle bg-surface-soft mb-3">
                        <i class="fas fa-box-open text-steel text-xl"></i>
                    </div>
                    <p class="text-body-sm text-steel">
                        Belum ada barang.
                    </p>
                    <button type="button" 
                            onclick="addItemRow()" 
                            class="btn-primary mt-3">
                        <i class="fas fa-plus"></i>
                        Tambah Barang Pertama
                    </button>
                </div>
            </div>

            {{-- ============ ACTION ============ --}}
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('stock-requests.index') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Kirim Permintaan
                </button>
            </div>

        </div>
    </div>
</form>

{{-- ============ TEMPLATE ITEM ROW (hidden) ============ --}}
<template id="item-row-template">
    <div class="item-row p-4 rounded-xl border-2 border-hairline-soft bg-canvas">
        <div class="flex items-start gap-3">
            {{-- Nomor --}}
            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-1">
                <span class="item-number text-body-sm-bold text-primary">1</span>
            </div>

            {{-- Content --}}
            <div class="flex-1 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- Item Select --}}
                    <div>
                        <label class="form-label text-caption">Barang <span class="text-critical">*</span></label>
                        <select name="items[INDEX][item_id]" 
                                required
                                onchange="checkStock(this)"
                                class="form-input item-select">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" 
                                        data-stock="{{ $item->quantity }}"
                                        data-name="{{ $item->name }}">
                                    {{ $item->name }} (Stok: {{ $item->quantity }})
                                </option>
                            @endforeach
                        </select>
                        <p class="stock-info text-caption text-steel mt-1"></p>
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label class="form-label text-caption">Jumlah <span class="text-critical">*</span></label>
                        <input type="number" 
                               name="items[INDEX][quantity]"
                               min="1"
                               max="10"
                               value="1"
                               required
                               oninput="updateSummary()"
                               class="form-input item-quantity">
                    </div>
                </div>

                {{-- Note --}}
                <div>
                    <input type="text" 
                           name="items[INDEX][note]"
                           placeholder="Catatan (opsional)..."
                           maxlength="200"
                           class="form-input !h-9 !text-xs">
                </div>
            </div>

            {{-- Remove Button --}}
            <button type="button" 
                    onclick="removeItemRow(this)" 
                    class="btn-icon !w-8 !h-8 hover:!bg-critical/10 hover:!text-critical mt-1 flex-shrink-0"
                    title="Hapus">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
    let itemIndex = 0;
    const maxItems = 5;

    function addItemRow() {
        const container = document.getElementById('items-container');
        const currentRows = container.querySelectorAll('.item-row').length;
        
        if (currentRows >= maxItems) {
            alert(`Maksimal ${maxItems} jenis barang per permintaan.`);
            return;
        }

        const template = document.getElementById('item-row-template');
        const clone = template.content.cloneNode(true);
        
        // Update INDEX placeholder
        const html = clone.querySelector('.item-row').outerHTML.replace(/INDEX/g, itemIndex);
        container.insertAdjacentHTML('beforeend', html);
        
        // Update numbering
        updateNumbering();
        updateSummary();
        
        // Hide empty state
        document.getElementById('items-empty').classList.add('hidden');
        
        itemIndex++;
    }

    function removeItemRow(btn) {
        const row = btn.closest('.item-row');
        row.remove();
        
        updateNumbering();
        updateSummary();
        
        // Show empty state kalau tidak ada row
        const container = document.getElementById('items-container');
        if (container.querySelectorAll('.item-row').length === 0) {
            document.getElementById('items-empty').classList.remove('hidden');
        }
    }

    function updateNumbering() {
        document.querySelectorAll('.item-row').forEach((row, i) => {
            row.querySelector('.item-number').textContent = i + 1;
        });
    }

    function checkStock(select) {
        const selected = select.options[select.selectedIndex];
        const stock = selected.dataset.stock;
        const stockInfo = select.closest('div').querySelector('.stock-info');
        const qtyInput = select.closest('.item-row').querySelector('.item-quantity');
        
        if (stock) {
            stockInfo.innerHTML = `<i class="fas fa-info-circle text-primary"></i> Stok tersedia: <strong>${stock}</strong>`;
            qtyInput.max = Math.min(parseInt(stock), 10);
        } else {
            stockInfo.textContent = '';
            qtyInput.max = 10;
        }
        
        updateSummary();
    }

    function updateSummary() {
        const rows = document.querySelectorAll('.item-row');
        const summaryEmpty = document.getElementById('summary-empty');
        const summaryContent = document.getElementById('summary-content');
        
        if (rows.length === 0) {
            summaryEmpty.classList.remove('hidden');
            summaryContent.classList.add('hidden');
            return;
        }

        let count = 0;
        let totalQty = 0;

        rows.forEach(row => {
            const itemSelect = row.querySelector('.item-select');
            const qtyInput = row.querySelector('.item-quantity');
            
            if (itemSelect.value) {
                count++;
                totalQty += parseInt(qtyInput.value) || 0;
            }
        });

        summaryEmpty.classList.add('hidden');
        summaryContent.classList.remove('hidden');
        
        document.getElementById('summary-count').textContent = count;
        document.getElementById('summary-qty').textContent = totalQty;
    }

    // Init: tambahkan 1 row saat load
    document.addEventListener('DOMContentLoaded', function() {
        addItemRow();
    });
</script>
@endpush