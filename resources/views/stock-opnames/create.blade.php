{{-- resources/views/stock-opnames/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Buka Sesi Opname')
@section('page_title', 'Buka Sesi Opname')
@section('page_subtitle', 'Mulai sesi hitung fisik stok per lokasi')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('stock-opnames.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-clipboard-list text-xs"></i> Stok Opname
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Buka Sesi</span>
</nav>

<div class="max-w-2xl">

    <form method="POST" action="{{ route('stock-opnames.store') }}" class="space-y-6">
        @csrf

        <div class="card">
            <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                <i class="fas fa-info-circle text-primary"></i>
                Informasi Sesi
            </h3>

            <div class="space-y-5">

                {{-- Lokasi --}}
                <div>
                    <label for="location_id" class="form-label">
                        Lokasi / Gudang <span class="text-critical">*</span>
                    </label>
                    <select id="location_id" 
                            name="location_id" 
                            required
                            class="form-input @error('location_id') form-input-error @enderror">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" @selected(old('location_id') == $loc->id)>
                                {{ $loc->name }} 
                                @if($loc->floor) ({{ $loc->floor }}) @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="text-caption text-steel mt-1">
                        Semua barang habis pakai di lokasi ini akan otomatis dimasukkan ke sesi.
                    </p>
                    @error('location_id')
                        <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Note --}}
                <div>
                    <label for="note" class="form-label">
                        Catatan
                        <span class="text-caption text-steel font-normal ml-1">(opsional)</span>
                    </label>
                    <textarea id="note" 
                              name="note" 
                              rows="3"
                              maxlength="500"
                              placeholder="Contoh: Opname rutin bulan Oktober..."
                              class="form-input !h-auto resize-none @error('note') form-input-error @enderror">{{ old('note') }}</textarea>
                    @error('note')
                        <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Info Box --}}
        <div class="card bg-primary/5 border-primary/20">
            <div class="flex items-start gap-3">
                <i class="fas fa-lightbulb text-primary mt-0.5"></i>
                <div>
                    <p class="text-body-sm-bold text-ink-deep">Bagaimana cara kerja opname?</p>
                    <ul class="text-body-sm text-steel mt-1 space-y-1 list-disc list-inside">
                        <li>Setelah sesi dibuka, Anda bisa input <strong>stok fisik</strong> per item.</li>
                        <li>Stok sistem akan <strong>di-snapshot</strong> saat sesi dibuat.</li>
                        <li>Submit → stok sistem <strong>dikoreksi</strong> sesuai fisik + log movement.</li>
                        <li>Sesi bisa dibatalkan selama masih <strong>draft</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Action --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('stock-opnames.index') }}" class="btn-ghost">
                <i class="fas fa-arrow-left"></i>
                Batal
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-play"></i>
                Buka Sesi Opname
            </button>
        </div>

    </form>

</div>

@endsection