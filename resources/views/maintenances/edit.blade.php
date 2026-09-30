{{-- resources/views/maintenances/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Perawatan')
@section('page_title', 'Edit Perawatan')
@section('page_subtitle', 'Perbarui data perawatan barang')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('maintenances.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-tools text-xs"></i> Perawatan
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <a href="{{ route('maintenances.show', $maintenance->id) }}" class="hover:text-primary">
        #{{ str_pad($maintenance->id, 5, '0', STR_PAD_LEFT) }}
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Edit</span>
</nav>

<form method="POST" action="{{ route('maintenances.update', $maintenance->id) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Info Barang ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4">Barang</h3>

                {{-- Foto --}}
                <div class="aspect-square rounded-xl bg-surface-soft overflow-hidden mb-4">
                    @if($maintenance->item->image)
                        <img src="{{ asset('storage/' . $maintenance->item->image) }}" 
                             alt="{{ $maintenance->item->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-box text-steel text-5xl"></i>
                        </div>
                    @endif
                </div>

                <h4 class="text-body-md-bold text-ink-deep mb-1">{{ $maintenance->item->name }}</h4>
                <p class="text-caption text-steel font-mono mb-3">{{ $maintenance->item->code }}</p>

                <div class="space-y-2 pt-3 border-t border-hairline-soft text-left">
                    <div class="flex items-center gap-2 text-body-sm">
                        <i class="fas fa-tag text-steel w-4 text-xs"></i>
                        <span class="text-steel">{{ $maintenance->item->category->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-body-sm">
                        <i class="fas fa-map-marker-alt text-steel w-4 text-xs"></i>
                        <span class="text-steel">{{ $maintenance->item->location->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-body-sm">
                        <i class="fas fa-heart text-steel w-4 text-xs"></i>
                        <span class="text-steel">{{ $maintenance->item->condition->label() }}</span>
                    </div>
                </div>

                <a href="{{ route('items.show', $maintenance->item->id) }}" 
                   class="btn-ghost w-full justify-center mt-4">
                    <i class="fas fa-external-link-alt"></i>
                    Lihat Barang
                </a>

                {{-- Info: Barang tidak bisa diubah --}}
                <div class="mt-4 p-3 rounded-xl bg-warning/5 border border-warning/20 text-left">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-warning mt-0.5 text-sm"></i>
                        <div>
                            <p class="text-caption-bold text-ink-deep">Barang Tidak Dapat Diubah</p>
                            <p class="text-caption text-steel mt-1">
                                Jika salah pilih barang, hapus perawatan ini dan buat yang baru.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================== KOLOM KANAN: Form ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-primary"></i>
                    Detail Perawatan
                </h3>

                <div class="space-y-5">

                    {{-- Tanggal --}}
                    <div>
                        <label for="maintenance_date" class="form-label">
                            Tanggal Perawatan <span class="text-critical">*</span>
                        </label>
                        <input id="maintenance_date" 
                               type="date" 
                               name="maintenance_date" 
                               value="{{ old('maintenance_date', $maintenance->maintenance_date->format('Y-m-d')) }}"
                               required
                               max="{{ date('Y-m-d') }}"
                               class="form-input @error('maintenance_date') form-input-error @enderror">
                        @error('maintenance_date')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Jenis --}}
                    <div>
                        <label class="form-label">
                            Jenis Perawatan <span class="text-critical">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @php $selectedType = old('type', $maintenance->type); @endphp

                            <label class="cursor-pointer">
                                <input type="radio" 
                                       name="type" 
                                       value="rutin"
                                       @checked($selectedType === 'rutin')
                                       class="sr-only type-radio">
                                <div class="type-card p-4 rounded-xl border-2 transition-all text-center
                                            {{ $selectedType === 'rutin' ? 'border-primary bg-primary/5' : 'border-hairline-soft' }}">
                                    <i class="fas fa-sync-alt text-primary text-2xl mb-2"></i>
                                    <p class="text-body-sm-bold text-ink-deep">Rutin</p>
                                    <p class="text-caption text-steel mt-1">Perawatan berkala</p>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" 
                                       name="type" 
                                       value="perbaikan"
                                       @checked($selectedType === 'perbaikan')
                                       class="sr-only type-radio">
                                <div class="type-card p-4 rounded-xl border-2 transition-all text-center
                                            {{ $selectedType === 'perbaikan' ? 'border-primary bg-primary/5' : 'border-hairline-soft' }}">
                                    <i class="fas fa-wrench text-warning text-2xl mb-2"></i>
                                    <p class="text-body-sm-bold text-ink-deep">Perbaikan</p>
                                    <p class="text-caption text-steel mt-1">Perbaiki kerusakan</p>
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Teknisi + Biaya --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="technician" class="form-label">Teknisi / Vendor</label>
                            <input id="technician" 
                                   type="text" 
                                   name="technician" 
                                   value="{{ old('technician', $maintenance->technician) }}"
                                   maxlength="100"
                                   class="form-input @error('technician') form-input-error @enderror">
                            @error('technician')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="cost" class="form-label">Biaya (Rp)</label>
                            <input id="cost" 
                                   type="number" 
                                   name="cost" 
                                   value="{{ old('cost', $maintenance->cost) }}"
                                   min="0"
                                   step="1000"
                                   class="form-input @error('cost') form-input-error @enderror">
                            @error('cost')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Jadwal Berikutnya --}}
                    <div>
                        <label for="next_maintenance_date" class="form-label">
                            Jadwal Perawatan Berikutnya
                        </label>
                        <input id="next_maintenance_date" 
                               type="date" 
                               name="next_maintenance_date" 
                               value="{{ old('next_maintenance_date', $maintenance->next_maintenance_date?->format('Y-m-d')) }}"
                               class="form-input @error('next_maintenance_date') form-input-error @enderror">
                        @error('next_maintenance_date')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="description" class="form-label">Deskripsi Perawatan</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  maxlength="1000"
                                  class="form-input !h-auto resize-none @error('description') form-input-error @enderror">{{ old('description', $maintenance->description) }}</textarea>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-caption text-steel">Opsional, maksimal 1000 karakter.</p>
                            <p class="text-caption text-steel">
                                <span id="char-count">{{ strlen(old('description', $maintenance->description ?? '')) }}</span>/1000
                            </p>
                        </div>
                        @error('description')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ============ ACTION ============ --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <button type="button" 
                        onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="btn-danger">
                    <i class="fas fa-trash"></i>
                    Hapus Perawatan
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('maintenances.show', $maintenance->id) }}" class="btn-ghost">
                        <i class="fas fa-arrow-left"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>

{{-- ==================== MODAL DELETE ==================== --}}
<div id="delete-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus Perawatan?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Data perawatan ini akan dihapus dari riwayat.
        </p>
        <form method="POST" action="{{ route('maintenances.destroy', $maintenance->id) }}" class="flex gap-3">
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
    // Highlight type radio
    document.querySelectorAll('.type-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.type-card').forEach(card => {
                card.classList.remove('border-primary', 'bg-primary/5');
                card.classList.add('border-hairline-soft');
            });
            const card = this.closest('label').querySelector('.type-card');
            card.classList.remove('border-hairline-soft');
            card.classList.add('border-primary', 'bg-primary/5');
        });
    });

    // Char counter
    document.getElementById('description')?.addEventListener('input', function() {
        document.getElementById('char-count').textContent = this.value.length;
    });
</script>
@endpush