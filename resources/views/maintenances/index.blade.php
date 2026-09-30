{{-- resources/views/maintenances/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Perawatan Barang')
@section('page_title', 'Perawatan Barang')
@section('page_subtitle', 'Kelola riwayat perawatan dan perbaikan barang')

@section('content')

<div class="space-y-6">

    {{-- ==================== STATISTIK ==================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Total --}}
        <div class="card-sm">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-tools text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Total Perawatan</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['total']) }}</p>
        </div>

        {{-- Bulan Ini --}}
        <div class="card-sm">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-success"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Bulan Ini</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['this_month']) }}</p>
        </div>

        {{-- Rutin --}}
        <div class="card-sm">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-info/10 flex items-center justify-center">
                    <i class="fas fa-sync-alt text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Rutin</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['routine']) }}</p>
        </div>

        {{-- Perbaikan --}}
        <div class="card-sm">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                    <i class="fas fa-wrench text-warning"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Perbaikan</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['repair']) }}</p>
        </div>

        {{-- Sedang Diperbaiki --}}
        <div class="card-sm border-warning/30 bg-gradient-to-br from-warning/5 to-warning/0">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                    <i class="fas fa-clock text-warning"></i>
                </div>
                @if($stats['in_progress'] > 0)
                    <span class="badge-warning animate-pulse">Active</span>
                @endif
            </div>
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Sedang Diperbaiki</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['in_progress']) }}</p>
        </div>
    </div>

    {{-- ==================== CARD TOTAL BIAYA ==================== --}}
    @if($stats['total_cost'] > 0)
        <div class="card bg-gradient-to-br from-primary/5 to-primary/0 border-primary/20">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-primary text-xl"></i>
                    </div>
                    <div>
                        <p class="text-caption text-steel uppercase tracking-wider">Total Biaya Perawatan</p>
                        <p class="text-heading-md text-ink-deep">
                            {{ format_currency($stats['total_cost']) }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-caption text-steel uppercase tracking-wider">Bulan Ini</p>
                    <p class="text-body-md-bold text-primary">
                        {{ format_currency($stats['cost_this_month']) }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- ==================== HEADER + ACTION ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $maintenances->total() }}</span> perawatan tercatat
            </p>
        </div>
        <a href="{{ route('maintenances.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            Catat Perawatan
        </a>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('maintenances.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Search --}}
            <div class="lg:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari barang, teknisi, atau deskripsi..."
                       class="form-input pl-11">
            </div>

            {{-- Date From --}}
            <input type="date" 
                   name="date_from" 
                   value="{{ $filters['date_from'] ?? '' }}"
                   class="form-input">

            {{-- Submit --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('maintenances.index') }}" class="btn-ghost" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Pill Tabs Type --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Jenis:</span>
            <a href="{{ route('maintenances.index', array_merge(request()->query(), ['type' => ''])) }}"
               class="pill-tab {{ empty($filters['type']) ? 'pill-tab-active' : '' }}">
                Semua
            </a>
            <a href="{{ route('maintenances.index', array_merge(request()->query(), ['type' => 'rutin'])) }}"
               class="pill-tab {{ ($filters['type'] ?? '') === 'rutin' ? 'pill-tab-active' : '' }}">
                <i class="fas fa-sync-alt mr-1.5 text-xs"></i> Rutin
            </a>
            <a href="{{ route('maintenances.index', array_merge(request()->query(), ['type' => 'perbaikan'])) }}"
               class="pill-tab {{ ($filters['type'] ?? '') === 'perbaikan' ? 'pill-tab-active' : '' }}">
                <i class="fas fa-wrench mr-1.5 text-xs"></i> Perbaikan
            </a>
        </div>

        {{-- Pill Tabs Status --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Status:</span>
            <a href="{{ route('maintenances.index', array_merge(request()->query(), ['status' => ''])) }}"
            class="pill-tab {{ empty($filters['status']) ? 'pill-tab-active' : '' }}">
                Semua
            </a>
            <a href="{{ route('maintenances.index', array_merge(request()->query(), ['status' => 'in_progress'])) }}"
            class="pill-tab {{ ($filters['status'] ?? '') === 'in_progress' ? 'pill-tab-active' : '' }}">
                <i class="fas fa-clock mr-1.5 text-xs"></i> Sedang Diperbaiki
            </a>
            <a href="{{ route('maintenances.index', array_merge(request()->query(), ['status' => 'completed'])) }}"
            class="pill-tab {{ ($filters['status'] ?? '') === 'completed' ? 'pill-tab-active' : '' }}">
                <i class="fas fa-check mr-1.5 text-xs"></i> Selesai
            </a>
        </div>
    </div>

    {{-- ==================== TABEL MAINTENANCE ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($maintenances->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Barang</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Tanggal</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Jenis</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Teknisi</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Biaya</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($maintenances as $maintenance)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                {{-- Barang --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($maintenance->item->image)
                                            <img src="{{ asset('storage/' . $maintenance->item->image) }}" 
                                                 alt="{{ $maintenance->item->name }}"
                                                 class="w-12 h-12 rounded-lg object-cover bg-surface-soft flex-shrink-0">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-box text-steel"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-body-sm-bold text-ink-deep truncate max-w-[250px]">
                                                {{ $maintenance->item->name }}
                                            </p>
                                            <p class="text-caption text-steel font-mono">
                                                {{ $maintenance->item->code }}
                                            </p>
                                            @if($maintenance->description)
                                                <p class="text-caption text-steel truncate max-w-[250px] mt-0.5">
                                                    {{ Str::limit($maintenance->description, 50) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <p class="text-body-sm text-ink">
                                        {{ $maintenance->maintenance_date->format('d M Y') }}
                                    </p>
                                    <p class="text-caption text-steel">
                                        {{ $maintenance->created_at->diffForHumans() }}
                                    </p>
                                </td>

                                {{-- Jenis --}}
                                <td class="px-5 py-4 text-center">
                                    @if($maintenance->type === 'rutin')
                                        <span class="badge-info">
                                            <i class="fas fa-sync-alt mr-1 text-xs"></i>
                                            Rutin
                                        </span>
                                    @else
                                        <span class="badge-warning">
                                            <i class="fas fa-wrench mr-1 text-xs"></i>
                                            Perbaikan
                                        </span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">
                                    @if($maintenance->isInProgress())
                                        <span class="badge-warning">
                                            <i class="fas fa-clock mr-1 text-xs"></i>
                                            Sedang
                                        </span>
                                    @else
                                        <span class="badge-success">
                                            <i class="fas fa-check mr-1 text-xs"></i>
                                            Selesai
                                        </span>
                                    @endif
                                </td>

                                {{-- Teknisi --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    @if($maintenance->technician)
                                        <p class="text-body-sm text-ink">
                                            <i class="fas fa-user-cog text-steel text-xs mr-1"></i>
                                            {{ $maintenance->technician }}
                                        </p>
                                    @else
                                        <span class="text-caption text-stone">-</span>
                                    @endif
                                </td>

                                {{-- Biaya --}}
                                <td class="px-5 py-4 text-right">
                                    @if($maintenance->cost > 0)
                                        <p class="text-body-sm-bold text-ink-deep">
                                            {{ format_currency($maintenance->cost) }}
                                        </p>
                                    @else
                                        <span class="badge-success text-caption">
                                            Gratis
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('maintenances.show', $maintenance->id) }}" 
                                        class="btn-icon !w-9 !h-9 hover:!bg-primary/10 hover:!text-primary" 
                                        title="Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>

                                        @if($maintenance->isInProgress() && $maintenance->type === 'perbaikan')
                                            <button type="button"
                                                    onclick="openCompleteModal({{ $maintenance->id }}, '{{ addslashes($maintenance->item->name) }}', {{ $maintenance->cost }})"
                                                    class="btn-icon !w-9 !h-9 hover:!bg-success/10 hover:!text-success" 
                                                    title="Tandai Selesai">
                                                <i class="fas fa-check-circle text-sm"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('maintenances.edit', $maintenance->id) }}" 
                                            class="btn-icon !w-9 !h-9 hover:!bg-warning/10 hover:!text-warning" 
                                            title="Edit">
                                                <i class="fas fa-pen text-sm"></i>
                                            </a>
                                        @endif

                                        <button type="button"
                                                onclick="confirmDelete({{ $maintenance->id }}, '{{ addslashes($maintenance->item->name) }}')"
                                                class="btn-icon !w-9 !h-9 hover:!bg-critical/10 hover:!text-critical" 
                                                title="Hapus">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($maintenances->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">
                    {{ $maintenances->links() }}
                </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-tools text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">
                    @if(array_filter($filters))
                        Tidak Ada Hasil
                    @else
                        Belum Ada Perawatan
                    @endif
                </h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    @if(array_filter($filters))
                        Tidak ada perawatan yang cocok dengan filter Anda.
                    @else
                        Mulai catat perawatan barang untuk pemantauan kondisi aset.
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('maintenances.index') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @else
                    <a href="{{ route('maintenances.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Catat Perawatan Pertama
                    </a>
                @endif
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
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus Data Perawatan?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Perawatan untuk <strong id="delete-item-name" class="text-ink-deep"></strong> akan dihapus dari riwayat.
        </p>
        <form id="delete-form" method="POST" class="flex gap-3">
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

{{-- ==================== MODAL TANDAI SELESAI ==================== --}}
<div id="complete-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-lg w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-success/10 mx-auto mb-4">
            <i class="fas fa-check-circle text-success text-2xl"></i>
        </div>
        
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">
            Tandai Perbaikan Selesai?
        </h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Barang <strong id="complete-item-name" class="text-ink-deep"></strong> akan dikembalikan ke kondisi <strong>Baik</strong> dan status <strong>Tersedia</strong>.
        </p>

        <form id="complete-form" method="POST" class="space-y-5">
            @csrf
            @method('PATCH')

            {{-- Catatan Hasil --}}
            <div>
                <label for="completion_note_index" class="form-label">Catatan Hasil Perbaikan</label>
                <textarea id="completion_note_index" 
                          name="completion_note" 
                          rows="3"
                          placeholder="Contoh: Lampu sudah diganti, sudah dites nyala normal..."
                          maxlength="500"
                          class="form-input !h-auto resize-none"></textarea>
                <p class="text-caption text-steel mt-1">Opsional.</p>
            </div>

            {{-- Biaya Final --}}
            <div>
                <label for="final_cost_index" class="form-label">Biaya Final (Rp)</label>
                <input type="number" 
                       id="final_cost_index" 
                       name="final_cost" 
                       min="0"
                       step="1000"
                       class="form-input">
                <p class="text-caption text-steel mt-1">Ubah jika biaya aktual berbeda.</p>
            </div>

            <div class="flex gap-3">
                <button type="button" 
                        onclick="document.getElementById('complete-modal').classList.add('hidden')"
                        class="btn-ghost flex-1">
                    Batal
                </button>
                <button type="submit" class="btn-primary flex-1">
                    <i class="fas fa-check-circle"></i>
                    Ya, Tandai Selesai
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function confirmDelete(id, itemName) {
        document.getElementById('delete-item-name').textContent = itemName;
        document.getElementById('delete-form').action = `/maintenances/${id}`;
        document.getElementById('delete-modal').classList.remove('hidden');
    }
    function openCompleteModal(id, itemName, currentCost) {
        document.getElementById('complete-item-name').textContent = itemName;
        document.getElementById('final_cost_index').value = currentCost || '';
        document.getElementById('complete-form').action = `/maintenances/${id}/complete`;
        document.getElementById('complete-modal').classList.remove('hidden');
    }
</script>
@endpush