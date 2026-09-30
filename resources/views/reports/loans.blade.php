{{-- resources/views/reports/loans.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Peminjaman')
@section('page_title', 'Laporan Peminjaman Barang')
@section('page_subtitle', 'Preview & ekspor laporan peminjaman')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('reports.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-chart-bar text-xs"></i> Laporan
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Peminjaman</span>
</nav>

<div class="space-y-6">

    {{-- ==================== HEADER + ACTION ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-heading-lg text-ink-deep">Laporan Peminjaman</h1>
            <p class="text-body-sm text-steel mt-1">
                {{ $loans->count() }} peminjaman ditampilkan
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reports.loans.pdf', request()->query()) }}" 
               class="btn-danger">
                <i class="fas fa-file-pdf"></i>
                Cetak PDF
            </a>
            <a href="{{ route('reports.loans.excel', request()->query()) }}" 
               class="btn-primary">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </a>
        </div>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('reports.loans') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="text-caption-bold text-steel mb-1 block">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-input">
            </div>
            <div>
                <label class="text-caption-bold text-steel mb-1 block">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-input">
            </div>
            <div>
                <label class="text-caption-bold text-steel mb-1 block">Status</label>
                <select name="status" class="form-input">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\LoanStatusEnum::cases() as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('reports.loans') }}" class="btn-ghost" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ==================== RINGKASAN ==================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Total</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($summary['total']) }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Dipinjam</p>
            <p class="text-heading-lg text-primary">{{ number_format($summary['borrowed']) }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Terlambat</p>
            <p class="text-heading-lg text-critical">{{ number_format($summary['overdue']) }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Dikembalikan</p>
            <p class="text-heading-lg text-success">{{ number_format($summary['returned']) }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Total Denda</p>
            <p class="text-body-md-bold text-critical">{{ format_currency($summary['total_fines']) }}</p>
        </div>
    </div>

    {{-- ==================== TABEL PREVIEW ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($loans->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-center px-3 py-3 text-caption-bold text-steel uppercase tracking-wider">No</th>
                            <th class="text-left px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Barang</th>
                            <th class="text-left px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Peminjam</th>
                            <th class="text-center px-4 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Tgl Pinjam</th>
                            <th class="text-center px-4 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Jatuh Tempo</th>
                            <th class="text-center px-4 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Tgl Kembali</th>
                            <th class="text-center px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Status</th>
                            <th class="text-right px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Denda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($loans as $index => $loan)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                <td class="text-center px-3 py-3 text-body-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-body-sm-bold text-ink-deep">{{ $loan->item->name ?? '-' }}</p>
                                    <p class="text-caption text-steel font-mono">{{ $loan->item->code ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-body-sm text-ink">{{ $loan->borrower->name ?? '-' }}</p>
                                    @if($loan->borrower && $loan->borrower->nip)
                                        <p class="text-caption text-steel">{{ $loan->borrower->nip }}</p>
                                    @endif
                                </td>
                                <td class="text-center px-4 py-3 text-body-sm hidden lg:table-cell">
                                    {{ $loan->loan_date->format('d M Y') }}
                                </td>
                                <td class="text-center px-4 py-3 hidden lg:table-cell">
                                    <span class="text-body-sm">{{ $loan->due_date->format('d M Y') }}</span>
                                    @if($loan->isOverdue() && !$loan->isReturned())
                                        <p class="text-caption text-critical font-bold">
                                            +{{ $loan->daysOverdue() }} hari
                                        </p>
                                    @endif
                                </td>
                                <td class="text-center px-4 py-3 text-body-sm hidden lg:table-cell">
                                    {{ $loan->return_date ? $loan->return_date->format('d M Y') : '-' }}
                                </td>
                                <td class="text-center px-4 py-3">
                                    @php
                                        $statusColor = match($loan->status->value) {
                                            'dipinjam' => 'badge-info',
                                            'terlambat' => 'badge-critical',
                                            'dikembalikan' => 'badge-success',
                                            default => 'badge-neutral',
                                        };
                                    @endphp
                                    <span class="{{ $statusColor }}">{{ $loan->status->label() }}</span>
                                </td>
                                <td class="text-right px-4 py-3">
                                    @if($loan->fine_amount > 0)
                                        <span class="text-body-sm-bold text-critical">
                                            {{ number_format($loan->fine_amount, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-caption text-stone">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-ink-deep text-white">
                        <tr>
                            <td colspan="7" class="text-right px-4 py-4 text-body-md-bold uppercase tracking-wider">
                                Total Denda
                            </td>
                            <td class="text-right px-4 py-4 text-body-md-bold">
                                {{ format_currency($summary['total_fines']) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-hand-holding text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">Tidak Ada Data</h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    Tidak ada peminjaman untuk periode yang dipilih.
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('reports.loans') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>

@endsection