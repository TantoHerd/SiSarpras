{{-- resources/views/receivings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Penerimaan Barang')
@section('page_title', 'Penerimaan Barang')
@section('page_subtitle', 'Catat barang masuk dari supplier / pembelian')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $receivings->total() }}</span> penerimaan tercatat
            </p>
        </div>
        <a href="{{ route('receivings.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Penerimaan Baru
        </a>
    </div>

    {{-- Filter --}}
    <div class="card">
        <form method="GET" action="{{ route('receivings.index') }}" class="flex gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Terima, Invoice, atau Supplier..." class="form-input pl-11">
            </div>
            <button type="submit" class="btn-ink"><i class="fas fa-search"></i> Cari</button>
            @if($search)
                <a href="{{ route('receivings.index') }}" class="btn-ghost"><i class="fas fa-times"></i></a>
            @endif
        </form>
    </div>

    {{-- Tabel --}}
    <div class="card !p-0 overflow-hidden">
        @if($receivings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">No. Terima</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Supplier</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Total Item</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Tgl Terima</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($receivings as $rcv)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="text-body-sm-bold text-ink-deep font-mono">{{ $rcv->receiving_number }}</p>
                                    <p class="text-caption text-steel">{{ $rcv->invoice_number ? 'INV: '.$rcv->invoice_number : 'Tanpa Invoice' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-body-sm-bold text-ink-deep">{{ $rcv->supplier->name ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="badge-info">{{ $rcv->items_count }} jenis</span>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-body-sm text-ink">{{ $rcv->receiving_date->format('d M Y') }}</p>
                                    <p class="text-caption text-steel">Oleh: {{ $rcv->creator->name ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('receivings.show', $rcv->id) }}" class="btn-icon !w-9 !h-9 hover:!bg-primary/10 hover:!text-primary" title="Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($receivings->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">{{ $receivings->links() }}</div>
            @endif
        @else
            <div class="py-16 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-truck-loading text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">Belum Ada Penerimaan</h3>
                <p class="text-subtitle-md text-steel mb-6">Mulai catat barang masuk dari supplier.</p>
                <a href="{{ route('receivings.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Penerimaan Baru</a>
            </div>
        @endif
    </div>

</div>

@endsection