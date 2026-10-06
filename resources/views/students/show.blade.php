{{-- resources/views/students/show.blade.php --}}
@extends('layouts.app')

@section('title', $student->name)
@section('page_title', 'Detail Siswa')
@section('page_subtitle', 'Informasi lengkap data siswa')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('students.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-user-graduate text-xs"></i> Data Siswa
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">{{ $student->name }}</span>
</nav>

<div class="space-y-6">

    {{-- ============ HERO CARD ============ --}}
    <div class="card">
        <div class="flex flex-wrap items-start gap-6">

            {{-- Avatar --}}
            <div class="w-20 h-20 rounded-2xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-graduate text-primary text-3xl"></i>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-[250px]">
                <h1 class="text-heading-lg text-ink-deep mb-2">{{ $student->name }}</h1>
                <p class="text-body-md text-steel mb-4 font-mono">
                    NIS: {{ $student->nis }}
                </p>

                <div class="flex flex-wrap items-center gap-3">
                    <span class="badge-info">
                        <i class="fas fa-graduation-cap mr-1.5 text-xs"></i>
                        {{ $student->class }}
                    </span>

                    @if($student->is_active)
                        <span class="badge-success">
                            <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                            Aktif
                        </span>
                    @else
                        <span class="badge-neutral">
                            <i class="fas fa-times-circle mr-1.5 text-xs"></i>
                            Nonaktif
                        </span>
                    @endif

                    @if($student->phone)
                        <span class="badge-neutral">
                            <i class="fas fa-phone mr-1.5 text-xs"></i>
                            {{ $student->phone }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Aksi --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('students.edit', $student->id) }}" class="btn-primary">
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

    {{-- ============ INFO CARD ============ --}}
    <div class="card">
        <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
            <i class="fas fa-info-circle text-primary"></i>
            Informasi Detail
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- NIS --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-id-card text-steel text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-caption text-steel uppercase tracking-wider">NIS</p>
                    <p class="text-body-sm-bold text-ink-deep font-mono">{{ $student->nis }}</p>
                </div>
            </div>

            {{-- Nama --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-steel text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-caption text-steel uppercase tracking-wider">Nama</p>
                    <p class="text-body-sm-bold text-ink-deep">{{ $student->name }}</p>
                </div>
            </div>

            {{-- Kelas --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-graduation-cap text-steel text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-caption text-steel uppercase tracking-wider">Kelas</p>
                    <p class="text-body-sm-bold text-ink-deep">{{ $student->class }}</p>
                </div>
            </div>

            {{-- Phone --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-phone text-steel text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-caption text-steel uppercase tracking-wider">No HP</p>
                    <p class="text-body-sm-bold text-ink-deep">{{ $student->phone ?? '-' }}</p>
                </div>
            </div>

            {{-- Status --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-toggle-on text-steel text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-caption text-steel uppercase tracking-wider">Status</p>
                    <p class="text-body-sm-bold text-ink-deep">
                        {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                    </p>
                </div>
            </div>

            {{-- Terdaftar --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar text-steel text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-caption text-steel uppercase tracking-wider">Terdaftar</p>
                    <p class="text-body-sm-bold text-ink-deep">
                        {{ $student->created_at->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- ==================== MODAL DELETE ==================== --}}
<div id="delete-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus Siswa?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Siswa <strong class="text-ink-deep">{{ $student->name }}</strong> akan dihapus dari sistem.
        </p>
        <form method="POST" action="{{ route('students.destroy', $student->id) }}" class="flex gap-3">
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