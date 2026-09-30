{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk - {{ setting('app_short_name', 'SISARPRAS') }}</title>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-canvas text-ink min-h-screen">

    <div class="min-h-screen flex">

        {{-- ==================== LEFT PANEL: Brand Showcase ==================== --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-ink-deep">

            {{-- Decorative Cobalt Gradient --}}
            <div class="absolute -top-40 -right-40 w-[600px] h-[600px] rounded-circle bg-primary/30 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] rounded-circle bg-primary-soft/20 blur-3xl"></div>

            {{-- Subtle grid pattern --}}
            <div class="absolute inset-0 opacity-[0.03]" 
                 style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 40px 40px;"></div>

            {{-- Content --}}
            <div class="relative z-10 flex flex-col justify-between p-12 xl:p-16 w-full">

                {{-- Top: Logo + Brand --}}
                <div class="flex items-center gap-3">
                    @php
                        $logoFile = setting('school_logo', 'logo-default.png');
                        $logoExists = $logoFile 
                            && $logoFile !== 'logo-default.png' 
                            && file_exists(storage_path('app/public/' . $logoFile));
                        $schoolInitial = strtoupper(substr(setting('school_name', 'S'), 0, 1));
                    @endphp

                    @if($logoExists)
                        <img src="{{ asset('storage/' . $logoFile) }}" 
                             alt="Logo" 
                             class="w-11 h-11 rounded-xl object-contain bg-white/10 p-1.5 backdrop-blur">
                    @else
                        <div class="w-11 h-11 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-subtitle-lg">
                            {{ $schoolInitial }}
                        </div>
                    @endif

                    <div>
                        <p class="text-body-md-bold text-white leading-tight">
                            {{ setting('app_short_name', 'SISARPRAS') }}
                        </p>
                        <p class="text-caption text-white/50 leading-tight">
                            Sistem Informasi Sarpras
                        </p>
                    </div>
                </div>

                {{-- Middle: Hero Copy --}}
                <div class="max-w-md">
                    <div class="badge-info mb-5 inline-flex">
                        <i class="fas fa-shield-alt mr-2 text-xs"></i>
                        Sistem Internal Sekolah
                    </div>

                    <h1 class="text-hero text-white leading-tight mb-5">
                        Kelola inventaris<br>
                        sekolah dengan<br>
                        <span class="text-primary-soft">lebih cerdas.</span>
                    </h1>

                    <p class="text-subtitle-md text-white/60 leading-relaxed">
                        Pendataan barang, peminjaman, dan perawatan dalam satu platform terintegrasi untuk bagian Sarana & Prasarana.
                    </p>
                </div>

                {{-- Bottom: Stats + Footer --}}
                <div>
                    <div class="grid grid-cols-3 gap-6 mb-8">
                        <div>
                            <p class="text-heading-lg text-white">100%</p>
                            <p class="text-caption text-white/50 uppercase tracking-wider mt-1">Digital</p>
                        </div>
                        <div>
                            <p class="text-heading-lg text-white">24/7</p>
                            <p class="text-caption text-white/50 uppercase tracking-wider mt-1">Akses</p>
                        </div>
                        <div>
                            <p class="text-heading-lg text-white">Aman</p>
                            <p class="text-caption text-white/50 uppercase tracking-wider mt-1">Terpusat</p>
                        </div>
                    </div>

                    <p class="text-caption text-white/40">
                        &copy; {{ date('Y') }} {{ setting('school_name', 'Sekolah') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        {{-- ==================== RIGHT PANEL: Login Form ==================== --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 md:p-12">
            <div class="w-full max-w-md">

                {{-- Mobile Brand (hanya tampil < lg) --}}
                <div class="lg:hidden flex items-center gap-3 mb-8">
                    @if($logoExists)
                        <img src="{{ asset('storage/' . $logoFile) }}" 
                             alt="Logo" 
                             class="w-10 h-10 rounded-xl object-contain bg-surface-soft p-1">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-bold">
                            {{ $schoolInitial }}
                        </div>
                    @endif
                    <div>
                        <p class="text-body-md-bold text-ink-deep leading-tight">
                            {{ setting('app_short_name', 'SISARPRAS') }}
                        </p>
                        <p class="text-caption text-steel leading-tight">
                            {{ setting('school_name', 'Sekolah') }}
                        </p>
                    </div>
                </div>

                {{-- Heading --}}
                <div class="mb-8">
                    <h2 class="text-heading-lg text-ink-deep mb-2">
                        Selamat datang kembali
                    </h2>
                    <p class="text-subtitle-md text-steel">
                        Masuk dengan akun Anda untuk melanjutkan.
                    </p>
                </div>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-6 flex items-start gap-3 p-4 rounded-xl bg-success/10 border border-success/20">
                        <i class="fas fa-check-circle text-success text-lg mt-0.5"></i>
                        <p class="text-body-sm text-ink">{{ session('status') }}</p>
                    </div>
                @endif

                {{-- Login Form --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="form-label">Alamat Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required 
                                   autofocus 
                                   autocomplete="username"
                                   placeholder="nama@sekolah.sch.id"
                                   class="form-input pl-11 @error('email') form-input-error @enderror">
                        </div>
                        @error('email')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="form-label !mb-0">Kata Sandi</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" 
                                   class="text-caption-bold text-primary hover:text-primary-deep transition-colors">
                                    Lupa sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="form-input pl-11 pr-11 @error('password') form-input-error @enderror">
                            
                            {{-- Toggle Password --}}
                            <button type="button" 
                                    onclick="togglePassword()"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-steel hover:text-ink transition-colors">
                                <i id="toggle-icon" class="far fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center">
                        <input id="remember_me" 
                               type="checkbox" 
                               name="remember"
                               class="w-4 h-4 rounded border-hairline text-primary focus:ring-primary focus:ring-offset-0">
                        <label for="remember_me" class="ml-2 text-body-sm text-ink cursor-pointer">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk ke Sistem
                    </button>
                </form>

                {{-- Divider --}}
                <div class="flex items-center gap-4 my-6">
                    <div class="flex-1 h-px bg-hairline-soft"></div>
                    <span class="text-caption text-stone">atau</span>
                    <div class="flex-1 h-px bg-hairline-soft"></div>
                </div>

                {{-- Info Box --}}
                <div class="p-4 rounded-xl bg-surface-soft border border-hairline-soft">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-primary mt-0.5"></i>
                        <div>
                            <p class="text-body-sm-bold text-ink-deep">Belum punya akun?</p>
                            <p class="text-body-sm text-steel mt-1">
                                Hubungi bagian <strong>Administrator</strong> atau <strong>Sarpras</strong> untuk mendapatkan akses.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Footer Mobile --}}
                <p class="lg:hidden text-center text-caption text-stone mt-8">
                    &copy; {{ date('Y') }} {{ setting('school_name', 'Sekolah') }}
                </p>

            </div>
        </div>
    </div>

    {{-- Toggle Password Script --}}
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggle-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>