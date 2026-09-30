{{-- resources/views/settings/partials/headmaster.blade.php --}}
<form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="group_name" value="school">
    <input type="hidden" name="redirect_tab" value="headmaster">

    <div>
        <h3 class="text-heading-sm text-ink-deep mb-1">Informasi Kepala Sekolah</h3>
        <p class="text-body-sm text-steel">
            Data ini akan muncul di bagian tanda tangan laporan PDF.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Nama --}}
        <div class="lg:col-span-2">
            <label for="headmaster_name" class="form-label">Nama Kepala Sekolah</label>
            <input id="headmaster_name" type="text" name="headmaster_name" 
                   value="{{ old('headmaster_name', setting('headmaster_name')) }}"
                   placeholder="Contoh: Dr. H. Ahmad Fauzi, M.Pd"
                   maxlength="150"
                   class="form-input">
            <p class="text-caption text-steel mt-1">
                Nama ini akan muncul di tanda tangan laporan.
            </p>
        </div>

        {{-- NIP --}}
        <div>
            <label for="headmaster_nip" class="form-label">NIP Kepala Sekolah</label>
            <input id="headmaster_nip" type="text" name="headmaster_nip" 
                   value="{{ old('headmaster_nip', setting('headmaster_nip')) }}"
                   placeholder="Contoh: 197505121999031005"
                   maxlength="50"
                   class="form-input">
        </div>
    </div>

    {{-- Preview Tanda Tangan --}}
    <div class="pt-6 border-hairline-soft">
        <h4 class="text-body-md-bold text-ink-deep mb-3">Preview Tanda Tangan</h4>
        <div class="p-6 bg-surface-soft rounded-xl">
            <div class="text-right max-w-sm ml-auto">
                <p class="text-body-sm text-ink mb-2">{{ now()->translatedFormat('d F Y') }}</p>
                <p class="text-body-sm text-ink mb-16">Kepala Sekolah,</p>
                <p id="preview-headmaster-name" 
                class="text-body-sm-bold text-ink border-t border-ink pt-2 inline-block min-w-[200px] text-center">
                    {{ old('headmaster_name', setting('headmaster_name')) ?: '_______________________' }}
                </p>
                <p id="preview-headmaster-nip" 
                class="text-caption text-steel mt-1">
                    @if(old('headmaster_nip', setting('headmaster_nip')))
                        NIP. {{ old('headmaster_nip', setting('headmaster_nip')) }}
                    @endif
                </p>
            </div>
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
    // Live preview tanda tangan
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('headmaster_name');
        const nipInput = document.getElementById('headmaster_nip');
        const namePreview = document.getElementById('preview-headmaster-name');
        const nipPreview = document.getElementById('preview-headmaster-nip');

        function updateSignaturePreview() {
            // Nama
            const name = nameInput.value.trim();
            namePreview.textContent = name || '_______________________';

            // NIP
            const nip = nipInput.value.trim();
            if (nip) {
                nipPreview.textContent = 'NIP. ' + nip;
                nipPreview.style.display = '';
            } else {
                nipPreview.textContent = '';
                nipPreview.style.display = 'none';
            }
        }

        if (nameInput) {
            nameInput.addEventListener('input', updateSignaturePreview);
            updateSignaturePreview(); // Init
        }

        if (nipInput) {
            nipInput.addEventListener('input', updateSignaturePreview);
        }
    });
</script>
@endpush