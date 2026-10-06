{{-- resources/views/settings/partials/portal.blade.php --}}
@php
    // Pre-compute enabled state (hindari kompleks di directive)
    $portalEnabled = setting('portal_siswa_enabled') === 'true' 
        || setting('portal_siswa_enabled') === true;
    
    $portalMaxLoans = old('portal_siswa_max_loans', setting('portal_siswa_max_loans', 3));
    $portalMaxDays  = old('portal_siswa_max_days', setting('portal_siswa_max_days', 7));
    $portalWelcome  = old('portal_siswa_welcome_text', setting('portal_siswa_welcome_text', ''));
    
    $portalUrl = url('/portal');
@endphp

<form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="group_name" value="portal">
    <input type="hidden" name="redirect_tab" value="portal">

    {{-- ============ HEADER ============ --}}
    <div>
        <h3 class="text-heading-sm text-ink-deep mb-1">Portal Siswa</h3>
        <p class="text-body-sm text-steel">
            Konfigurasi portal peminjaman mandiri untuk siswa.
        </p>
    </div>

    <div class="space-y-5">

        {{-- ============ TOGGLE UTAMA ============ --}}
        <label id="portal-toggle-card"
               class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors
                      {{ $portalEnabled 
                         ? 'border-primary bg-primary/5' 
                         : 'border-hairline-soft bg-canvas hover:border-hairline' }}">
            <input type="hidden" name="portal_siswa_enabled" value="0">
            <input type="checkbox" 
                   name="portal_siswa_enabled" 
                   value="1"
                   id="portal-toggle"
                   @checked($portalEnabled)
                   onchange="togglePortalCard(this)"
                   class="mt-0.5 w-5 h-5 rounded border-hairline-soft text-primary focus:ring-primary">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <p class="text-body-sm-bold text-ink-deep">Aktifkan Portal Siswa</p>
                    <span class="badge-info text-xs">
                        <i class="fas fa-external-link-alt mr-1"></i>
                        {{ $portalUrl }}
                    </span>
                </div>
                <p class="text-caption text-steel">
                    Kalau diaktifkan, siswa dapat mengakses halaman portal untuk mengajukan peminjaman secara mandiri tanpa login.
                </p>
            </div>
        </label>

        {{-- ============ BATAS PINJAMAN ============ --}}
        <div>
            <h4 class="text-body-md-bold text-ink-deep mb-3 flex items-center gap-2">
                <i class="fas fa-shield-alt text-primary"></i>
                Batasan Peminjaman
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Max Loans --}}
                <div>
                    <label for="portal_siswa_max_loans" class="form-label">
                        Maksimal Pinjaman Aktif per Siswa
                    </label>
                    <div class="relative">
                        <input id="portal_siswa_max_loans" 
                               type="number" 
                               name="portal_siswa_max_loans" 
                               value="{{ $portalMaxLoans }}"
                               min="1" 
                               max="20"
                               class="form-input pr-16 @error('portal_siswa_max_loans') form-input-error @enderror">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-caption text-steel font-bold">
                            pinjaman
                        </span>
                    </div>
                    <p class="text-caption text-steel mt-1">
                        Siswa tidak bisa mengajukan jika sudah mencapai batas ini.
                    </p>
                    @error('portal_siswa_max_loans')
                        <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Max Days --}}
                <div>
                    <label for="portal_siswa_max_days" class="form-label">
                        Durasi Pinjam Siswa
                    </label>
                    <div class="relative">
                        <input id="portal_siswa_max_days" 
                               type="number" 
                               name="portal_siswa_max_days" 
                               value="{{ $portalMaxDays }}"
                               min="1" 
                               max="30"
                               class="form-input pr-12 @error('portal_siswa_max_days') form-input-error @enderror">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-caption text-steel font-bold">
                            hari
                        </span>
                    </div>
                    <p class="text-caption text-steel mt-1">
                        Jatuh tempo otomatis dihitung dari tanggal pinjam.
                    </p>
                    @error('portal_siswa_max_days')
                        <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ============ TEKS SAMBUTAN ============ --}}
        <div>
            <h4 class="text-body-md-bold text-ink-deep mb-3 flex items-center gap-2">
                <i class="fas fa-comment-dots text-primary"></i>
                Teks Sambutan
            </h4>

            <textarea id="portal_siswa_welcome_text" 
                      name="portal_siswa_welcome_text" 
                      rows="3"
                      maxlength="500"
                      placeholder="Selamat datang di Portal Peminjaman Siswa..."
                      class="form-input !h-auto resize-none @error('portal_siswa_welcome_text') form-input-error @enderror">{{ $portalWelcome }}</textarea>
            <div class="flex items-center justify-between mt-1">
                <p class="text-caption text-steel">
                    Tampil di halaman utama portal.
                </p>
                <p class="text-caption text-steel">
                    <span id="portal-char-count">{{ strlen($portalWelcome) }}</span>/500
                </p>
            </div>
            @error('portal_siswa_welcome_text')
                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- ============ PREVIEW LINK ============ --}}
        <div class="p-4 rounded-xl bg-primary/5 border border-primary/20">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-link text-primary"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-body-sm-bold text-ink-deep">Link Portal</p>
                    <p class="text-caption text-steel mt-1 mb-2">
                        Bagikan link ini ke siswa:
                    </p>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 px-3 py-2 rounded-lg bg-canvas border border-hairline-soft text-body-sm font-mono truncate">
                            {{ $portalUrl }}
                        </code>
                        <button type="button"
                                data-portal-url="{{ $portalUrl }}"
                                onclick="copyPortalLink(this)"
                                class="btn-ghost !py-2 !px-3 !text-xs">
                            <i class="fas fa-copy"></i>
                            Salin
                        </button>
                    </div>
                </div>
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
    // ============ Toggle Portal Card ============
    function togglePortalCard(checkbox) {
        const card = document.getElementById('portal-toggle-card');
        if (!card) return;
        
        if (checkbox.checked) {
            card.classList.add('border-primary', 'bg-primary/5');
            card.classList.remove('border-hairline-soft', 'bg-canvas');
        } else {
            card.classList.remove('border-primary', 'bg-primary/5');
            card.classList.add('border-hairline-soft', 'bg-canvas');
        }
    }

    // ============ Copy Portal Link ============
    function copyPortalLink(btn) {
        const url = btn.dataset.portalUrl || '';
        
        navigator.clipboard.writeText(url).then(() => {
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Tersalin';
            
            setTimeout(() => {
                btn.innerHTML = original;
            }, 1500);
        }).catch(err => {
            console.error('Failed to copy:', err);
            alert('Gagal menyalin link. Silakan copy manual: ' + url);
        });
    }

    // ============ Char Counter ============
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('portal_siswa_welcome_text');
        const counter = document.getElementById('portal-char-count');
        
        if (textarea && counter) {
            textarea.addEventListener('input', function() {
                counter.textContent = this.value.length;
            });
        }
    });
</script>
@endpush