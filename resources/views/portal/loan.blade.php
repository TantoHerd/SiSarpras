{{-- resources/views/portal/loan.blade.php --}}
@extends('portal.layouts.app')

@section('title', 'Peminjaman Barang')

@section('content')

<div class="max-w-4xl mx-auto py-8 px-4">

    {{-- Back Button --}}
    <a href="{{ route('portal.index') }}" class="inline-flex items-center text-steel hover:text-primary mb-6 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Portal
    </a>

    <div class="card p-6 md:p-8">
        <div class="flex items-center gap-4 mb-8 border-b border-hairline-soft pb-6">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
                <i class="fas fa-hand-holding text-primary text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-ink-deep">Peminjaman Barang</h1>
                <p class="text-steel text-sm">Ajukan peminjaman, tunggu approval dari petugas.</p>
            </div>
        </div>

        {{-- TAMBAHKAN INI DI ATAS FORM --}}
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

        <form method="POST" action="{{ route('portal.loan.store') }}" id="loan-form" class="space-y-6">
            @csrf

            {{-- Identitas --}}
            @include('portal.partials.identity-autofill', ['type' => 'all'])

            {{-- Daftar Barang --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-ink-deep">Barang yang Dipinjam</h3>
                    <button type="button" onclick="addLoanItem()" class="btn-ghost !py-2 !px-4 !text-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </button>
                </div>

                <div id="loan-items" class="space-y-4"></div>
                <div id="loan-empty" class="text-center py-8 border-2 border-dashed border-hairline-soft rounded-xl">
                    <p class="text-steel">Belum ada barang dipilih.</p>
                </div>
            </div>

            {{-- Keperluan --}}
            <div>
                <label class="form-label">Keperluan Peminjaman <span class="text-critical">*</span></label>
                <textarea name="purpose" rows="3" required class="form-input !h-auto resize-none" placeholder="Contoh: Untuk presentasi di aula..."></textarea>
            </div>

            {{-- Submit --}}
            <div class="pt-4 border-t border-hairline-soft flex justify-end gap-3">
                <a href="{{ route('portal.index') }}" class="btn-ghost">Batal</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane mr-2"></i> Ajukan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Template Item Row --}}
<template id="loan-item-template">
    <div class="loan-item p-4 rounded-xl border border-hairline-soft bg-surface-soft/50">
        <div class="flex items-start gap-4">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-6">
                    <label class="text-caption text-steel mb-1 block">Barang</label>
                    <select name="items[INDEX][item_id]" required class="form-input select-item">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-stock="{{ $item->quantity }}">
                                {{ $item->name }} (Stok: {{ $item->quantity }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="text-caption text-steel mb-1 block">Jumlah</label>
                    <input type="number" name="items[INDEX][quantity]" min="1" value="1" required class="form-input input-qty">
                </div>
                <div class="md:col-span-3">
                    <label class="text-caption text-steel mb-1 block">Catatan</label>
                    <input type="text" name="items[INDEX][note]" placeholder="Opsional" class="form-input">
                </div>
            </div>
            <button type="button" onclick="this.closest('.loan-item').remove(); checkEmpty();" 
                    class="btn-icon hover:bg-critical/10 hover:text-critical mt-5">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
    let index = 0;

    function addLoanItem() {
        const container = document.getElementById('loan-items');
        const template = document.getElementById('loan-item-template').innerHTML;
        
        const html = template.replace(/INDEX/g, index++);
        container.insertAdjacentHTML('beforeend', html);
        
        document.getElementById('loan-empty').classList.add('hidden');
    }

    function checkEmpty() {
        const container = document.getElementById('loan-items');
        if (container.children.length === 0) {
            document.getElementById('loan-empty').classList.remove('hidden');
        }
    }

    // Init 1 row
    document.addEventListener('DOMContentLoaded', () => addLoanItem());
</script>
@endpush