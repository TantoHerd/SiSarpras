{{-- resources/views/students/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Siswa')
@section('page_title', 'Data Siswa')
@section('page_subtitle', 'Kelola data siswa untuk portal peminjaman')

@section('content')

<div class="space-y-6">

    {{-- ← BARU: Flash import result --}}
    @if(session('import_result'))
        @php $result = session('import_result'); @endphp
        <div class="card bg-primary/5 border-primary/20">
            <h3 class="text-body-md-bold text-ink-deep mb-3 flex items-center gap-2">
                <i class="fas fa-clipboard-check text-primary"></i>
                Hasil Import
            </h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center p-3 rounded-xl bg-success/10">
                    <p class="text-caption text-steel uppercase">Baru</p>
                    <p class="text-heading-md text-success">{{ $result['success'] }}</p>
                </div>
                <div class="text-center p-3 rounded-xl bg-warning/10">
                    <p class="text-caption text-steel uppercase">Diperbarui</p>
                    <p class="text-heading-md text-warning">{{ $result['update'] }}</p>
                </div>
                <div class="text-center p-3 rounded-xl bg-critical/10">
                    <p class="text-caption text-steel uppercase">Dilewati</p>
                    <p class="text-heading-md text-critical">{{ $result['skip'] }}</p>
                </div>
            </div>

            @if(!empty($result['skipped']))
                <div class="mt-4 pt-4 border-t border-hairline-soft">
                    <p class="text-body-sm-bold text-ink-deep mb-2">Baris yang Dilewati:</p>
                    <ul class="text-caption text-steel space-y-1 max-h-40 overflow-y-auto">
                        @foreach($result['skipped'] as $skip)
                            <li>
                                <i class="fas fa-times-circle text-critical text-xs mr-1"></i>
                                NIS: <strong>{{ $skip['nis'] ?? '-' }}</strong> — {{ $skip['reason'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- ==================== STATISTIK ==================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-user-graduate text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Total Siswa</p>
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
                    <i class="fas fa-user-slash text-warning"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Nonaktif</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['inactive']) }}</p>
        </div>
    </div>

    {{-- ==================== HEADER ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $students->total() }}</span> siswa terdaftar
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('students.import.form') }}" class="btn-ghost">
                <i class="fas fa-file-excel"></i>
                Import Excel
            </a>
            <a href="{{ route('students.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i>
                Tambah Siswa
            </a>
        </div>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('students.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            {{-- Search --}}
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari NIS, nama, atau kelas..."
                       class="form-input pl-11">
            </div>

            {{-- Kelas --}}
            <select name="class" class="form-input">
                <option value="">Semua Kelas</option>
                @foreach($classes as $class)
                    <option value="{{ $class }}" @selected(($filters['class'] ?? '') === $class)>
                        {{ $class }}
                    </option>
                @endforeach
            </select>

            {{-- Submit --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('students.index') }}" class="btn-ghost" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Pill Tabs Status --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Status:</span>
            <a href="{{ route('students.index', array_merge(request()->query(), ['is_active' => ''])) }}"
               class="pill-tab {{ ($filters['is_active'] ?? '') === '' || ($filters['is_active'] ?? null) === null ? 'pill-tab-active' : '' }}">
                Semua
            </a>
            <a href="{{ route('students.index', array_merge(request()->query(), ['is_active' => '1'])) }}"
               class="pill-tab {{ ($filters['is_active'] ?? '') === '1' ? 'pill-tab-active' : '' }}">
                Aktif
            </a>
            <a href="{{ route('students.index', array_merge(request()->query(), ['is_active' => '0'])) }}"
               class="pill-tab {{ ($filters['is_active'] ?? '') === '0' ? 'pill-tab-active' : '' }}">
                Nonaktif
            </a>
        </div>
    </div>

    {{-- ==================== TABEL ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($students->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Siswa</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Kelas</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">No HP</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Status</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($students as $student)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                {{-- Siswa --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user-graduate text-primary"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-body-sm-bold text-ink-deep truncate max-w-[250px]">
                                                {{ $student->name }}
                                            </p>
                                            <p class="text-caption text-steel font-mono">
                                                NIS: {{ $student->nis }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kelas --}}
                                <td class="px-5 py-4">
                                    <span class="badge-info">{{ $student->class }}</span>
                                </td>

                                {{-- No HP --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    @if($student->phone)
                                        <p class="text-body-sm text-ink">
                                            <i class="fas fa-phone text-steel text-xs mr-1"></i>
                                            {{ $student->phone }}
                                        </p>
                                    @else
                                        <span class="text-caption text-stone">-</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">
                                    @if($student->is_active)
                                        <span class="badge-success">Aktif</span>
                                    @else
                                        <span class="badge-neutral">Nonaktif</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('students.show', $student->id) }}" 
                                           class="btn-icon !w-9 !h-9 hover:!bg-primary/10 hover:!text-primary" 
                                           title="Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('students.edit', $student->id) }}" 
                                           class="btn-icon !w-9 !h-9 hover:!bg-warning/10 hover:!text-warning" 
                                           title="Edit">
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>
                                        <button type="button"
                                                onclick="confirmDelete({{ $student->id }}, '{{ addslashes($student->name) }}')"
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

            @if($students->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">
                    {{ $students->links() }}
                </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-user-graduate text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">
                    @if(array_filter($filters))
                        Tidak Ada Hasil
                    @else
                        Belum Ada Siswa
                    @endif
                </h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    @if(array_filter($filters))
                        Tidak ada siswa yang cocok dengan filter Anda.
                    @else
                        Mulai tambahkan data siswa untuk mengaktifkan portal peminjaman.
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('students.index') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @else
                    <a href="{{ route('students.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Tambah Siswa Pertama
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
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus Siswa?</h3>
        <p id="delete-message" class="text-body-sm text-steel text-center mb-6"></p>

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

@endsection

@push('scripts')
<script>
    function confirmDelete(id, name) {
        document.getElementById('delete-message').innerHTML = 
            `Siswa <strong class="text-ink-deep">${name}</strong> akan dihapus dari sistem.`;
        document.getElementById('delete-form').action = `/students/${id}`;
        document.getElementById('delete-modal').classList.remove('hidden');
    }
</script>
@endpush