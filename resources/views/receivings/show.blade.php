{{-- resources/views/receivings/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Penerimaan #' . $receiving->receiving_number)

@section('content')

<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('receivings.index') }}" class="hover:text-primary flex items-center gap-1.5"><i class="fas fa-truck-loading text-xs"></i> Penerimaan</a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep font-mono">{{ $receiving->receiving_number }}</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-6">
        <div class="card">
            <h3 class="text-body-md-bold text-ink-deep mb-4"><i class="fas fa-info-circle text-primary mr-2"></i> Info Penerimaan</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-caption text-steel">No. Terima</p>
                    <p class="text-body-sm-bold text-ink-deep font-mono">{{ $receiving->receiving_number }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Tanggal Terima</p>
                    <p class="text-body-sm-bold text-ink-deep">{{ $receiving->receiving_date->translatedFormat('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Supplier</p>
                    <p class="text-body-sm-bold text-ink-deep">{{ $receiving->supplier->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Sumber Dana</p>
                    <p class="text-body-sm-bold text-ink-deep">{{ $receiving->fundingSource->code ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Invoice</p>
                    <p class="text-body-sm text-ink">{{ $receiving->invoice_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Dicatat Oleh</p>
                    <p class="text-body-sm text-ink">{{ $receiving->creator->name ?? '-' }}</p>
                </div>
                @if($receiving->note)
                <div>
                    <p class="text-caption text-steel">Catatan</p>
                    <p class="text-body-sm text-ink">{{ $receiving->note }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="card !p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-hairline-soft bg-surface-soft">
                <h3 class="text-body-md-bold text-ink-deep">Barang yang Diterima</h3>
            </div>
                        <div class="divide-y divide-hairline-soft">
                @foreach($receiving->items as $ri)
                    @php
                        // Cek apakah barang ini baru dibuat pada penerimaan ini
                        // (Bandingkan waktu pembuatan barang dengan waktu penerimaan)
                        $isNewItem = $ri->item && $ri->item->created_at >= $receiving->created_at;
                    @endphp
                    
                    <div class="p-5 flex flex-col md:flex-row md:items-center gap-4">
                        {{-- Ikon --}}
                        <div class="w-12 h-12 rounded-lg {{ $isNewItem ? 'bg-warning/10' : 'bg-primary/10' }} flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ $isNewItem ? 'fa-star text-warning' : 'fa-box text-primary' }}"></i>
                        </div>

                        {{-- Info Barang --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <p class="text-body-sm-bold text-ink-deep">{{ $ri->item->name ?? '-' }}</p>
                                @if($isNewItem)
                                    <span class="badge-warning text-xs">Barang Baru</span>
                                @endif
                            </div>
                            <p class="text-caption text-steel font-mono">{{ $ri->item->code ?? '-' }}</p>
                            
                            {{-- Tombol Lengkapi Data (Hanya untuk Barang Baru) --}}
                            @if($isNewItem)
                                <div class="mt-3">
                                    <a href="{{ route('items.edit', $ri->item->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary-deep transition-colors">
                                        <i class="fas fa-edit"></i> Lengkapi Detail (Foto/Spesifikasi)
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Jumlah --}}
                        <div class="text-left md:text-right">
                            <p class="text-caption text-steel">Jumlah Diterima</p>
                            <p class="text-heading-sm text-primary font-bold">
                                +{{ $ri->quantity }} {{ $ri->item->unit->name ?? '' }}
                            </p>
                        </div>

                        {{-- Harga --}}
                        <div class="text-left md:text-right border-t md:border-t-0 md:border-l border-hairline-soft pt-3 md:pt-0 md:pl-4">
                            <p class="text-caption text-steel">Harga Satuan</p>
                            <p class="text-body-sm font-bold">{{ format_currency($ri->price) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('receivings.index') }}" class="btn-ghost">Kembali ke Daftar</a>
        </div>
    </div>
</div>

@endsection