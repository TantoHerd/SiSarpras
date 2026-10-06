{{-- resources/views/categories/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Kategori Barang')
@section('page_title', 'Kategori Barang')
@section('page_subtitle', 'Kelola kategori untuk klasifikasi barang')

@section('content')

<div class="space-y-6">

    {{-- ==================== STATISTIK ==================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-tags text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Total Kategori</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['total']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Ada Barangnya</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['with_items']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                    <i class="fas fa-inbox text-warning"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Kosong</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['empty']) }}</p>
        </div>

        {{-- ← BARU: Portal Siswa count --}}
        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-user-graduate text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Portal Siswa</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['student_loanable'] ?? 0) }}</p>
        </div>
    </div>

    {{-- ==================== HEADER ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $categories->total() }}</span> kategori terdaftar
            </p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            Tambah Kategori
        </a>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('categories.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nama atau deskripsi kategori..."
                       class="form-input pl-11">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('categories.index') }}" class="btn-ghost" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Filter Baris 1: has_items --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Barang:</span>

            {{-- Semua --}}
            <a href="{{ route('categories.index', array_merge(request()->query(), ['has_items' => ''])) }}"
               class="pill-tab {{ !isset($filters['has_items']) || $filters['has_items'] === '' || $filters['has_items'] === null ? 'pill-tab-active' : '' }}">
                Semua
            </a>

            {{-- Ada Barang --}}
            <a href="{{ route('categories.index', array_merge(request()->query(), ['has_items' => '1'])) }}"
               class="pill-tab {{ ($filters['has_items'] ?? '') === '1' ? 'pill-tab-active' : '' }}">
                Ada Barang
            </a>

            {{-- Kosong --}}
            <a href="{{ route('categories.index', array_merge(request()->query(), ['has_items' => '0'])) }}"
               class="pill-tab {{ ($filters['has_items'] ?? '') === '0' ? 'pill-tab-active' : '' }}">
                Kosong
            </a>
        </div>

        {{-- ← BARU: Filter Baris 2: Portal --}}
        <div class="flex flex-wrap items-center gap-2 mt-3">
            <span class="text-caption text-steel mr-2">Portal:</span>

            <a href="{{ route('categories.index', array_merge(request()->query(), ['allow_student_loan' => ''])) }}"
               class="pill-tab {{ !isset($filters['allow_student_loan']) || $filters['allow_student_loan'] === '' || $filters['allow_student_loan'] === null ? 'pill-tab-active' : '' }}">
                Semua
            </a>

            <a href="{{ route('categories.index', array_merge(request()->query(), ['allow_student_loan' => '1'])) }}"
               class="pill-tab {{ ($filters['allow_student_loan'] ?? '') === '1' ? 'pill-tab-active' : '' }}">
                <i class="fas fa-user-graduate text-xs mr-1"></i>
                Bisa Dipinjam Siswa
            </a>

            <a href="{{ route('categories.index', array_merge(request()->query(), ['allow_student_loan' => '0'])) }}"
               class="pill-tab {{ ($filters['allow_student_loan'] ?? '') === '0' ? 'pill-tab-active' : '' }}">
                <i class="fas fa-times text-xs mr-1"></i>
                Hanya Guru
            </a>
        </div>
    </div>

    {{-- ==================== TABEL KATEGORI ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($categories->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Kategori</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden xl:table-cell">Deskripsi</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Mode Tracking</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Portal</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Jumlah Barang</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($categories as $category)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                {{-- Kategori (Icon + Nama) --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                            <i class="fas {{ $category->icon ?? 'fa-boxes' }} text-primary"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-body-sm-bold text-ink-deep truncate max-w-[250px]">
                                                {{ $category->name }}
                                            </p>
                                            <p class="text-caption text-steel font-mono">
                                                ID: #{{ str_pad($category->id, 3, '0', STR_PAD_LEFT) }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Deskripsi --}}
                                <td class="px-5 py-4 hidden xl:table-cell">
                                    <p class="text-body-sm text-steel line-clamp-2 max-w-md">
                                        {{ $category->description ?? 'Tidak ada deskripsi' }}
                                    </p>
                                </td>

                                {{-- Mode Tracking --}}
                                <td class="px-5 py-4 text-center hidden lg:table-cell">
                                    @if($category->default_tracking_mode === 'per_unit')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-pill bg-primary/10 text-primary text-caption-bold">
                                            <i class="fas fa-fingerprint text-xs"></i>
                                            Per Unit
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-pill bg-warning/10 text-warning text-caption-bold">
                                            <i class="fas fa-layer-group text-xs"></i>
                                            Per Batch
                                        </span>
                                    @endif
                                </td>

                                {{-- Portal Siswa --}}
                                <td class="px-5 py-4 text-center hidden lg:table-cell">
                                    @if($category->allow_student_loan)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-pill bg-success/10 text-success text-caption-bold">
                                            <i class="fas fa-user-graduate text-xs"></i>
                                            Ya
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-pill bg-surface-soft text-steel text-caption-bold">
                                            <i class="fas fa-times text-xs"></i>
                                            Tidak
                                        </span>
                                    @endif
                                </td>

                                {{-- Jumlah Barang --}}
                                <td class="px-5 py-4 text-center">
                                    @if($category->items_count > 0)
                                        <a href="{{ route('items.index', ['category_id' => $category->id]) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-pill bg-primary/10 hover:bg-primary/20 transition-colors">
                                            <i class="fas fa-boxes text-primary text-xs"></i>
                                            <span class="text-caption-bold text-primary">
                                                {{ $category->items_count }} barang
                                            </span>
                                        </a>
                                    @else
                                        <span class="badge-neutral">
                                            Kosong
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('categories.show', $category->id) }}" 
                                           class="btn-icon !w-9 !h-9 hover:!bg-primary/10 hover:!text-primary" 
                                           title="Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('categories.edit', $category->id) }}" 
                                           class="btn-icon !w-9 !h-9 hover:!bg-warning/10 hover:!text-warning" 
                                           title="Edit">
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>
                                        <button type="button"
                                                onclick="confirmDelete({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->items_count }})"
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
            @if($categories->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">
                    {{ $categories->links() }}
                </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-tags text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">
                    @if(array_filter($filters))
                        Tidak Ada Hasil
                    @else
                        Belum Ada Kategori
                    @endif
                </h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    @if(array_filter($filters))
                        Tidak ada kategori yang cocok dengan filter Anda.
                    @else
                        Mulai tambahkan kategori pertama untuk klasifikasi barang.
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('categories.index') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @else
                    <a href="{{ route('categories.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Tambah Kategori Pertama
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
        <h3 id="delete-title" class="text-heading-sm text-ink-deep text-center mb-2">Hapus Kategori?</h3>
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
            message.innerHTML = `Kategori <strong class="text-ink-deep">${name}</strong> masih digunakan oleh <strong class="text-critical">${itemsCount} barang</strong>. Pindahkan barang ke kategori lain terlebih dahulu.`;
            
            submitBtn.className = 'btn-ghost flex-1';
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Mengerti';
            submitBtn.type = 'button';
            submitBtn.onclick = () => document.getElementById('delete-modal').classList.add('hidden');
            form.action = '#';
        } else {
            iconBg.className = 'flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4';
            icon.className = 'fas fa-exclamation-triangle text-critical text-2xl';
            title.textContent = 'Hapus Kategori?';
            message.innerHTML = `Kategori <strong class="text-ink-deep">${name}</strong> akan dihapus dari sistem.`;
            
            submitBtn.className = 'btn-danger flex-1';
            submitBtn.innerHTML = '<i class="fas fa-trash"></i> Ya, Hapus';
            submitBtn.type = 'submit';
            submitBtn.onclick = null;
            form.action = `/categories/${id}`;
        }

        document.getElementById('delete-modal').classList.remove('hidden');
    }
</script>
@endpush