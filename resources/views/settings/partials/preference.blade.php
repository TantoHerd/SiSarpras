{{-- resources/views/settings/partials/preference.blade.php --}}
<form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="group_name" value="preference">
    <input type="hidden" name="redirect_tab" value="preference">

    <div>
        <h3 class="text-heading-sm text-ink-deep mb-1">Preferensi Aplikasi</h3>
        <p class="text-body-sm text-steel">Pengaturan tampilan dan format data di aplikasi.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Nama Aplikasi --}}
        <div>
            <label for="app_name" class="form-label">
                Nama Aplikasi <span class="text-critical">*</span>
            </label>
            <input id="app_name" type="text" name="app_name" 
                   value="{{ old('app_name', setting('app_name')) }}"
                   required maxlength="100"
                   class="form-input @error('app_name') form-input-error @enderror">
            @error('app_name')
                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Singkatan --}}
        <div>
            <label for="app_short_name" class="form-label">
                Singkatan Aplikasi <span class="text-critical">*</span>
            </label>
            <input id="app_short_name" type="text" name="app_short_name" 
                   value="{{ old('app_short_name', setting('app_short_name')) }}"
                   required maxlength="30"
                   class="form-input @error('app_short_name') form-input-error @enderror">
            @error('app_short_name')
                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Versi --}}
        <div>
            <label for="app_version" class="form-label">
                Versi Aplikasi <span class="text-critical">*</span>
            </label>
            <input id="app_version" type="text" name="app_version" 
                   value="{{ old('app_version', setting('app_version')) }}"
                   required maxlength="20"
                   placeholder="1.0.0"
                   class="form-input">
        </div>

        {{-- Timezone --}}
        <div>
            <label for="timezone" class="form-label">
                Zona Waktu <span class="text-critical">*</span>
            </label>
            <select id="timezone" name="timezone" required
                    class="form-input @error('timezone') form-input-error @enderror">
                @php
                    $timezones = [
                        'Asia/Jakarta'   => 'WIB (Asia/Jakarta)',
                        'Asia/Makassar'  => 'WITA (Asia/Makassar)',
                        'Asia/Jayapura'  => 'WIT (Asia/Jayapura)',
                        'Asia/Singapore' => 'SGT (Asia/Singapore)',
                        'UTC'            => 'UTC',
                    ];
                @endphp
                @foreach($timezones as $value => $label)
                    <option value="{{ $value }}" @selected(setting('timezone') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Format Tanggal --}}
        <div>
            <label for="date_format" class="form-label">
                Format Tanggal <span class="text-critical">*</span>
            </label>
            <select id="date_format" name="date_format" required class="form-input">
                @php
                    $formats = [
                        'd-m-Y'  => 'DD-MM-YYYY (' . now()->format('d-m-Y') . ')',
                        'd/m/Y'  => 'DD/MM/YYYY (' . now()->format('d/m/Y') . ')',
                        'Y-m-d'  => 'YYYY-MM-DD (' . now()->format('Y-m-d') . ')',
                        'j F Y'  => 'D Month YYYY (' . now()->translatedFormat('j F Y') . ')',
                    ];
                @endphp
                @foreach($formats as $value => $label)
                    <option value="{{ $value }}" @selected(setting('date_format') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Simbol Mata Uang --}}
        <div>
            <label for="currency_symbol" class="form-label">
                Simbol Mata Uang <span class="text-critical">*</span>
            </label>
            <input id="currency_symbol" type="text" name="currency_symbol" 
                   value="{{ old('currency_symbol', setting('currency_symbol')) }}"
                   required maxlength="10"
                   class="form-input">
        </div>

        {{-- Posisi Mata Uang --}}
        <div>
            <label for="currency_position" class="form-label">
                Posisi Simbol <span class="text-critical">*</span>
            </label>
            <select id="currency_position" name="currency_position" required class="form-input">
                <option value="before" @selected(setting('currency_position') === 'before')>
                    Sebelum angka (Rp 1.500.000)
                </option>
                <option value="after" @selected(setting('currency_position') === 'after')>
                    Setelah angka (1.500.000 Rp)
                </option>
            </select>
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