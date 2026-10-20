{{-- resources/views/portal/bhp.blade.php --}}
@extends('portal.layouts.app')

@section('title', 'Permintaan BHP')

@section('content')

<div class="max-w-3xl mx-auto py-8 px-4">

    {{-- Back Button --}}
    <a href="{{ route('portal.index') }}" class="inline-flex items-center text-steel hover:text-primary mb-6 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Portal
    </a>

    <div class="card p-6 md:p-8">
        <div class="flex items-center gap-4 mb-8 border-b border-hairline-soft pb-6">
            <div class="w-12 h-12 rounded-xl bg-warning/10 flex items-center justify-center">
                <i class="fas fa-boxes text-warning text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-ink-deep">Permintaan BHP</h1>
                <p class="text-steel text-sm">Barang habis pakai langsung diambil, stok otomatis berkurang.</p>
            </div>
        </div>

        {{-- TAMPILKAN ERROR --}}
        @if($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-critical/10 border border-critical/20">
                <p class="text-body-sm-bold text-critical mb-2">Terjadi kesalahan:</p>
                <ul class="list-disc list-inside text-body-sm text-ink space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-xl bg-critical/10 border border-critical/20">
                <p class="text-body-sm text-ink">{{ session('error') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('portal.bhp.store') }}" id="bhp-form" class="space-y-6">
            @csrf

            {{-- ═══════════════════════════════════════════════ --}}
            {{-- SECTION 1: Identitas (Autofill) --}}
            {{-- ═══════════════════════════════════════════════ --}}
            @include('portal.partials.identity-autofill', ['type' => 'all'])

            {{-- ═══════════════════════════════════════════════ --}}
            {{-- SECTION 2: Pilih Barang --}}
            {{-- ═══════════════════════════════════════════════ --}}
            <div>
                <h3 class="text-lg font-bold text-ink-deep mb-4">Barang yang Diminta</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Pilih Barang --}}
                    <div class="md:col-span-2">
                        <label class="form-label">Pilih Barang <span class="text-critical">*</span></label>
                        <select name="item_id" id="item_id" required class="form-input">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" 
                                        data-stock="{{ $item->quantity }}"
                                        data-unit="{{ $item->unit ?? 'unit' }}"
                                        {{ $item->quantity <= $item->min_stock ? 'disabled' : '' }}>
                                    {{ $item->name }} 
                                    (Stok: {{ $item->quantity }})
                                    {{ $item->quantity <= $item->min_stock ? ' - HABIS' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Jumlah --}}
                    <div>
                        <label class="form-label">Jumlah <span class="text-critical">*</span></label>
                        <input type="number" name="quantity" id="quantity" min="1" value="1" required class="form-input">
                        <p class="text-caption text-steel mt-1" id="stock-info">Pilih barang dulu.</p>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════ --}}
            {{-- SECTION 3: Keperluan --}}
            {{-- ═══════════════════════════════════════════════ --}}
            <div>
                <label class="form-label">Keperluan <span class="text-critical">*</span></label>
                <textarea name="purpose" rows="3" required class="form-input !h-auto resize-none" placeholder="Contoh: Untuk keperluan ATK kelas X..."></textarea>
            </div>

            {{-- Submit --}}
            <div class="pt-4 border-t border-hairline-soft flex justify-end gap-3">
                <a href="{{ route('portal.index') }}" class="btn-ghost">Batal</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Permintaan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Update info stok saat barang dipilih
    document.getElementById('item_id').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const stock = opt.dataset.stock;
        const info = document.getElementById('stock-info');
        const qty = document.getElementById('quantity');
        
        if (stock) {
            info.innerHTML = `Stok tersedia: <strong>${stock}</strong>`;
            qty.max = stock;
            qty.value = 1;
        } else {
            info.textContent = 'Pilih barang dulu.';
            qty.max = '';
        }
    });
</script>
@endpush