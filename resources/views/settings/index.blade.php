{{-- resources/views/settings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page_title', 'Pengaturan Sistem')
@section('page_subtitle', 'Konfigurasi informasi sekolah dan preferensi aplikasi')

@section('content')

<div class="space-y-6">

    {{-- ==================== HEADER + RESET ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Perubahan akan langsung berlaku di seluruh sistem
            </p>
        </div>
        <button type="button"
                onclick="document.getElementById('reset-modal').classList.remove('hidden')"
                class="btn-ghost">
            <i class="fas fa-undo"></i>
            Reset ke Default
        </button>
    </div>

    {{-- ==================== FLASH ==================== --}}
    @if(session('success'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-success/10 border border-success/20">
            <i class="fas fa-check-circle text-success text-lg mt-0.5"></i>
            <p class="text-body-sm text-ink">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-critical/10 border border-critical/20">
            <i class="fas fa-exclamation-circle text-critical text-lg mt-0.5"></i>
            <p class="text-body-sm text-ink">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ==================== TAB NAVIGATION ==================== --}}
    <div class="card !p-0 overflow-hidden">
        <div class="flex flex-wrap border-b border-hairline-soft">
            @php
                $tabs = [
                    ['key' => 'school',      'label' => 'Informasi Sekolah', 'icon' => 'fa-school'],
                    ['key' => 'headmaster',  'label' => 'Kepala Sekolah',     'icon' => 'fa-user-tie'],
                    ['key' => 'preference',  'label' => 'Preferensi',         'icon' => 'fa-cog'],
                    ['key' => 'loan',        'label' => 'Aturan Peminjaman',  'icon' => 'fa-hand-holding'],
                ];
            @endphp

            @foreach($tabs as $t)
                <a href="{{ route('settings.index', ['tab' => $t['key']]) }}"
                   class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 py-4 border-b-2 transition-all
                          {{ $tab === $t['key'] 
                             ? 'border-primary text-primary font-bold bg-primary/5' 
                             : 'border-transparent text-steel hover:text-ink hover:bg-surface-soft' }}">
                    <i class="fas {{ $t['icon'] }} text-sm"></i>
                    <span class="text-body-sm-bold hidden md:inline">{{ $t['label'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- ==================== TAB CONTENT ==================== --}}
        <div class="p-6">

            {{-- TAB: INFORMASI SEKOLAH --}}
            @if($tab === 'school')
                @include('settings.partials.school')
            @endif

            {{-- TAB: KEPALA SEKOLAH --}}
            @if($tab === 'headmaster')
                @include('settings.partials.headmaster')
            @endif

            {{-- TAB: PREFERENSI --}}
            @if($tab === 'preference')
                @include('settings.partials.preference')
            @endif

            {{-- TAB: ATURAN PEMINJAMAN --}}
            @if($tab === 'loan')
                @include('settings.partials.loan')
            @endif

        </div>
    </div>

</div>

{{-- ==================== MODAL RESET ==================== --}}
<div id="reset-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-warning/10 mx-auto mb-4">
            <i class="fas fa-undo text-warning text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Reset Pengaturan?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Semua pengaturan akan dikembalikan ke nilai default. 
            Logo sekolah juga akan dihapus. <strong>Tindakan ini tidak dapat dibatalkan.</strong>
        </p>
        <form method="POST" action="{{ route('settings.reset') }}" class="flex gap-3">
            @csrf
            <button type="button" 
                    onclick="document.getElementById('reset-modal').classList.add('hidden')"
                    class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-danger flex-1">
                <i class="fas fa-undo"></i> Ya, Reset
            </button>
        </form>
    </div>
</div>

@endsection