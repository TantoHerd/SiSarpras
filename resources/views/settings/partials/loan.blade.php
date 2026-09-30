{{-- resources/views/settings/partials/loan.blade.php --}}
<form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="group_name" value="loan">
    <input type="hidden" name="redirect_tab" value="loan">

    <div>
        <h3 class="text-heading-sm text-ink-deep mb-1">Aturan Peminjaman</h3>
        <p class="text-body-sm text-steel">
            Konfigurasi batas waktu dan sistem denda untuk peminjaman barang.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Max Hari --}}
        <div>
            <label for="max_loan_days" class="form-label">
                Maksimal Hari Peminjaman <span class="text-critical">*</span>
            </label>
            <input id="max_loan_days" type="number" name="max_loan_days" 
                   value="{{ old('max_loan_days', setting('max_loan_days', 7)) }}"
                   required min="1" max="365"
                   class="form-input @error('max_loan_days') form-input-error @enderror">
            <p class="text-caption text-steel mt-1">
                Batas waktu maksimal user boleh meminjam barang.
            </p>
            @error('max_loan_days')
                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Max Pinjam per User --}}
        <div>
            <label for="max_loan_per_user" class="form-label">
                Maksimal Pinjam per User <span class="text-critical">*</span>
            </label>
            <input id="max_loan_per_user" type="number" name="max_loan_per_user" 
                   value="{{ old('max_loan_per_user', setting('max_loan_per_user', 5)) }}"
                   required min="1" max="50"
                   class="form-input @error('max_loan_per_user') form-input-error @enderror">
            <p class="text-caption text-steel mt-1">
                Batas jumlah barang yang bisa dipinjam bersamaan.
            </p>
            @error('max_loan_per_user')
                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    {{-- ============ SISTEM DENDA ============ --}}
    <div class="pt-6 border-t border-hairline-soft">
        <h4 class="text-body-md-bold text-ink-deep mb-3">Sistem Denda</h4>
        <p class="text-body-sm text-steel mb-4">
            Aktifkan jika sekolah menerapkan denda untuk keterlambatan pengembalian.
        </p>

        @php $isFineActive = setting('is_fine_active') === 'true'; @endphp

        {{-- Toggle Denda --}}
        <div class="p-4 rounded-xl border-2 transition-all {{ $isFineActive ? 'border-primary bg-primary/5' : 'border-hairline-soft' }}">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" 
                       id="is_fine_active" 
                       name="is_fine_active" 
                       value="1"
                       @checked($isFineActive)
                       onchange="toggleFineInput(this)"
                       class="w-5 h-5 rounded border-hairline text-primary focus:ring-primary">
                <div>
                    <p class="text-body-sm-bold text-ink-deep">Aktifkan Sistem Denda</p>
                    <p class="text-caption text-steel mt-1">
                        Jika aktif, sistem akan otomatis menghitung denda keterlambatan.
                    </p>
                </div>
            </label>
        </div>

        {{-- Denda per Hari --}}
        <div id="fine-input-group" class="mt-4 {{ $isFineActive ? '' : 'hidden' }}">
            <label for="fine_per_day" class="form-label">
                Denda per Hari (Rp) <span class="text-critical">*</span>
            </label>
            <input id="fine_per_day" type="number" name="fine_per_day" 
                   value="{{ old('fine_per_day', setting('fine_per_day', 0)) }}"
                   min="0" step="1000"
                   class="form-input @error('fine_per_day') form-input-error @enderror">
            <p class="text-caption text-steel mt-1">
                Jumlah denda yang dikenakan per hari keterlambatan.
            </p>
            @error('fine_per_day')
                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    {{-- ============ ACTION ============ --}}
    <div class="flex items-center justify-end gap-3 pt-6 border-t border-hairline-soft">
        <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i>
            Simpan Perubahan
        </button>
    </div>
</form>

@push('scripts')
<script>
    function toggleFineInput(checkbox) {
        const group = document.getElementById('fine-input-group');
        const wrapper = checkbox.closest('.p-4');
        
        if (checkbox.checked) {
            group.classList.remove('hidden');
            wrapper.classList.add('border-primary', 'bg-primary/5');
            wrapper.classList.remove('border-hairline-soft');
        } else {
            group.classList.add('hidden');
            wrapper.classList.remove('border-primary', 'bg-primary/5');
            wrapper.classList.add('border-hairline-soft');
        }
    }
</script>
@endpush