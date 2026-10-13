{{-- resources/views/stock-requests/my.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Permintaan Saya')
@section('page_title', 'Riwayat Permintaan BHP')
@section('page_subtitle', 'Daftar semua permintaan BHP yang pernah Anda ajukan')

@section('content')

<div class="space-y-6">

    {{-- ==================== HEADER ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $requests->total() }}</span> permintaan
            </p>
        </div>
        <a href="{{ route('stock-requests.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            Permintaan Baru
        </a>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('stock-requests.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nomor permintaan atau keperluan..."
                       class="form-input pl-11">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('stock-requests.index') }}" class="btn-ghost" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Pill Tabs Status --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Status:</span>
            
            <a href="{{ route('stock-requests.index', array_merge(request()->query(), ['status' => ''])) }}"
               class="pill-tab {{ empty($filters['status']) ? 'pill-tab-active' : '' }}">
                Semua
            </a>
            <a href="{{ route('stock-requests.index', array_merge(request()->query(), ['status' => 'pending'])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === 'pending' ? 'pill-tab-active' : '' }}">
                Pending
            </a>
            <a href="{{ route('stock-requests.index', array_merge(request()->query(), ['status' => 'approved'])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === 'approved' ? 'pill-tab-active' : '' }}">
                Disetujui
            </a>
            <a href="{{ route('stock-requests.index', array_merge(request()->query(), ['status' => 'rejected'])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === 'rejected' ? 'pill-tab-active' : '' }}">
                Ditolak
            </a>
        </div>
    </div>

    {{-- ==================== LIST ==================== --}}
    @if($requests->count() > 0)
        <div class="space-y-3">
            @foreach($requests as $req)
                <div class="card hover:border-primary/30 transition-colors">
                    <div class="flex flex-wrap items-center gap-4">

                        {{-- Icon --}}
                        <div class="w-14 h-14 rounded-xl bg-warning/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-boxes text-warning text-xl"></i>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-[200px]">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <h4 class="text-body-md-bold text-ink-deep font-mono">
                                    {{ $req->request_number }}
                                </h4>
                                <span class="{{ $req->status_badge }}">{{ $req->status_label }}</span>
                            </div>
                            <p class="text-body-sm text-steel truncate">
                                {{ Str::limit($req->purpose, 80) }}
                            </p>
                            <p class="text-caption text-steel mt-1">
                                <i class="fas fa-cube text-xs"></i>
                                {{ $req->items->count() }} jenis barang,
                                {{ $req->items->sum('quantity_requested') }} unit total
                                <span class="text-stone mx-1">•</span>
                                {{ $req->created_at->translatedFormat('d M Y, H:i') }}
                            </p>
                        </div>

                        {{-- Action --}}
                        <div class="flex items-center gap-2">
                            <a href="{{ route('stock-requests.show', $req->id) }}" class="btn-ghost btn-sm">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($requests->hasPages())
            <div class="card">
                {{ $requests->links() }}
            </div>
        @endif

    @else
        <div class="card py-16 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                <i class="fas fa-inbox text-steel text-3xl"></i>
            </div>
            <h3 class="text-heading-sm text-ink-deep mb-2">
                @if(array_filter($filters))
                    Tidak Ada Hasil
                @else
                    Belum Ada Permintaan
                @endif
            </h3>
            <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                @if(array_filter($filters))
                    Tidak ada permintaan yang cocok dengan filter.
                @else
                    Anda belum pernah mengajukan permintaan BHP.
                @endif
            </p>
            @if(array_filter($filters))
                <a href="{{ route('stock-requests.index') }}" class="btn-ghost">
                    <i class="fas fa-times"></i> Reset Filter
                </a>
            @else
                <a href="{{ route('stock-requests.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Buat Permintaan Pertama
                </a>
            @endif
        </div>
    @endif

</div>

@endsection