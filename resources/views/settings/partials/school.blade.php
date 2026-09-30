{{-- resources/views/settings/partials/school.blade.php --}}
<form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="group_name" value="school">

    <div>
        <h3 class="text-heading-sm text-ink-deep mb-1">Informasi Sekolah</h3>
        <p class="text-body-sm text-steel">Data ini akan tampil di header aplikasi dan kop surat laporan.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Nama Sekolah --}}
        <div class="lg:col-span-2">
            <label for="school_name" class="form-label">
                Nama Sekolah <span class="text-critical">*</span>
            </label>
            <input id="school_name" type="text" name="school_name" 
                   value="{{ old('school_name', setting('school_name')) }}"
                   required maxlength="150"
                   class="form-input @error('school_name') form-input-error @enderror">
            @error('school_name')
                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Alamat --}}
        <div class="lg:col-span-2">
            <label for="school_address" class="form-label">
                Alamat Lengkap <span class="text-critical">*</span>
            </label>
            <textarea id="school_address" name="school_address" rows="2" required maxlength="500"
                      class="form-input !h-auto resize-none @error('school_address') form-input-error @enderror">{{ old('school_address', setting('school_address')) }}</textarea>
            @error('school_address')
                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Telepon --}}
        <div>
            <label for="school_phone" class="form-label">
                Telepon <span class="text-critical">*</span>
            </label>
            <input id="school_phone" type="text" name="school_phone" 
                   value="{{ old('school_phone', setting('school_phone')) }}"
                   required maxlength="30"
                   class="form-input @error('school_phone') form-input-error @enderror">
            @error('school_phone')
                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="school_email" class="form-label">
                Email <span class="text-critical">*</span>
            </label>
            <input id="school_email" type="email" name="school_email" 
                   value="{{ old('school_email', setting('school_email')) }}"
                   required maxlength="100"
                   class="form-input @error('school_email') form-input-error @enderror">
            @error('school_email')
                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- NPSN --}}
        <div>
            <label for="school_npsn" class="form-label">NPSN</label>
            <input id="school_npsn" type="text" name="school_npsn" 
                   value="{{ old('school_npsn', setting('school_npsn')) }}"
                   maxlength="30"
                   class="form-input">
            <p class="text-caption text-steel mt-1">Nomor Pokok Sekolah Nasional (opsional)</p>
        </div>

        {{-- Website --}}
        <div>
            <label for="school_website" class="form-label">Website</label>
            <input id="school_website" type="url" name="school_website" 
                   value="{{ old('school_website', setting('school_website')) }}"
                   placeholder="https://"
                   maxlength="150"
                   class="form-input @error('school_website') form-input-error @enderror">
            @error('school_website')
                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    {{-- ============ LOGO SEKOLAH ============ --}}
    <div class="pt-6 border-t border-hairline-soft">
        <h4 class="text-body-md-bold text-ink-deep mb-3">Logo Sekolah</h4>
        
        <div class="flex flex-wrap items-start gap-6">
            {{-- Preview Logo --}}
            <div class="flex-shrink-0">
                @php
                    $logoFile = setting('school_logo', 'logo-default.png');
                    $logoExists = $logoFile && $logoFile !== 'logo-default.png' 
                        && file_exists(storage_path('app/public/' . $logoFile));
                @endphp

                @if($logoExists)
                    <img id="logo-preview" 
                         src="{{ asset('storage/' . $logoFile) }}" 
                         alt="Logo"
                         class="w-32 h-32 rounded-2xl object-contain bg-surface-soft p-3 border-2 border-hairline-soft">
                @else
                    <div id="logo-placeholder"
                         class="w-32 h-32 rounded-2xl bg-primary/10 flex items-center justify-center border-2 border-hairline-soft">
                        <i class="fas fa-school text-primary text-4xl"></i>
                    </div>
                    <img id="logo-preview" src="" alt="Preview" class="w-32 h-32 rounded-2xl object-contain bg-surface-soft p-3 border-2 border-hairline-soft hidden">
                @endif
            </div>

            {{-- Upload Form --}}
            <div class="flex-1 min-w-[250px]">
                <input type="file" id="logo-input" name="school_logo" accept="image/*"
                       onchange="previewLogo(event)"
                       class="block w-full text-body-sm text-steel
                              file:mr-4 file:py-2.5 file:px-4 file:rounded-pill
                              file:border-0 file:text-caption-bold
                              file:bg-primary file:text-white
                              hover:file:bg-primary-deep file:cursor-pointer
                              file:transition-colors">
                <p class="text-caption text-steel mt-2">
                    Format: JPG, PNG, WEBP. Maks 2MB. Ukuran ideal: 512×512px.
                </p>
                @error('school_logo')
                    <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                        <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                    </p>
                @enderror

                @if($logoExists)
                    <button type="button"
                            onclick="document.getElementById('remove-logo-modal').classList.remove('hidden')"
                            class="mt-3 text-caption-bold text-critical hover:underline">
                        <i class="fas fa-trash"></i> Hapus Logo
                    </button>
                @endif
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

{{-- Modal Hapus Logo --}}
<div id="remove-logo-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus Logo Sekolah?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Logo akan dikembalikan ke default (inisial sekolah).
        </p>
        <div class="flex gap-3">
            <button type="button" 
                    onclick="document.getElementById('remove-logo-modal').classList.add('hidden')"
                    class="btn-ghost flex-1">
                Batal
            </button>
            <form method="POST" action="{{ route('settings.remove-logo') }}" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger w-full">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewLogo(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logo-preview');
            const placeholder = document.getElementById('logo-placeholder');
            
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush