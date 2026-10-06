{{-- 
    Partial Form Sumber Dana
    Variabel: $fundingSource (nullable — null saat create)
--}}

@php
    $isEdit = !is_null($fundingSource);
@endphp

{{-- Kode --}}
<div>
    <label for="code" class="block text-sm font-semibold text-[#0a1317] mb-1.5">
        Kode <span class="text-[#e41e3f]">*</span>
    </label>
    <input type="text"
           id="code"
           name="code"
           value="{{ old('code', $fundingSource->code ?? '') }}"
           maxlength="20"
           placeholder="Contoh: BOS"
           class="form-input w-full uppercase font-mono @error('code') border-[#e41e3f] @enderror"
           style="text-transform: uppercase;">
    <p class="text-xs text-gray-500 mt-1">Hanya huruf kapital, angka, dan tanda hubung (-). Maks 20 karakter.</p>
    @error('code')
        <p class="text-xs text-[#e41e3f] mt-1.5">{{ $message }}</p>
    @enderror
</div>

{{-- Nama --}}
<div>
    <label for="name" class="block text-sm font-semibold text-[#0a1317] mb-1.5">
        Nama <span class="text-[#e41e3f]">*</span>
    </label>
    <input type="text"
           id="name"
           name="name"
           value="{{ old('name', $fundingSource->name ?? '') }}"
           maxlength="100"
           placeholder="Contoh: Bantuan Operasional Sekolah"
           class="form-input w-full @error('name') border-[#e41e3f] @enderror">
    @error('name')
        <p class="text-xs text-[#e41e3f] mt-1.5">{{ $message }}</p>
    @enderror
</div>

{{-- Deskripsi --}}
<div>
    <label for="description" class="block text-sm font-semibold text-[#0a1317] mb-1.5">
        Deskripsi <span class="text-gray-400 text-xs font-normal">(opsional)</span>
    </label>
    <textarea id="description"
              name="description"
              rows="3"
              maxlength="500"
              placeholder="Keterangan tambahan tentang sumber dana ini..."
              class="form-input w-full @error('description') border-[#e41e3f] @enderror">{{ old('description', $fundingSource->description ?? '') }}</textarea>
    @error('description')
        <p class="text-xs text-[#e41e3f] mt-1.5">{{ $message }}</p>
    @enderror
</div>

{{-- Status Aktif --}}
<div>
    <label class="block text-sm font-semibold text-[#0a1317] mb-1.5">Status</label>
    <label class="inline-flex items-center gap-3 cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox"
               name="is_active"
               value="1"
               @checked(old('is_active', $fundingSource->is_active ?? true))
               class="w-5 h-5 rounded border-gray-300 text-[#0064e0] focus:ring-[#0064e0]">
        <span class="text-sm text-gray-700">
            Aktif
            <span class="text-xs text-gray-400 block">Nonaktifkan jika sumber dana ini sudah tidak dipakai.</span>
        </span>
    </label>
    @error('is_active')
        <p class="text-xs text-[#e41e3f] mt-1.5">{{ $message }}</p>
    @enderror
</div>