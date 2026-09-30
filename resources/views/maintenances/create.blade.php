{{-- resources/views/maintenances/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Catat Perawatan')
@section('page_title', 'Catat Perawatan')
@section('page_subtitle', 'Catat aktivitas perawatan atau perbaikan barang')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('maintenances.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-tools text-xs"></i> Perawatan
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Catat Perawatan</span>
</nav>

<form method="POST" action="{{ route('maintenances.store') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Preview ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card text-center sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4 text-left">Preview</h3>

                <div id="preview-empty" class="py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-circle bg-surface-soft mb-3">
                        <i class="fas fa-tools text-steel text-2xl"></i>
                    </div>
                    <p class="text-body-sm text-steel">
                        Belum ada barang dipilih.<br>
                        Pilih dari form di kanan.
                    </p>
                </div>

                <div id="preview-content" class="hidden">
                    {{-- Foto Barang --}}
                    <div class="aspect-square rounded-xl bg-surface-soft overflow-hidden mb-4">
                        <img id="preview-image" 
                             src="" 
                             alt="Preview"
                             class="w-full h-full object-cover hidden">
                        <div id="preview-image-placeholder" 
                             class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-box text-steel text-5xl"></i>
                        </div>
                    </div>

                    {{-- Nama --}}
                    <h4 id="preview-name" class="text-body-md-bold text-ink-deep mb-1"></h4>
                    <p id="preview-code" class="text-caption text-steel font-mono mb-3"></p>

                    {{-- Meta --}}
                    <div class="space-y-2 pt-3 border-t border-hairline-soft text-left">
                        <div class="flex items-center gap-2 text-body-sm">
                            <i class="fas fa-tag text-steel w-4 text-xs"></i>
                            <span id="preview-category" class="text-steel">-</span>
                        </div>
                        <div class="flex items-center gap-2 text-body-sm">
                            <i class="fas fa-map-marker-alt text-steel w-4 text-xs"></i>
                            <span id="preview-location" class="text-steel">-</span>
                        </div>
                        <div class="flex items-center gap-2 text-body-sm">
                            <i class="fas fa-heart text-steel w-4 text-xs"></i>
                            <span id="preview-condition" class="text-steel">-</span>
                        </div>
                    </div>

                    {{-- Info Box: Auto Update --}}
                    <div id="preview-info" class="mt-4 p-3 rounded-xl bg-primary/5 border border-primary/20 text-left hidden">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-info-circle text-primary mt-0.5 text-sm"></i>
                            <div>
                                <p class="text-caption-bold text-ink-deep">Auto-Update</p>
                                <p class="text-caption text-steel mt-1">
                                    Setelah perbaikan dicatat, kondisi barang otomatis berubah menjadi <strong>Baik</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================== KOLOM KANAN: Form ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ============ INFORMASI BARANG ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-box text-primary"></i>
                    Informasi Barang
                </h3>

                {{-- Search --}}
                <div class="relative mb-4">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm"></i>
                    <input type="text" 
                           id="search-item"
                           placeholder="Cari barang..."
                           oninput="filterItems()"
                           class="form-input pl-11">
                </div>

                {{-- Hidden input --}}
                <input type="hidden" id="item_id" name="item_id" value="{{ old('item_id', $selectedItemId) }}">

                {{-- Items List --}}
                @if($items->count() > 0)
                    <div id="items-list" class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-2">
                        @foreach($items as $item)
                            <label class="item-option cursor-pointer" 
                                   data-name="{{ strtolower($item->name) }}"
                                   data-code="{{ strtolower($item->code) }}">
                                <input type="radio" 
                                       name="item_radio" 
                                       value="{{ $item->id }}"
                                       @checked(old('item_id', $selectedItemId) == $item->id)
                                       onchange="selectItem(this)"
                                       class="sr-only">
                                <div class="item-card p-3 rounded-xl border-2 border-hairline-soft bg-canvas
                                            hover:border-hairline transition-all
                                            flex items-center gap-3">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" 
                                             alt="{{ $item->name }}"
                                             class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-14 h-14 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-box text-steel"></i>
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <p class="text-body-sm-bold text-ink-deep truncate">{{ $item->name }}</p>
                                        <p class="text-caption text-steel font-mono truncate">{{ $item->code }}</p>
                                        <p class="text-caption text-steel truncate">
                                            <i class="fas fa-map-marker-alt text-xs"></i>
                                            {{ $item->location->name ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="check-icon w-6 h-6 rounded-full border-2 border-hairline flex items-center justify-center flex-shrink-0 transition-all">
                                        <i class="fas fa-check text-white text-xs opacity-0 transition-opacity check-mark"></i>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-circle bg-surface-soft mb-3">
                            <i class="fas fa-box-open text-steel text-2xl"></i>
                        </div>
                        <p class="text-body-sm text-steel">Belum ada barang terdaftar.</p>
                    </div>
                @endif

                @error('item_id')
                    <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                        <i class="fas fa-exclamation-circle text-xs"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- ============ DETAIL PERAWATAN ============ --}}
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
                               value="{{ old('maintenance_date', date('Y-m-d')) }}"
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

                    {{-- Jenis Perawatan --}}
                    <div>
                        <label class="form-label">
                            Jenis Perawatan <span class="text-critical">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @php $selectedType = old('type', 'rutin'); @endphp

                            <label class="cursor-pointer">
                                <input type="radio" 
                                       name="type" 
                                       value="rutin"
                                       @checked($selectedType === 'rutin')
                                       onchange="updatePreview(); updateTypeInfo()"
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
                                       onchange="updatePreview(); updateTypeInfo()"
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

                    {{-- ============ Status Perbaikan (khusus perbaikan) ============ --}}
                    <div id="completion-status-group" class="hidden">
                        <label class="form-label">Status Perbaikan</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @php $isCompleted = old('is_completed', '1'); @endphp

                            <label class="cursor-pointer">
                                <input type="radio" 
                                    name="is_completed" 
                                    value="0"
                                    @checked($isCompleted === '0' || $isCompleted === 0)
                                    class="sr-only completion-radio">
                                <div class="completion-card p-4 rounded-xl border-2 transition-all text-center
                                            {{ $isCompleted === '0' ? 'border-warning bg-warning/5' : 'border-hairline-soft' }}">
                                    <i class="fas fa-clock text-warning text-2xl mb-2"></i>
                                    <p class="text-body-sm-bold text-ink-deep">Sedang Diperbaiki</p>
                                    <p class="text-caption text-steel mt-1">Barang belum bisa dipakai</p>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" 
                                    name="is_completed" 
                                    value="1"
                                    @checked($isCompleted === '1' || $isCompleted === 1)
                                    class="sr-only completion-radio">
                                <div class="completion-card p-4 rounded-xl border-2 transition-all text-center
                                            {{ $isCompleted === '1' || $isCompleted === 1 ? 'border-success bg-success/5' : 'border-hairline-soft' }}">
                                    <i class="fas fa-check-circle text-success text-2xl mb-2"></i>
                                    <p class="text-body-sm-bold text-ink-deep">Langsung Selesai</p>
                                    <p class="text-caption text-steel mt-1">Barang kembali tersedia</p>
                                </div>
                            </label>
                        </div>
                        <p class="text-caption text-steel mt-2">
                            Pilih <strong>"Sedang Diperbaiki"</strong> jika barang masih di teknisi. Anda bisa tandai selesai nanti.
                        </p>
                    </div>

                    {{-- Teknisi + Biaya --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="technician" class="form-label">Teknisi / Vendor</label>
                            <input id="technician" 
                                   type="text" 
                                   name="technician" 
                                   value="{{ old('technician') }}"
                                   placeholder="Contoh: Bpk. Andi (Teknisi AC)"
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
                                   value="{{ old('cost', 0) }}"
                                   min="0"
                                   step="1000"
                                   class="form-input @error('cost') form-input-error @enderror">
                            <p class="text-caption text-steel mt-1">
                                Kosongkan atau isi 0 jika gratis.
                            </p>
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
                               value="{{ old('next_maintenance_date') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="form-input @error('next_maintenance_date') form-input-error @enderror">
                        <p class="text-caption text-steel mt-1">
                            Opsional. Sistem akan mengingatkan Anda saat jadwal mendekati.
                        </p>
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
                                  placeholder="Jelaskan detail perawatan/perbaikan yang dilakukan..."
                                  maxlength="1000"
                                  class="form-input !h-auto resize-none @error('description') form-input-error @enderror">{{ old('description') }}</textarea>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-caption text-steel">Opsional, maksimal 1000 karakter.</p>
                            <p class="text-caption text-steel">
                                <span id="char-count">0</span>/1000
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
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('maintenances.index') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </a>
                <button type="submit" 
                        id="submit-btn"
                        class="btn-primary"
                        disabled>
                    <i class="fas fa-save"></i>
                    Simpan Perawatan
                </button>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    const itemsData = @json($itemsJson);

    function selectItem(radio) {
        const itemId = radio.value;
        const item = itemsData[itemId];
        if (!item) return;

        document.getElementById('item_id').value = itemId;

        // Reset highlights
        document.querySelectorAll('.item-card').forEach(card => {
            card.classList.remove('border-primary', 'bg-primary/5');
            card.classList.add('border-hairline-soft');
        });
        document.querySelectorAll('.check-icon').forEach(icon => {
            icon.classList.remove('bg-primary', 'border-primary');
            icon.classList.add('border-hairline');
        });
        document.querySelectorAll('.check-mark').forEach(mark => {
            mark.classList.add('opacity-0');
        });

        // Highlight selected
        const card = radio.closest('label').querySelector('.item-card');
        card.classList.remove('border-hairline-soft');
        card.classList.add('border-primary', 'bg-primary/5');

        const checkIcon = radio.closest('label').querySelector('.check-icon');
        checkIcon.classList.remove('border-hairline');
        checkIcon.classList.add('bg-primary', 'border-primary');

        const checkMark = radio.closest('label').querySelector('.check-mark');
        checkMark.classList.remove('opacity-0');

        // Update preview
        document.getElementById('preview-empty').classList.add('hidden');
        document.getElementById('preview-content').classList.remove('hidden');

        document.getElementById('preview-name').textContent = item.name;
        document.getElementById('preview-code').textContent = item.code;
        document.getElementById('preview-category').textContent = item.category;
        document.getElementById('preview-location').textContent = item.location;
        document.getElementById('preview-condition').textContent = item.condition;

        const previewImage = document.getElementById('preview-image');
        const previewPlaceholder = document.getElementById('preview-image-placeholder');

        if (item.image) {
            previewImage.src = item.image;
            previewImage.classList.remove('hidden');
            previewPlaceholder.classList.add('hidden');
        } else {
            previewImage.src = '';
            previewImage.classList.add('hidden');
            previewPlaceholder.classList.remove('hidden');
        }

        updateTypeInfo();
        document.getElementById('submit-btn').disabled = false;
    }

    function updateTypeInfo() {
        const type = document.querySelector('input[name="type"]:checked')?.value;
        const infoBox = document.getElementById('preview-info');
        const completionGroup = document.getElementById('completion-status-group');

        // Tampilkan/sembunyikan group "Status Perbaikan"
        if (type === 'perbaikan') {
            completionGroup.classList.remove('hidden');
        } else {
            completionGroup.classList.add('hidden');
        }

        // Tampilkan/sembunyikan info box auto-update
        if (type === 'perbaikan' && !document.getElementById('preview-content').classList.contains('hidden')) {
            infoBox.classList.remove('hidden');
        } else {
            infoBox.classList.add('hidden');
        }
    }

    // Highlight completion radio
    document.querySelectorAll('.completion-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.completion-card').forEach(card => {
                card.classList.remove('border-warning', 'border-success', 'bg-warning/5', 'bg-success/5');
                card.classList.add('border-hairline-soft');
            });
            
            const card = this.closest('label').querySelector('.completion-card');
            card.classList.remove('border-hairline-soft');
            
            if (this.value === '0') {
                card.classList.add('border-warning', 'bg-warning/5');
            } else {
                card.classList.add('border-success', 'bg-success/5');
            }
        });
    });

    function filterItems() {
        const search = document.getElementById('search-item').value.toLowerCase();
        document.querySelectorAll('.item-option').forEach(option => {
            const name = option.dataset.name || '';
            const code = option.dataset.code || '';
            option.style.display = (name.includes(search) || code.includes(search)) ? '' : 'none';
        });
    }

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
            updateTypeInfo();
        });
    });

    // Char counter
    document.getElementById('description')?.addEventListener('input', function() {
        document.getElementById('char-count').textContent = this.value.length;
    });

    // Auto-select kalau ada old/selected
    document.addEventListener('DOMContentLoaded', function() {
        const checked = document.querySelector('input[name="item_radio"]:checked');
        if (checked) selectItem(checked);
    });
</script>
@endpush