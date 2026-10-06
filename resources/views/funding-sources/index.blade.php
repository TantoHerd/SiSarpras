{{-- resources/views/funding-sources/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Sumber Dana')
@section('page_title', 'Sumber Dana')
@section('page_subtitle', 'Kelola master data sumber perolehan aset')

@section('content')

<div class="space-y-6">

    {{-- ==================== STATISTIK ==================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Total Sumber Dana</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['total']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Aktif</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['active']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                    <i class="fas fa-inbox text-warning"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Belum Dipakai</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['empty']) }}</p>
        </div>
    </div>

    {{-- ==================== HEADER ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $sources->total() }}</span> sumber dana terdaftar
            </p>
        </div>
        <div class="flex items-center gap-2">
            {{-- ← BARU: Export Excel --}}
            <a href="{{ route('funding-sources.export.excel', request()->query()) }}" 
            class="btn-ghost">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </a>
            <a href="{{ route('funding-sources.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i>
                Tambah Sumber Dana
            </a>
        </div>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('funding-sources.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari kode atau nama sumber dana..."
                       class="form-input pl-11">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('funding-sources.index') }}" class="btn-ghost" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Pill Tabs --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Filter:</span>
            <a href="{{ route('funding-sources.index', array_merge(request()->query(), ['is_active' => ''])) }}"
               class="pill-tab {{ !isset($filters['is_active']) || $filters['is_active'] === '' || $filters['is_active'] === null ? 'pill-tab-active' : '' }}">
                Semua
            </a>
            <a href="{{ route('funding-sources.index', array_merge(request()->query(), ['is_active' => '1'])) }}"
               class="pill-tab {{ ($filters['is_active'] ?? '') === '1' ? 'pill-tab-active' : '' }}">
                Aktif
            </a>
            <a href="{{ route('funding-sources.index', array_merge(request()->query(), ['is_active' => '0'])) }}"
               class="pill-tab {{ ($filters['is_active'] ?? '') === '0' ? 'pill-tab-active' : '' }}">
                Nonaktif
            </a>
        </div>
    </div>

    {{-- ==================== TABEL SUMBER DANA ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($sources->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Sumber Dana</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Deskripsi</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Status</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Barang</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($sources as $source)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                {{-- Sumber Dana --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-money-bill-wave text-primary"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-body-sm-bold text-ink-deep truncate max-w-[250px]">
                                                {{ $source->name }}
                                            </p>
                                            <p class="text-caption text-steel font-mono">
                                                {{ $source->code }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Deskripsi --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    @if($source->description)
                                        <p class="text-body-sm text-steel truncate max-w-[300px]">
                                            {{ Str::limit($source->description, 60) }}
                                        </p>
                                    @else
                                        <span class="text-caption text-stone">-</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">
                                    @if($source->is_active)
                                        <span class="badge-success">Aktif</span>
                                    @else
                                        <span class="badge-neutral">Nonaktif</span>
                                    @endif
                                </td>

                                {{-- Barang --}}
                                <td class="px-5 py-4 text-center">
                                    @if($source->items_count > 0)
                                        <a href="{{ route('items.index', ['funding_source_id' => $source->id]) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-pill bg-primary/10 hover:bg-primary/20 transition-colors">
                                            <i class="fas fa-boxes text-primary text-xs"></i>
                                            <span class="text-caption-bold text-primary">
                                                {{ $source->items_count }} barang
                                            </span>
                                        </a>
                                    @else
                                        <span class="badge-neutral">Kosong</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('funding-sources.edit', $source->id) }}" 
                                           class="btn-icon !w-9 !h-9 hover:!bg-warning/10 hover:!text-warning" 
                                           title="Edit">
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>
                                        <button type="button"
                                                onclick="confirmDelete({{ $source->id }}, '{{ addslashes($source->name) }}', {{ $source->items_count }})"
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

            @if($sources->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">
                    {{ $sources->links() }}
                </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-money-bill-wave text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">
                    @if(array_filter($filters))
                        Tidak Ada Hasil
                    @else
                        Belum Ada Sumber Dana
                    @endif
                </h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    @if(array_filter($filters))
                        Tidak ada sumber dana yang cocok dengan filter Anda.
                    @else
                        Mulai tambahkan sumber dana pertama untuk pencatatan asal perolehan aset.
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('funding-sources.index') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @else
                    <a href="{{ route('funding-sources.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Tambah Sumber Dana Pertama
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>

{{-- ==================== MODAL DELETE ==================== --}}
<div id="delete-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div id="delete-icon-bg" class="flex items-center justify-center w-14 h-14 rounded-circle mx-auto mb-4">
            <i id="delete-icon" class="text-2xl"></i>
        </div>
        <h3 id="delete-title" class="text-heading-sm text-ink-deep text-center mb-2">Hapus Sumber Dana?</h3>
        <p id="delete-message" class="text-body-sm text-steel text-center mb-6"></p>

        <form id="delete-form" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="btn-ghost flex-1">
                Batal
            </button>
            <button id="delete-submit-btn" type="submit" class="flex-1">
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function confirmDelete(id, name, itemsCount) {
        const iconBg = document.getElementById('delete-icon-bg');
        const icon = document.getElementById('delete-icon');
        const title = document.getElementById('delete-title');
        const message = document.getElementById('delete-message');
        const submitBtn = document.getElementById('delete-submit-btn');
        const form = document.getElementById('delete-form');

        if (itemsCount > 0) {
            iconBg.className = 'flex items-center justify-center w-14 h-14 rounded-circle bg-warning/10 mx-auto mb-4';
            icon.className = 'fas fa-exclamation-triangle text-warning text-2xl';
            title.textContent = 'Tidak Dapat Dihapus';
            message.innerHTML = `Sumber dana <strong class="text-ink-deep">${name}</strong> masih digunakan oleh <strong class="text-critical">${itemsCount} barang</strong>. Nonaktifkan saja atau ubah sumber dana barang terlebih dahulu.`;
            
            submitBtn.className = 'btn-ghost flex-1';
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Mengerti';
            submitBtn.type = 'button';
            submitBtn.onclick = () => document.getElementById('delete-modal').classList.add('hidden');
            form.action = '#';
        } else {
            iconBg.className = 'flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4';
            icon.className = 'fas fa-exclamation-triangle text-critical text-2xl';
            title.textContent = 'Hapus Sumber Dana?';
            message.innerHTML = `Sumber dana <strong class="text-ink-deep">${name}</strong> akan dihapus dari sistem.`;
            
            submitBtn.className = 'btn-danger flex-1';
            submitBtn.innerHTML = '<i class="fas fa-trash"></i> Ya, Hapus';
            submitBtn.type = 'submit';
            submitBtn.onclick = null;
            form.action = `/funding-sources/${id}`;
        }

        document.getElementById('delete-modal').classList.remove('hidden');
    }
</script>
@endpush