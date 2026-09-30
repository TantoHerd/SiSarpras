{{-- resources/views/suppliers/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Supplier / Vendor')
@section('page_title', 'Supplier / Vendor')
@section('page_subtitle', 'Kelola data supplier dan vendor barang')

@section('content')

<div class="space-y-6">

    {{-- ==================== STATISTIK ==================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-truck text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Total Supplier</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['total']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Aktif (Punya Barang)</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['with_items']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                    <i class="fas fa-inbox text-warning"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Belum Ada Barang</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['empty']) }}</p>
        </div>
    </div>

    {{-- ==================== HEADER ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $suppliers->total() }}</span> supplier terdaftar
            </p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            Tambah Supplier
        </a>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('suppliers.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nama, PIC, telepon, atau email..."
                       class="form-input pl-11">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('suppliers.index') }}" class="btn-ghost" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Pill Tabs --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Filter:</span>
            <a href="{{ route('suppliers.index', array_merge(request()->query(), ['has_items' => ''])) }}"
               class="pill-tab {{ !isset($filters['has_items']) || $filters['has_items'] === '' || $filters['has_items'] === null ? 'pill-tab-active' : '' }}">
                Semua
            </a>
            <a href="{{ route('suppliers.index', array_merge(request()->query(), ['has_items' => '1'])) }}"
               class="pill-tab {{ ($filters['has_items'] ?? '') === '1' ? 'pill-tab-active' : '' }}">
                Punya Barang
            </a>
            <a href="{{ route('suppliers.index', array_merge(request()->query(), ['has_items' => '0'])) }}"
               class="pill-tab {{ ($filters['has_items'] ?? '') === '0' ? 'pill-tab-active' : '' }}">
                Belum Ada Barang
            </a>
        </div>
    </div>

    {{-- ==================== TABEL SUPPLIER ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($suppliers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Supplier</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Kontak Person</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Kontak</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Barang</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($suppliers as $supplier)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                {{-- Supplier --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-truck text-primary"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-body-sm-bold text-ink-deep truncate max-w-[250px]">
                                                {{ $supplier->name }}
                                            </p>
                                            @if($supplier->address)
                                                <p class="text-caption text-steel truncate max-w-[250px]">
                                                    <i class="fas fa-map-marker-alt text-xs"></i>
                                                    {{ Str::limit($supplier->address, 50) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Contact Person --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    @if($supplier->contact_person)
                                        <p class="text-body-sm text-ink">
                                            <i class="fas fa-user text-steel text-xs mr-1"></i>
                                            {{ $supplier->contact_person }}
                                        </p>
                                    @else
                                        <span class="text-caption text-stone">-</span>
                                    @endif
                                </td>

                                {{-- Kontak --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <div class="space-y-1">
                                        @if($supplier->phone)
                                            <p class="text-body-sm text-ink">
                                                <i class="fas fa-phone text-steel text-xs mr-1"></i>
                                                {{ $supplier->phone }}
                                            </p>
                                        @endif
                                        @if($supplier->email)
                                            <p class="text-caption text-steel">
                                                <i class="fas fa-envelope text-xs mr-1"></i>
                                                {{ $supplier->email }}
                                            </p>
                                        @endif
                                        @if(!$supplier->phone && !$supplier->email)
                                            <span class="text-caption text-stone">-</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Barang --}}
                                <td class="px-5 py-4 text-center">
                                    @if($supplier->items_count > 0)
                                        <a href="{{ route('items.index', ['supplier_id' => $supplier->id]) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-pill bg-primary/10 hover:bg-primary/20 transition-colors">
                                            <i class="fas fa-boxes text-primary text-xs"></i>
                                            <span class="text-caption-bold text-primary">
                                                {{ $supplier->items_count }} barang
                                            </span>
                                        </a>
                                    @else
                                        <span class="badge-neutral">Kosong</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('suppliers.show', $supplier->id) }}" 
                                           class="btn-icon !w-9 !h-9 hover:!bg-primary/10 hover:!text-primary" 
                                           title="Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}" 
                                           class="btn-icon !w-9 !h-9 hover:!bg-warning/10 hover:!text-warning" 
                                           title="Edit">
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>
                                        <button type="button"
                                                onclick="confirmDelete({{ $supplier->id }}, '{{ addslashes($supplier->name) }}', {{ $supplier->items_count }})"
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

            @if($suppliers->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">
                    {{ $suppliers->links() }}
                </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-truck text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">
                    @if(array_filter($filters))
                        Tidak Ada Hasil
                    @else
                        Belum Ada Supplier
                    @endif
                </h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    @if(array_filter($filters))
                        Tidak ada supplier yang cocok dengan filter Anda.
                    @else
                        Mulai tambahkan supplier pertama untuk pencatatan asal barang.
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('suppliers.index') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @else
                    <a href="{{ route('suppliers.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Tambah Supplier Pertama
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
        <h3 id="delete-title" class="text-heading-sm text-ink-deep text-center mb-2">Hapus Supplier?</h3>
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
            message.innerHTML = `Supplier <strong class="text-ink-deep">${name}</strong> masih terkait dengan <strong class="text-critical">${itemsCount} barang</strong>. Ubah supplier barang terlebih dahulu.`;
            
            submitBtn.className = 'btn-ghost flex-1';
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Mengerti';
            submitBtn.type = 'button';
            submitBtn.onclick = () => document.getElementById('delete-modal').classList.add('hidden');
            form.action = '#';
        } else {
            iconBg.className = 'flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4';
            icon.className = 'fas fa-exclamation-triangle text-critical text-2xl';
            title.textContent = 'Hapus Supplier?';
            message.innerHTML = `Supplier <strong class="text-ink-deep">${name}</strong> akan dihapus dari sistem.`;
            
            submitBtn.className = 'btn-danger flex-1';
            submitBtn.innerHTML = '<i class="fas fa-trash"></i> Ya, Hapus';
            submitBtn.type = 'submit';
            submitBtn.onclick = null;
            form.action = `/suppliers/${id}`;
        }

        document.getElementById('delete-modal').classList.remove('hidden');
    }
</script>
@endpush