{{-- resources/views/receivings/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Penerimaan Barang Baru')

@section('content')

<form method="POST" action="{{ route('receivings.store') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- KOLOM KIRI: Info Header --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-4"><i class="fas fa-info-circle text-primary mr-2"></i> Info Penerimaan</h3>
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Tanggal Terima <span class="text-critical">*</span></label>
                        <input type="date" name="receiving_date" value="{{ old('receiving_date', date('Y-m-d')) }}" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-input">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" @selected(old('supplier_id') == $sup->id)>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Sumber Dana</label>
                        <select name="funding_source_id" class="form-input">
                            <option value="">-- Pilih Sumber Dana --</option>
                            @foreach($fundingSources as $fs)
                                <option value="{{ $fs->id }}" @selected(old('funding_source_id') == $fs->id)>{{ $fs->code }} - {{ $fs->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">No. Invoice / Surat Jalan</label>
                        <input type="text" name="invoice_number" value="{{ old('invoice_number') }}" placeholder="Opsional" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Catatan</label>
                        <textarea name="note" rows="2" class="form-input !h-auto">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Daftar Barang --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card bg-primary/5 border-primary/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-body-md-bold text-ink-deep">Barang yang Diterima</h3>
                    <button type="button" onclick="addRow()" class="btn-primary !py-2 !px-4 !text-sm"><i class="fas fa-plus"></i> Tambah Baris</button>
                </div>
                <div id="items-container" class="space-y-4"></div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('receivings.index') }}" class="btn-ghost">Batal</a>
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Simpan Penerimaan</button>
            </div>
        </div>
    </div>
</form>

{{-- TEMPLATE ROW (Hidden) --}}
<template id="row-template">
    <div class="item-row p-4 bg-white rounded-xl border border-hairline-soft shadow-sm relative">
        
        {{-- Tombol Hapus (X) --}}
        <button type="button" onclick="this.closest('.item-row').remove()" 
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center text-critical bg-critical/5 hover:bg-critical/10 rounded-full transition-colors z-10" 
                title="Hapus Baris">
            <i class="fas fa-times"></i>
        </button>
        
        {{-- Toggle Mode --}}
        <div class="mb-3 pr-10 flex gap-4 border-b border-hairline-soft pb-3">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="mode[INDEX]" value="existing" checked onchange="toggleMode(this, INDEX)" class="text-primary focus:ring-primary">
                <span class="text-sm font-bold">Pilih Barang Existing</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="mode[INDEX]" value="new" onchange="toggleMode(this, INDEX)" class="text-primary focus:ring-primary">
                <span class="text-sm font-bold">Buat Barang Baru</span>
            </label>
        </div>

        {{-- MODE EXISTING --}}
        <div id="existing-INDEX" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label class="text-caption font-bold text-steel">Pilih Barang</label>
                <select name="items[INDEX][item_id]" class="form-input select-existing" onchange="checkExisting(this)">
                    <option value="">-- Cari Barang --</option>
                    @foreach($items as $itm)
                        <option value="{{ $itm->id }}" data-price="{{ $itm->price }}">{{ $itm->name }} ({{ $itm->code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-caption font-bold text-steel">Jumlah</label>
                <input type="number" name="items[INDEX][quantity]" min="1" value="1" class="form-input">
            </div>
        </div>

        {{-- MODE NEW (Disamakan dengan form Item) --}}
        <div id="new-INDEX" class="hidden space-y-4">
            {{-- Nama Barang --}}
            <div>
                <label class="text-caption font-bold text-steel">Nama Barang Baru <span class="text-critical">*</span></label>
                <input type="text" name="items[INDEX][name]" class="form-input" placeholder="Contoh: Laptop Asus">
            </div>

            {{-- Kategori + Satuan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-caption font-bold text-steel">Kategori <span class="text-critical">*</span></label>
                    <select name="items[INDEX][category_id]" class="form-input">
                        <option value="">-- Pilih --</option>
                        @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-caption font-bold text-steel">Satuan <span class="text-critical">*</span></label>
                    <select name="items[INDEX][unit_id]" class="form-input">
                        <option value="">-- Pilih --</option>
                        @foreach($units as $u) <option value="{{ $u->id }}">{{ $u->name }}</option> @endforeach
                    </select>
                </div>
            </div>

            {{-- Lokasi + Jumlah --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-caption font-bold text-steel">Lokasi Awal</label>
                    <select name="items[INDEX][location_id]" class="form-input">
                        <option value="">-- Pilih --</option>
                        @foreach($locations as $loc) <option value="{{ $loc->id }}">{{ $loc->name }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-caption font-bold text-steel">Jumlah Diterima <span class="text-critical">*</span></label>
                    <input type="number" name="items[INDEX][quantity]" min="1" value="1" class="form-input">
                </div>
            </div>

            {{-- Tipe Barang (Radio Card) --}}
            <div>
                <label class="text-caption font-bold text-steel mb-2 block">Tipe Barang</label>
                <div class="grid grid-cols-2 gap-3">
                    {{-- Aset --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="items[INDEX][item_type]" value="asset" checked 
                               onchange="toggleMinStock(this, INDEX)" class="item-type-input-INDEX sr-only">
                        <div class="item-type-card-INDEX p-3 rounded-xl border-2 border-primary bg-primary/5 text-center transition-all">
                            <i class="fas fa-box text-primary mb-1"></i>
                            <p class="text-xs font-bold text-ink-deep">Aset Tetap</p>
                        </div>
                    </label>
                    {{-- BHP --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="items[INDEX][item_type]" value="consumable" 
                               onchange="toggleMinStock(this, INDEX)" class="item-type-input-INDEX sr-only">
                        <div class="item-type-card-INDEX p-3 rounded-xl border-2 border-hairline-soft text-center transition-all">
                            <i class="fas fa-boxes text-steel mb-1"></i>
                            <p class="text-xs font-bold text-ink-deep">Habis Pakai</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Min Stok (Khusus BHP) --}}
            <div id="min-stock-wrapper-INDEX" class="hidden">
                <label class="text-caption font-bold text-steel">Minimal Stok (BHP)</label>
                <input type="number" name="items[INDEX][min_stock]" min="0" value="0" class="form-input" placeholder="Batas notifikasi stok menipis">
            </div>
        </div>

        {{-- Harga Satuan --}}
        <div class="mt-4 pt-3 border-t border-hairline-soft">
            <label class="text-caption font-bold text-steel">Harga Satuan (Rp)</label>
            <input type="number" name="items[INDEX][price]" min="0" step="1000" value="0" class="form-input">
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
    let index = 0;

    function addRow() {
        const container = document.getElementById('items-container');
        const template = document.getElementById('row-template').innerHTML;
        
        // Ganti INDEX dengan index unik
        const html = template.replace(/INDEX/g, index);
        container.insertAdjacentHTML('beforeend', html);
        
        index++;
    }

    function toggleMode(radio, idx) {
        const existingDiv = document.getElementById(`existing-${idx}`);
        const newDiv = document.getElementById(`new-${idx}`);

        if (radio.value === 'existing') {
            existingDiv.classList.remove('hidden');
            newDiv.classList.add('hidden');
            // Hapus required attributes dari form new
            newDiv.querySelectorAll('select, input').forEach(el => el.removeAttribute('required'));
        } else {
            existingDiv.classList.add('hidden');
            newDiv.classList.remove('hidden');
            // Hapus required attributes dari form existing
            existingDiv.querySelectorAll('select, input').forEach(el => el.removeAttribute('required'));
        }
    }

        // Fungsi untuk mengubah warna card Aset/BHP dan memunculkan Min Stok
    function toggleMinStock(radio, idx) {
        const wrapper = document.getElementById(`min-stock-wrapper-${idx}`);
        const cards = document.querySelectorAll(`.item-type-input-${idx}`);
        
        cards.forEach(input => {
            const card = input.closest('label').querySelector(`.item-type-card-${idx}`);
            const icon = card.querySelector('i');
            
            if (input.checked) {
                // Card Terpilih
                card.className = `item-type-card-${idx} p-3 rounded-xl border-2 text-center transition-all ${input.value === 'consumable' ? 'border-warning bg-warning/5' : 'border-primary bg-primary/5'}`;
                icon.className = `mb-1 ${input.value === 'consumable' ? 'fas fa-boxes text-warning' : 'fas fa-box text-primary'}`;
            } else {
                // Card Tidak Terpilih
                card.className = `item-type-card-${idx} p-3 rounded-xl border-2 border-hairline-soft text-center transition-all`;
                icon.className = `mb-1 ${input.value === 'consumable' ? 'fas fa-boxes text-steel' : 'fas fa-box text-steel'}`;
            }
        });

        // Tampilkan/Sembunyikan Min Stok
        if (radio.value === 'consumable') {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
        }
    }

    // Init 1 row
    document.addEventListener('DOMContentLoaded', () => addRow());
</script>
@endpush