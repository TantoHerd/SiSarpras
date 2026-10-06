{{-- resources/views/portal-requests/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Permintaan Portal')
@section('page_title', 'Permintaan dari Portal Siswa')
@section('page_subtitle', 'Review dan proses permintaan peminjaman dari siswa')

@section('content')

<div class="space-y-6">

    {{-- ==================== STATISTIK ==================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-warning"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Pending</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['pending']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Disetujui</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['approved']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-critical/10 flex items-center justify-center">
                    <i class="fas fa-times-circle text-critical"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Ditolak</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['rejected']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-calendar-day text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Hari Ini</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['today']) }}</p>
        </div>
    </div>

    {{-- ==================== HEADER ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $requests->total() }}</span> permintaan ditemukan
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('portal.index') }}" target="_blank" class="btn-ghost">
                <i class="fas fa-external-link-alt"></i>
                Buka Portal
            </a>
        </div>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('portal-requests.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            {{-- Search --}}
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari NIS, nama, atau kelas siswa..."
                       class="form-input pl-11">
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('portal-requests.index') }}" class="btn-ghost" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Pill Tabs Status --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Status:</span>
            
            <a href="{{ route('portal-requests.index', array_merge(request()->query(), ['status' => 'pending'])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === 'pending' ? 'pill-tab-active' : '' }}">
                <i class="fas fa-hourglass-half text-xs mr-1"></i>
                Pending
                @if($stats['pending'] > 0)
                    <span class="ml-1 badge-critical text-xs">{{ $stats['pending'] }}</span>
                @endif
            </a>

            <a href="{{ route('portal-requests.index', array_merge(request()->query(), ['status' => 'approved'])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === 'approved' ? 'pill-tab-active' : '' }}">
                Disetujui
            </a>

            <a href="{{ route('portal-requests.index', array_merge(request()->query(), ['status' => 'rejected'])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === 'rejected' ? 'pill-tab-active' : '' }}">
                Ditolak
            </a>

            <a href="{{ route('portal-requests.index', array_merge(request()->query(), ['status' => ''])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === '' ? 'pill-tab-active' : '' }}">
                Semua
            </a>
        </div>
    </div>

    {{-- ==================== TABEL ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($requests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Siswa</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Barang</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Jumlah</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Diajukan</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Status</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($requests as $req)
                            <tr class="hover:bg-surface-soft/50 transition-colors {{ $req->isPending() ? 'bg-warning/5' : '' }}">

                                {{-- Siswa --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user-graduate text-primary text-sm"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-body-sm-bold text-ink-deep truncate max-w-[200px]">
                                                {{ $req->student_name }}
                                            </p>
                                            <p class="text-caption text-steel font-mono">
                                                {{ $req->nis }} • {{ $req->student_class }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Barang --}}
                                <td class="px-5 py-4">
                                    <p class="text-body-sm-bold text-ink-deep truncate max-w-[250px]">
                                        {{ $req->item->name ?? '-' }}
                                    </p>
                                    <p class="text-caption text-steel font-mono">
                                        {{ $req->item->code ?? '-' }}
                                    </p>
                                </td>

                                {{-- Jumlah --}}
                                <td class="px-5 py-4 text-center hidden lg:table-cell">
                                    <span class="text-body-sm-bold text-ink-deep">{{ $req->quantity }}</span>
                                    @if($req->item && $req->item->isPerBatch())
                                        <span class="text-caption text-steel block">unit</span>
                                    @endif
                                </td>

                                {{-- Diajukan --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <p class="text-body-sm text-ink">
                                        {{ $req->created_at->format('d M Y') }}
                                    </p>
                                    <p class="text-caption text-steel">
                                        {{ $req->created_at->diffForHumans() }}
                                    </p>
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="{{ $req->status_badge }}">
                                        {{ $req->status_label }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('portal-requests.show', $req->id) }}" 
                                           class="btn-icon !w-9 !h-9 hover:!bg-primary/10 hover:!text-primary" 
                                           title="Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>

                                        @if($req->isPending())
                                            <button type="button"
                                                    onclick="quickApprove({{ $req->id }}, '{{ addslashes($req->student_name) }}', '{{ addslashes($req->item->name ?? '') }}')"
                                                    class="btn-icon !w-9 !h-9 hover:!bg-success/10 hover:!text-success" 
                                                    title="Setujui">
                                                <i class="fas fa-check text-sm"></i>
                                            </button>
                                            <button type="button"
                                                    onclick="quickReject({{ $req->id }}, '{{ addslashes($req->student_name) }}')"
                                                    class="btn-icon !w-9 !h-9 hover:!bg-critical/10 hover:!text-critical" 
                                                    title="Tolak">
                                                <i class="fas fa-times text-sm"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($requests->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">
                    {{ $requests->links() }}
                </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="py-16 px-6 text-center">
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
                        Tidak ada permintaan yang cocok dengan filter yang dipilih.
                    @else
                        Belum ada siswa yang mengajukan peminjaman melalui portal.
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('portal-requests.index') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>

{{-- ==================== MODAL APPROVE ==================== --}}
<div id="approve-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-success/10 mx-auto mb-4">
            <i class="fas fa-check-circle text-success text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Setujui Permintaan?</h3>
        <p id="approve-message" class="text-body-sm text-steel text-center mb-6"></p>

        <form id="approve-form" method="POST" class="flex gap-3">
            @csrf
            <button type="button" onclick="document.getElementById('approve-modal').classList.add('hidden')" class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-primary flex-1 !bg-success hover:!bg-success/80">
                <i class="fas fa-check"></i> Ya, Setujui
            </button>
        </form>
    </div>
</div>

{{-- ==================== MODAL REJECT ==================== --}}
<div id="reject-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-times-circle text-critical text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Tolak Permintaan?</h3>
        <p id="reject-message" class="text-body-sm text-steel text-center mb-4"></p>

        <form id="reject-form" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="rejection_reason" class="form-label">Alasan Penolakan <span class="text-critical">*</span></label>
                <textarea id="rejection_reason" 
                          name="rejection_reason" 
                          rows="3"
                          required
                          minlength="5"
                          maxlength="500"
                          placeholder="Contoh: Barang sedang digunakan untuk kegiatan lain..."
                          class="form-input !h-auto resize-none"></textarea>
                <p class="text-caption text-steel mt-1">
                    Minimal 5 karakter. Alasan akan ditampilkan ke siswa.
                </p>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="btn-ghost flex-1">
                    Batal
                </button>
                <button type="submit" class="btn-danger flex-1">
                    <i class="fas fa-times"></i> Ya, Tolak
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function quickApprove(id, studentName, itemName) {
        document.getElementById('approve-message').innerHTML = 
            `Permintaan dari <strong class="text-ink-deep">${studentName}</strong> untuk barang <strong class="text-ink-deep">${itemName}</strong> akan disetujui. Loan akan otomatis dibuat.`;
        document.getElementById('approve-form').action = `/admin/portal-requests/${id}/approve`;
        document.getElementById('approve-modal').classList.remove('hidden');
    }

    function quickReject(id, studentName) {
        document.getElementById('reject-message').innerHTML = 
            `Permintaan dari <strong class="text-ink-deep">${studentName}</strong> akan ditolak. Mohon isi alasan penolakan.`;
        document.getElementById('reject-form').action = `/admin/portal-requests/${id}/reject`;
        document.getElementById('reject-modal').classList.remove('hidden');
    }
</script>
@endpush