{{-- resources/views/portal/bhp.blade.php --}}
@extends('portal.layouts.app')

@section('title', 'Permintaan BHP')

@section('content')

<div class="max-w-3xl mx-auto py-8 px-4">

    {{-- Back Button --}}
    <a href="{{ route('portal.index') }}" class="inline-flex items-center text-steel hover:text-primary mb-6 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Portal
    </a>

    <div class="card p-6 md:p-8">
        <div class="flex items-center gap-4 mb-8 border-b border-hairline-soft pb-6">
            <div class="w-12 h-12 rounded-xl bg-warning/10 flex items-center justify-center">
                <i class="fas fa-boxes text-warning text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-ink-deep">Permintaan BHP</h1>
                <p class="text-steel text-sm">Barang habis pakai langsung diambil, stok otomatis berkurang.</p>
            </div>
        </div>

        {{-- TAMPILKAN ERROR --}}
        @if($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-critical/10 border border-critical/20">
                <p class="text-body-sm-bold text-critical mb-2">Terjadi kesalahan:</p>
                <ul class="list-disc list-inside text-body-sm text-ink space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-xl bg-critical/10 border border-critical/20">
                <p class="text-body-sm text-ink">{{ session('error') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('portal.bhp.store') }}" id="bhp-form" class="space-y-6">
            @csrf

            {{-- Identitas --}}
            @include('portal.partials.identity-autofill', ['type' => 'all'])

            {{-- Daftar Barang --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-ink-deep">Barang yang Diminta</h3>
                    <button type="button" onclick="addBhpItem()" class="btn-ghost !py-2 !px-4 !text-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </button>
                </div>

                <div id="bhp-items" class="space-y-4"></div>
                <div id="bhp-empty" class="text-center py-8 border-2 border-dashed border-hairline-soft rounded-xl hidden">
                    <p class="text-steel">Belum ada barang dipilih.</p>
                </div>
            </div>

            {{-- Keperluan --}}
            <div>
                <label class="form-label">Keperluan <span class="text-critical">*</span></label>
                <textarea name="purpose" rows="3" required class="form-input !h-auto resize-none" placeholder="Contoh: Untuk keperluan ATK kelas X..."></textarea>
            </div>

            {{-- Submit --}}
            <div class="pt-4 border-t border-hairline-soft flex justify-end gap-3">
                <a href="{{ route('portal.index') }}" class="btn-ghost">Batal</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Permintaan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Template Item Row --}}
<template id="bhp-item-template">
    <div class="bhp-item p-4 rounded-xl border border-hairline-soft bg-surface-soft/50">
        <div class="flex items-start gap-4">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-8">
                    <label class="text-caption text-steel mb-1 block">Barang</label>
                    <select name="items[INDEX][item_id]" required class="form-input select-item" onchange="checkStock(this)">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" 
                                    data-stock="{{ $item->quantity }}"
                                    {{ $item->quantity <= $item->min_stock ? 'disabled' : '' }}>
                                {{ $item->name }} (Stok: {{ $item->quantity }})
                                {{ $item->quantity <= $item->min_stock ? ' - HABIS' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="stock-info text-caption text-steel mt-1"></p>
                </div>
                <div class="md:col-span-4">
                    <label class="text-caption text-steel mb-1 block">Jumlah</label>
                    <input type="number" name="items[INDEX][quantity]" min="1" value="1" required class="form-input input-qty">
                </div>
            </div>
            <button type="button" onclick="removeBhpItem(this)" 
                    class="btn-icon hover:bg-critical/10 hover:text-critical mt-5">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
    let bhpIndex = 0;

    // Fungsi untuk menambah baris barang
    function addBhpItem() {
        const container = document.getElementById('bhp-items');
        const template = document.getElementById('bhp-item-template').innerHTML;
        
        // Ganti INDEX dengan angka unik
        const html = template.replace(/INDEX/g, bhpIndex++);
        container.insertAdjacentHTML('beforeend', html);
        
        document.getElementById('bhp-empty').classList.add('hidden');
    }

    // Fungsi untuk menghapus baris barang
    function removeBhpItem(btn) {
        btn.closest('.bhp-item').remove();
        checkEmpty();
    }

    // Cek apakah container kosong
    function checkEmpty() {
        const container = document.getElementById('bhp-items');
        if (container.children.length === 0) {
            document.getElementById('bhp-empty').classList.remove('hidden');
        }
    }

    // Fungsi update info stok
    function checkStock(select) {
        const selected = select.options[select.selectedIndex];
        const stock = selected.dataset.stock;
        const stockInfo = select.closest('div').querySelector('.stock-info');
        const qtyInput = select.closest('.bhp-item').querySelector('.input-qty');
        
        if (stock) {
            stockInfo.innerHTML = `<i class="fas fa-info-circle text-primary"></i> Stok tersedia: <strong>${stock}</strong>`;
            qtyInput.max = stock;
            qtyInput.value = 1;
        } else {
            stockInfo.textContent = '';
            qtyInput.max = '';
        }
    }

    // Init 1 row saat halaman selesai load
    document.addEventListener('DOMContentLoaded', function() {
        addBhpItem();
    });
</script>
@endpush