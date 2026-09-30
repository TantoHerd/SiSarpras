{{-- resources/views/loans/return.blade.php --}}
@extends('layouts.app')

@section('title', 'Proses Pengembalian')
@section('page_title', 'Proses Pengembalian')
@section('page_subtitle', 'Konfirmasi pengembalian barang')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('loans.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-hand-holding text-xs"></i> Peminjaman
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <a href="{{ route('loans.show', $loan->id) }}" class="hover:text-primary">
        #{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Kembalikan</span>
</nav>

<form method="POST" action="{{ route('loans.return.store', $loan->id) }}">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Preview Barang ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4">Barang yang Dikembalikan</h3>

                {{-- Foto --}}
                <div class="aspect-square rounded-xl bg-surface-soft overflow-hidden mb-4">
                    @if($loan->item->image)
                        <img src="{{ asset('storage/' . $loan->item->image) }}" 
                             alt="{{ $loan->item->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-box text-steel text-5xl"></i>
                        </div>
                    @endif
                </div>

                {{-- Nama --}}
                <h4 class="text-body-md-bold text-ink-deep mb-1">{{ $loan->item->name }}</h4>
                <p class="text-caption text-steel font-mono mb-4">{{ $loan->item->code }}</p>

                {{-- Info --}}
                <div class="space-y-2 pt-4 border-t border-hairline-soft">
                    <div class="flex items-center justify-between text-body-sm">
                        <span class="text-steel">Peminjam</span>
                        <span class="text-body-sm-bold text-ink-deep">
                            {{ $loan->borrower->name ?? '-' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-body-sm">
                        <span class="text-steel">Tgl Pinjam</span>
                        <span class="text-body-sm-bold text-ink-deep">
                            {{ $loan->loan_date->format('d M Y') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-body-sm">
                        <span class="text-steel">Jatuh Tempo</span>
                        <span class="text-body-sm-bold text-ink-deep">
                            {{ $loan->due_date->format('d M Y') }}
                        </span>
                    </div>
                </div>

                {{-- Status Terlambat --}}
                @if($loan->isOverdue())
                    <div class="mt-4 p-3 rounded-xl bg-critical/10 border border-critical/20">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle text-critical mt-0.5 text-sm"></i>
                            <div>
                                <p class="text-caption-bold text-critical">Terlambat!</p>
                                <p class="text-caption text-ink-deep mt-0.5">
                                    Terlambat {{ $loan->daysOverdue() }} hari dari jatuh tempo
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- ==================== KOLOM KANAN: Form ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ============ INFO DENDA ============ --}}
            @if($finePreview['is_active'] && $finePreview['total'] > 0)
                <div class="card bg-critical/5 border-critical/20">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg bg-critical/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-money-bill-wave text-critical text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-body-md-bold text-ink-deep mb-1">Denda Keterlambatan</p>
                            <div class="space-y-1 text-body-sm text-steel">
                                <div class="flex justify-between">
                                    <span>Denda per hari</span>
                                    <span class="font-mono">{{ format_currency($finePreview['per_day']) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Jumlah hari terlambat</span>
                                    <span>{{ $finePreview['days'] }} hari</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center pt-3 mt-3 border-t border-critical/20">
                                <span class="text-body-sm-bold text-ink-deep">Total Denda</span>
                                <span class="text-heading-sm text-critical">
                                    {{ format_currency($finePreview['total']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ============ FORM ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-primary"></i>
                    Konfirmasi Pengembalian
                </h3>

                <div class="space-y-5">

                    {{-- Catatan Kondisi --}}
                    <div>
                        <label for="condition_note" class="form-label">
                            Catatan Kondisi Barang
                        </label>
                        <textarea id="condition_note" 
                                  name="condition_note" 
                                  rows="4"
                                  placeholder="Contoh: Barang dikembalikan dalam kondisi baik, tidak ada kerusakan..."
                                  class="form-input !h-auto resize-none @error('condition_note') form-input-error @enderror">{{ old('condition_note') }}</textarea>
                        <p class="text-caption text-steel mt-1">
                            Kosongkan jika barang dikembalikan dalam kondisi normal.
                        </p>
                        @error('condition_note')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Denda Dibayar (jika ada denda) --}}
                    @if($finePreview['is_active'] && $finePreview['total'] > 0)
                        <div class="p-4 rounded-xl bg-warning/10 border border-warning/20">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" 
                                       name="is_fine_paid" 
                                       value="1"
                                       @checked(old('is_fine_paid'))
                                       class="mt-1 w-4 h-4 rounded border-hairline text-primary focus:ring-primary">
                                <div>
                                    <p class="text-body-sm-bold text-ink-deep">
                                        Denda sudah dibayar
                                    </p>
                                    <p class="text-caption text-steel mt-1">
                                        Centang jika peminjam sudah membayar denda sebesar 
                                        <strong>{{ format_currency($finePreview['total']) }}</strong>.
                                    </p>
                                </div>
                            </label>
                        </div>
                    @endif

                    {{-- Info: Setelah submit --}}
                    <div class="p-4 rounded-xl bg-primary/5 border border-primary/20">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-primary mt-0.5"></i>
                            <div>
                                <p class="text-body-sm-bold text-ink-deep">Setelah submit</p>
                                <ul class="text-body-sm text-steel mt-1 space-y-1 list-disc list-inside">
                                    <li>Status peminjaman berubah menjadi <strong>Dikembalikan</strong></li>
                                    <li>Status barang kembali ke <strong>Tersedia</strong></li>
                                    <li>Data ini akan tercatat di riwayat sistem</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ============ ACTION ============ --}}
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('loans.show', $loan->id) }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-check-circle"></i>
                    Konfirmasi Pengembalian
                </button>
            </div>

        </div>
    </div>
</form>

@endsection