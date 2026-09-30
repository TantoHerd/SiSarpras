{{-- resources/views/locations/show.blade.php --}}
@extends('layouts.app')

@section('title', $location->name)
@section('page_title', 'Detail Lokasi')
@section('page_subtitle', 'Informasi dan barang dalam lokasi ini')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('locations.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-map-marker-alt text-xs"></i> Lokasi Barang
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    @if($location->parent)
        <a href="{{ route('locations.show', $location->parent->id) }}" class="hover:text-primary">
            {{ $location->parent->name }}
        </a>
        <i class="fas fa-chevron-right text-xs text-stone"></i>
    @endif
    <span class="text-ink-deep">{{ $location->name }}</span>
</nav>

<div class="space-y-6">

    {{-- ============ HERO CARD ============ --}}
    <div class="card">
        <div class="flex flex-wrap items-start gap-6">

            {{-- Icon Besar --}}
            <div class="w-20 h-20 rounded-2xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-map-marker-alt text-primary text-3xl"></i>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-[250px]">
                <div class="flex items-center gap-3 flex-wrap mb-2">
                    <h1 class="text-heading-lg text-ink-deep">{{ $location->name }}</h1>
                    @if($location->floor)
                        <span class="badge-info">
                            <i class="fas fa-building mr-1.5 text-xs"></i>
                            {{ $location->floor }}
                        </span>
                    @endif
                </div>
                <p class="text-body-md text-steel mb-4">
                    {{ $location->description ?? 'Tidak ada deskripsi' }}
                </p>

                <div class="flex flex-wrap items-center gap-3">
                    <span class="badge-primary">
                        <i class="fas fa-boxes mr-1.5 text-xs"></i>
                        {{ $location->items_count ?? $location->items()->count() }} barang
                    </span>
                    @if($location->children->count() > 0)
                        <span class="badge-neutral">
                            <i class="fas fa-sitemap mr-1.5 text-xs"></i>
                            {{ $location->children->count() }} sub-lokasi
                        </span>
                    @endif
                    <span class="badge-neutral">
                        <i class="fas fa-calendar mr-1.5 text-xs"></i>
                        Dibuat {{ $location->created_at->format('d M Y') }}
                    </span>
                </div>
            </div>

            {{-- Aksi --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('locations.edit', $location->id) }}" class="btn-primary">
                    <i class="fas fa-pen"></i>
                    Edit
                </a>
                <button type="button"
                        onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="btn-ghost">
                    <i class="fas fa-trash text-critical"></i>
                </button>
            </div>

        </div>
    </div>

    {{-- ============ SUB-LOKASI (kalau ada) ============ --}}
    @if($location->children->count() > 0)
        <div class="card">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-body-md-bold text-ink-deep">Sub-Lokasi</h3>
                    <p class="text-caption text-steel">Lokasi yang berada di bawah {{ $location->name }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($location->children as $child)
                    <a href="{{ route('locations.show', $child->id) }}" 
                       class="flex items-center gap-3 p-3 rounded-xl bg-surface-soft hover:bg-primary/5 border border-transparent hover:border-primary/20 transition-all">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-primary text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-body-sm-bold text-ink-deep truncate">{{ $child->name }}</p>
                            @if($child->floor)
                                <p class="text-caption text-steel">{{ $child->floor }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============ DAFTAR BARANG ============ --}}
    <div class="card !p-0 overflow-hidden">

        <div class="px-5 py-4 border-b border-hairline-soft flex items-center justify-between">
            <div>
                <h3 class="text-body-md-bold text-ink-deep">Barang dalam Lokasi Ini</h3>
                <p class="text-caption text-steel">
                    Menampilkan 10 barang terbaru
                </p>
            </div>
            @if($location->items_count > 10)
                <a href="{{ route('items.index', ['location_id' => $location->id]) }}" 
                   class="text-body-sm-bold text-primary hover:text-primary-deep inline-flex items-center gap-1.5">
                    Lihat semua
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            @endif
        </div>

        @if($items->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Barang</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Kategori</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Kondisi</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($items as $item)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($item->image)
                                            <img src="{{ asset('storage/' . $item->image) }}" 
                                                 alt="{{ $item->name }}"
                                                 class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-box text-steel text-sm"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-body-sm-bold text-ink-deep truncate max-w-[250px]">
                                                {{ $item->name }}
                                            </p>
                                            <p class="text-caption text-steel font-mono">{{ $item->code }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <span class="badge-neutral">{{ $item->category->name ?? '-' }}</span>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @php
                                        $conditionColor = match($item->condition->value) {
                                            'baik' => 'bg-success/10 text-success',
                                            'rusak_ringan' => 'bg-warning/10 text-warning',
                                            'rusak_berat' => 'bg-critical/10 text-critical',
                                            default => 'bg-surface-soft text-steel',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-pill text-caption-bold {{ $conditionColor }}">
                                        {{ $item->condition->label() }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('items.show', $item->id) }}" 
                                       class="btn-icon !w-9 !h-9 hover:!bg-primary/10 hover:!text-primary" 
                                       title="Lihat">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-16 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-circle bg-surface-soft mb-4">
                    <i class="fas fa-box-open text-steel text-2xl"></i>
                </div>
                <h4 class="text-body-md-bold text-ink-deep mb-2">Belum Ada Barang</h4>
                <p class="text-body-sm text-steel mb-4">
                    Lokasi ini belum memiliki barang terdaftar.
                </p>
                <a href="{{ route('items.create', ['location_id' => $location->id]) }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Tambah Barang
                </a>
            </div>
        @endif
    </div>

</div>

{{-- ==================== MODAL DELETE ==================== --}}
<div id="delete-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus Lokasi?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Lokasi <strong class="text-ink-deep">{{ $location->name }}</strong> akan dihapus.
        </p>
        <form method="POST" action="{{ route('locations.destroy', $location->id) }}" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-danger flex-1">
                <i class="fas fa-trash"></i> Ya, Hapus
            </button>
        </form>
    </div>
</div>

@endsection