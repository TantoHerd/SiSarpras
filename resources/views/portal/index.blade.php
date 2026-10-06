{{-- resources/views/portal/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Peminjaman Siswa - {{ setting('school_name', 'Sekolah') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, sans-serif;
            background:
                radial-gradient(circle at 20% 20%, rgba(0, 100, 224, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(10, 19, 23, 0.06) 0%, transparent 50%),
                #f1f4f7;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="max-w-4xl mx-auto py-8">

        {{-- ============ HEADER ============ --}}
        <div class="text-center mb-8">
            @php
                $logoFile = setting('school_logo', 'logo-default.png');
                $logoExists = $logoFile
                    && $logoFile !== 'logo-default.png'
                    && file_exists(storage_path('app/public/' . $logoFile));
                $schoolInitial = strtoupper(substr(setting('school_name', 'S'), 0, 1));
            @endphp

            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-primary to-primary-deep shadow-lg mb-4 overflow-hidden">
                @if($logoExists)
                    <img src="{{ asset('storage/' . $logoFile) }}" alt="Logo" class="w-full h-full object-contain p-2">
                @else
                    <span class="text-white font-bold text-3xl">{{ $schoolInitial }}</span>
                @endif
            </div>
            <h1 class="text-heading-lg text-ink-deep">{{ setting('school_name', 'Sekolah') }}</h1>
            <p class="text-body-md text-steel mt-1">Portal Peminjaman Siswa</p>
        </div>

        {{-- ============ WELCOME CARD ============ --}}
        <div class="card mb-6 bg-primary/5 border-primary/20">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-hand-holding-heart text-primary text-lg"></i>
                </div>
                <div>
                    <h2 class="text-body-md-bold text-ink-deep mb-1">Selamat Datang!</h2>
                    <p class="text-body-sm text-steel">{{ $welcomeText }}</p>
                </div>
            </div>
        </div>

        {{-- ============ FLASH MESSAGES ============ --}}
        @if(session('error'))
            <div class="card mb-6 bg-critical/5 border-critical/20">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-critical mt-0.5"></i>
                    <p class="text-body-sm text-ink">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="card mb-6 bg-success/5 border-success/20">
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-success mt-0.5"></i>
                    <p class="text-body-sm text-ink">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- ============ FORM ============ --}}
        <form method="POST" action="{{ route('portal.store') }}" id="portal-form" class="space-y-6">
            @csrf

            {{-- Step 1: Identitas --}}
            <div class="card">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-body-sm-bold">1</div>
                    <h3 class="text-body-md-bold text-ink-deep">Data Diri</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nis" class="form-label">
                            NIS <span class="text-critical">*</span>
                        </label>
                        <input id="nis" 
                               type="text" 
                               name="nis" 
                               value="{{ old('nis') }}"
                               placeholder="Contoh: 2024001"
                               required
                               maxlength="20"
                               class="form-input font-mono @error('nis') form-input-error @enderror">
                        @error('nis')
                            <p class="mt-2 text-body-sm text-critical-strong">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="student_phone" class="form-label">
                            No HP
                            <span class="text-caption text-steel font-normal ml-1">(opsional)</span>
                        </label>
                        <input id="student_phone" 
                               type="text" 
                               name="student_phone" 
                               value="{{ old('student_phone') }}"
                               placeholder="Sesuai data sekolah"
                               maxlength="20"
                               class="form-input @error('student_phone') form-input-error @enderror">
                        <p class="text-caption text-steel mt-1">
                            Isi jika terdaftar di sekolah (untuk verifikasi).
                        </p>
                    </div>
                </div>
            </div>

            {{-- Step 2: Pilih Barang --}}
            <div class="card">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-body-sm-bold">2</div>
                    <h3 class="text-body-md-bold text-ink-deep">Pilih Barang</h3>
                </div>

                @if($items->count() > 0)
                    <div class="space-y-3 max-h-[400px] overflow-y-auto pr-1">
                        @foreach($items as $item)
                            <label class="cursor-pointer block">
                                <input type="radio" 
                                       name="item_id" 
                                       value="{{ $item->id }}"
                                       data-quantity="{{ $item->quantity }}"
                                       data-mode="{{ $item->effective_tracking_mode }}"
                                       @checked(old('item_id') == $item->id)
                                       onchange="selectItem(this)"
                                       class="sr-only item-radio">
                                <div class="item-card p-4 rounded-xl border-2 border-hairline-soft bg-canvas hover:border-hairline transition-all">
                                    <div class="flex items-center gap-3">
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
                                            <p class="text-body-sm-bold text-ink-deep">{{ $item->name }}</p>
                                            <p class="text-caption text-steel font-mono">{{ $item->code }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="badge-info text-xs">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    {{ $item->category->name ?? '-' }}
                                                </span>
                                                <span class="text-caption text-steel">
                                                    Stok: {{ $item->quantity }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="check-icon w-6 h-6 rounded-full border-2 border-hairline flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-check text-white text-xs opacity-0"></i>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-box-open text-steel text-3xl mb-3"></i>
                        <p class="text-body-sm text-steel">Tidak ada barang tersedia saat ini.</p>
                    </div>
                @endif

                @error('item_id')
                    <p class="mt-3 text-body-sm text-critical-strong">{{ $message }}</p>
                @enderror
            </div>

            {{-- Step 3: Detail --}}
            <div class="card">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-body-sm-bold">3</div>
                    <h3 class="text-body-md-bold text-ink-deep">Detail Peminjaman</h3>
                </div>

                <div class="space-y-4">
                    {{-- Quantity (muncul kalau per_batch) --}}
                    <div id="quantity-wrapper" class="hidden">
                        <label for="quantity" class="form-label">Jumlah</label>
                        <p class="text-caption text-steel mb-2" id="quantity-info"></p>
                        <input id="quantity" 
                               type="number" 
                               name="quantity" 
                               value="{{ old('quantity', 1) }}"
                               min="1"
                               class="form-input @error('quantity') form-input-error @enderror">
                        @error('quantity')
                            <p class="mt-2 text-body-sm text-critical-strong">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Purpose --}}
                    <div>
                        <label for="purpose" class="form-label">
                            Keperluan <span class="text-critical">*</span>
                        </label>
                        <textarea id="purpose" 
                                  name="purpose" 
                                  rows="4"
                                  required
                                  placeholder="Contoh: Untuk praktikum Biologi kelas X IPA 1..."
                                  class="form-input !h-auto resize-none @error('purpose') form-input-error @enderror">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <p class="mt-2 text-body-sm text-critical-strong">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="card bg-warning/5 border-warning/20">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-warning mt-0.5"></i>
                    <div>
                        <p class="text-body-sm-bold text-ink-deep">Perhatian</p>
                        <ul class="text-body-sm text-steel mt-1 space-y-1 list-disc list-inside">
                            <li>Permintaan akan direview oleh petugas sarpras.</li>
                            <li>Anda dapat cek status di halaman <a href="{{ route('portal.status') }}" class="text-primary font-bold hover:underline">Cek Status</a>.</li>
                            <li>Barang harus dikembalikan sesuai tanggal jatuh tempo.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('portal.status') }}" class="btn-ghost">
                    <i class="fas fa-search"></i>
                    Cek Status
                </a>
                <button type="submit" id="submit-btn" class="btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Kirim Permintaan
                </button>
            </div>

        </form>

        {{-- Footer --}}
        <div class="text-center mt-8 text-caption text-steel">
            <p>{{ setting('app_name', 'SISARPRAS') }} v{{ setting('app_version', '1.0.0') }}</p>
            <p class="mt-1">© {{ date('Y') }} {{ setting('school_name', 'Sekolah') }}</p>
        </div>

    </div>

    {{-- ============ SCRIPTS ============ --}}
    <script>
        // ============ Global error handler untuk debug ============
        window.addEventListener('error', function(e) {
            console.error('JS Error:', e.error || e.message);
        });

        // ============ Select Item Function ============
        function selectItem(radio) {
            const quantity = parseInt(radio.dataset.quantity);
            const mode = radio.dataset.mode;

            // Reset semua card
            document.querySelectorAll('.item-card').forEach(card => {
                card.classList.remove('border-primary', 'bg-primary/5');
                card.classList.add('border-hairline-soft');
            });
            document.querySelectorAll('.check-icon').forEach(icon => {
                icon.classList.remove('bg-primary', 'border-primary');
                icon.classList.add('border-hairline');
            });
            document.querySelectorAll('.check-icon i').forEach(i => {
                i.classList.add('opacity-0');
            });

            // Highlight selected card
            const label = radio.closest('label');
            const card = label.querySelector('.item-card');
            const checkIcon = label.querySelector('.check-icon');
            
            card.classList.remove('border-hairline-soft');
            card.classList.add('border-primary', 'bg-primary/5');

            checkIcon.classList.remove('border-hairline');
            checkIcon.classList.add('bg-primary', 'border-primary');
            checkIcon.querySelector('i').classList.remove('opacity-0');

            // Update quantity field
            const wrapper = document.getElementById('quantity-wrapper');
            const info = document.getElementById('quantity-info');
            const input = document.getElementById('quantity');

            if (mode === 'per_batch') {
                wrapper.classList.remove('hidden');
                info.textContent = `Stok tersedia: ${quantity} unit`;
                input.max = quantity;
                input.value = Math.min(parseInt(input.value) || 1, quantity);
            } else {
                wrapper.classList.add('hidden');
                input.value = 1;
            }
        }

        // ============ Init on Load ============
        document.addEventListener('DOMContentLoaded', function () {
            // Init selected item
            const checked = document.querySelector('.item-radio:checked');
            if (checked) selectItem(checked);

            // Debug: log form submit (JANGAN preventDefault)
            const form = document.getElementById('portal-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('✅ Form submit fired');
                    console.log('   Action:', this.action);
                    console.log('   Method:', this.method);
                    
                    // Cek apakah ada item yang dipilih
                    const itemChecked = this.querySelector('input[name="item_id"]:checked');
                    if (!itemChecked) {
                        console.warn('⚠️ No item selected');
                    } else {
                        console.log('   Item ID:', itemChecked.value);
                    }
                });
            }
        });
    </script>

</body>
</html>