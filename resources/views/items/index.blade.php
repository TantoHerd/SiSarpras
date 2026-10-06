{{-- resources/views/items/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Barang')
@section('page_title', 'Data Barang')
@section('page_subtitle', 'Kelola inventaris sarana dan prasarana sekolah')

@section('content')

<div class="space-y-6">

    {{-- ==================== HEADER + ACTION ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $items->total() }}</span> barang terdaftar
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button"
                    id="btn-batch-label"
                    onclick="printBatchLabels()"
                    class="btn-ink hidden">
                <i class="fas fa-print"></i>
                <span>Cetak Label Massal</span>
                <span id="selected-count" class="badge-info ml-1">0</span>
            </button>
            <a href="{{ route('items.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i>
                Tambah Barang
            </a>
        </div>
    </div>

    {{-- ==================== FILTER CARD ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('items.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            {{-- Search --}}
            <div class="lg:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nama, kode, merk, serial..."
                       class="form-input pl-11">
            </div>

            {{-- Kategori --}}
            <select name="category_id" class="form-input">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(($filters['category_id'] ?? '') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            {{-- Lokasi --}}
            <select name="location_id" class="form-input">
                <option value="">Semua Lokasi</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" @selected(($filters['location_id'] ?? '') == $loc->id)>
                        {{ $loc->name }}
                    </option>
                @endforeach
            </select>
            {{-- Filter Sumber Dana --}}
            <div>
                <select name="funding_source_id" class="form-input">
                    <option value="">Semua Sumber Dana</option>
                    @foreach($fundingSources as $source)
                        <option value="{{ $source->id }}"
                                @selected(request('funding_source_id') == $source->id)>
                            {{ $source->code }} - {{ $source->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ← BARU: Filter Mode Tracking --}}
            <select name="tracking_mode" class="form-input">
                <option value="">Semua Mode Tracking</option>
                <option value="per_unit" @selected(($filters['tracking_mode'] ?? '') === 'per_unit')>
                    Per Unit (aset unik)
                </option>
                <option value="per_batch" @selected(($filters['tracking_mode'] ?? '') === 'per_batch')>
                    Per Batch (quantity)
                </option>
            </select>

            {{-- Submit --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('items.index') }}" class="btn-ghost" title="Reset Filter">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Filter Kondisi & Status (Pill Tabs) --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Status:</span>
            <a href="{{ route('items.index', array_merge(request()->query(), ['status' => ''])) }}"
               class="pill-tab {{ empty($filters['status']) ? 'pill-tab-active' : '' }}">
                Semua
            </a>
            @foreach(\App\Enums\ItemStatusEnum::cases() as $status)
                <a href="{{ route('items.index', array_merge(request()->query(), ['status' => $status->value])) }}"
                   class="pill-tab {{ ($filters['status'] ?? '') === $status->value ? 'pill-tab-active' : '' }}">
                    {{ $status->label() }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- ==================== TABEL BARANG ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($items->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-center px-3 py-3 w-12">
                                <input type="checkbox"
                                    id="select-all"
                                    onchange="toggleSelectAll(this)"
                                    class="w-4 h-4 rounded border-hairline-soft text-primary focus:ring-primary cursor-pointer">
                            </th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Barang</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Kategori</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Lokasi</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Mode</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Stok</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Kondisi</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Status</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($items as $item)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                <td class="text-center px-3 py-3">
                                    <input type="checkbox"
                                        name="item_ids[]"
                                        value="{{ $item->id }}"
                                        class="item-checkbox w-4 h-4 rounded border-hairline-soft text-primary focus:ring-primary cursor-pointer"
                                        onchange="updateSelection()">
                                </td>
                                {{-- Barang --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($item->image)
                                            <img src="{{ asset('storage/' . $item->image) }}" 
                                                 alt="{{ $item->name }}"
                                                 class="w-12 h-12 rounded-lg object-cover bg-surface-soft flex-shrink-0">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-box text-steel"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-body-sm-bold text-ink-deep truncate max-w-[250px]">
                                                {{ $item->name }}
                                                @if($item->fundingSource)
                                                    <span class="badge-info text-xs mt-1 inline-flex">
                                                        <i class="fas fa-money-bill-wave mr-1"></i>
                                                        {{ $item->fundingSource->code }}
                                                    </span>
                                                @endif
                                            </p>
                                            <p class="text-caption text-steel font-mono">
                                                {{ $item->code }}
                                            </p>
                                            @if($item->brand)
                                                <p class="text-caption text-steel">{{ $item->brand }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Kategori --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <span class="badge-neutral">
                                        {{ $item->category->name ?? '-' }}
                                    </span>
                                </td>

                                {{-- Lokasi --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <p class="text-body-sm text-ink">
                                        {{ $item->location->name ?? '-' }}
                                    </p>
                                    @if($item->location->floor)
                                        <p class="text-caption text-steel">{{ $item->location->floor }}</p>
                                    @endif
                                </td>

                                {{-- ← BARU: Mode Tracking --}}
                                <td class="px-5 py-4 text-center hidden lg:table-cell">
                                    @if($item->isPerBatch())
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-pill bg-warning/10 text-warning text-caption-bold"
                                            title="Per Batch: 1 kode untuk grup">
                                            <i class="fas fa-layer-group text-xs"></i>
                                            Batch
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-pill bg-cobalt/10 text-cobalt text-caption-bold"
                                            title="Per Unit: 1 barang = 1 kode">
                                            <i class="fas fa-fingerprint text-xs"></i>
                                            Unit
                                        </span>
                                    @endif
                                </td>

                                {{-- Stok --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="text-body-sm-bold text-ink-deep">{{ $item->quantity }}</span>
                                </td>

                                {{-- Kondisi --}}
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

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">
                                    @php
                                        $statusColor = match($item->status->value) {
                                            'tersedia' => 'badge-success',
                                            'dipinjam' => 'badge-warning',
                                            'perbaikan' => 'badge-info',
                                            'tidak_aktif' => 'badge-critical',
                                            default => 'badge-neutral',
                                        };
                                    @endphp
                                    <span class="{{ $statusColor }}">
                                        {{ $item->status->label() }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('items.show', $item->id) }}" 
                                        class="btn-icon !w-9 !h-9 hover:!bg-primary/10 hover:!text-primary" 
                                        title="Lihat Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('items.edit', $item->id) }}" 
                                        class="btn-icon !w-9 !h-9 hover:!bg-warning/10 hover:!text-warning" 
                                        title="Edit">
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>
                                        <button type="button"
                                                onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->name) }}')"
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
            @if($items->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">
                    {{ $items->links() }}
                </div>
            @endif

        @else
            {{-- ==================== EMPTY STATE ==================== --}}
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-box-open text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">
                    @if(array_filter($filters))
                        Tidak Ada Hasil
                    @else
                        Belum Ada Barang
                    @endif
                </h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    @if(array_filter($filters))
                        Tidak ada barang yang cocok dengan filter Anda. Coba ubah kriteria pencarian.
                    @else
                        Mulai dengan menambahkan barang pertama ke dalam sistem inventaris sekolah.
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('items.index') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @else
                    <a href="{{ route('items.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Tambah Barang Pertama
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>

{{-- ==================== MODAL HAPUS BARANG ==================== --}}
<div id="delete-modal-index" 
     class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">

        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>

        <h3 class="text-heading-sm text-ink-deep text-center mb-2">
            Hapus Barang Ini?
        </h3>
        <p class="text-body-sm text-steel text-center mb-2">
            Barang berikut akan dihapus dari sistem:
        </p>
        <div class="p-3 rounded-xl bg-surface-soft border border-hairline-soft mb-6">
            <p id="delete-item-name" 
               class="text-body-sm-bold text-ink-deep text-center truncate">
            </p>
        </div>

        <form id="delete-form-index" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')

            <button type="button" 
                    onclick="closeDeleteModal()"
                    class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-danger flex-1">
                <i class="fas fa-trash"></i>
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(id, name) {
        document.getElementById('delete-item-name').textContent = name;
        document.getElementById('delete-form-index').action = `/items/${id}`;
        document.getElementById('delete-modal-index').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal-index').classList.add('hidden');
    }

    // ==================== MULTI-SELECT UNTUK CETAK LABEL MASSAL ====================

    /**
     * Toggle semua checkbox ketika "select all" dicentang
     */
    function toggleSelectAll(checkbox) {
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        itemCheckboxes.forEach(cb => cb.checked = checkbox.checked);
        updateSelection();
    }

    /**
     * Update tampilan tombol "Cetak Label Massal" berdasarkan jumlah item terpilih
     */
    function updateSelection() {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        const count = checked.length;
        const btn = document.getElementById('btn-batch-label');
        const countBadge = document.getElementById('selected-count');
        const selectAll = document.getElementById('select-all');

        // Update badge jumlah
        if (countBadge) countBadge.textContent = count;

        // Tampilkan / sembunyikan tombol
        if (count > 0) {
            btn.classList.remove('hidden');
        } else {
            btn.classList.add('hidden');
        }

        // Sync state "select all"
        const allCheckboxes = document.querySelectorAll('.item-checkbox');
        if (selectAll && allCheckboxes.length > 0) {
            selectAll.checked = count === allCheckboxes.length;
            selectAll.indeterminate = count > 0 && count < allCheckboxes.length;
        }
    }

    /**
     * Buka halaman cetak label massal dengan ID item terpilih
     */
    function printBatchLabels() {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.value);

        if (ids.length === 0) {
            alert('Pilih minimal 1 barang untuk dicetak labelnya.');
            return;
        }

        if (ids.length > 100) {
            alert('Maksimal 100 barang per sekali cetak. Silakan filter dulu.');
            return;
        }

        // Redirect ke halaman cetak label massal
        const url = `{{ route('items.barcode-batch') }}?ids=${ids.join(',')}`;
        window.open(url, '_blank');
    }

    /**
     * Init saat halaman selesai load
     */
    document.addEventListener('DOMContentLoaded', function () {
        updateSelection();
    });
</script>
@endpush

@endsection