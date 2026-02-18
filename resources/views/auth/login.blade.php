@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex bg-gray-950 text-white relative overflow-hidden">

    {{-- ═══════════════════════════════════════════════════════════
         ANIMATED BACKGROUND ELEMENTS
    ═══════════════════════════════════════════════════════════ --}}
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        {{-- Gradient Orbs --}}
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600 rounded-full opacity-20 blur-[120px] animate-pulse"></div>
        <div class="absolute top-1/2 -right-40 w-80 h-80 bg-purple-600 rounded-full opacity-15 blur-[100px] animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute -bottom-40 left-1/3 w-72 h-72 bg-blue-600 rounded-full opacity-10 blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>
        {{-- Grid Pattern --}}
        <div class="absolute inset-0" style="background-image: linear-gradient(rgba(99,102,241,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(99,102,241,0.05) 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         LEFT PANEL: BRANDING (Hidden on Mobile)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="hidden lg:flex lg:w-[55%] relative flex-col justify-between p-14 z-10">

        {{-- Top: Logo & School Name --}}
        <div class="flex items-center space-x-3">
            @if($globalSettings['logo_url'])
                <img src="{{ $globalSettings['logo_url'] }}" alt="Logo" class="h-11 w-11 rounded-2xl object-cover border border-white/10 shadow-xl">
            @else
                <div class="h-11 w-11 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl shadow-indigo-900/50">
                    <i class="fas fa-clipboard-check text-white text-lg"></i>
                </div>
            @endif
            <div>
                <span class="text-white font-bold text-base tracking-wide">{{ $globalSettings['school_name'] ?? 'E-Absensi' }}</span>
                <p class="text-indigo-400 text-xs font-medium">Sistem Absensi Digital</p>
            </div>
        </div>

        {{-- Center: Hero Content --}}
        <div class="space-y-8">
            {{-- Badge --}}
            <div class="inline-flex items-center space-x-2 bg-indigo-500/10 border border-indigo-500/20 rounded-full px-4 py-1.5">
                <span class="h-2 w-2 bg-green-400 rounded-full animate-pulse"></span>
                <span class="text-indigo-300 text-xs font-semibold tracking-wider uppercase">Sistem Aktif & Real-Time</span>
            </div>

            <div>
                <h1 class="text-5xl xl:text-6xl font-black text-white leading-[1.1] tracking-tight mb-5">
                    Kelola Absensi
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400">
                        Lebih Cerdas
                    </span>
                </h1>
                <p class="text-gray-400 text-lg leading-relaxed max-w-md">
                    Platform digital terintegrasi untuk memantau kehadiran siswa, laporan akademik, dan komunikasi sekolah secara real-time.
                </p>
            </div>

            {{-- Feature Pills --}}
            <div class="flex flex-wrap gap-3">
                @foreach([
                    ['icon' => 'fa-qrcode', 'text' => 'Scan QR Code'],
                    ['icon' => 'fa-chart-line', 'text' => 'Laporan Real-Time'],
                    ['icon' => 'fa-bell', 'text' => 'Notifikasi Instan'],
                ] as $feature)
                <div class="flex items-center space-x-2 bg-white/5 border border-white/10 rounded-xl px-4 py-2 backdrop-blur-sm">
                    <i class="fas {{ $feature['icon'] }} text-indigo-400 text-sm"></i>
                    <span class="text-gray-300 text-sm font-medium">{{ $feature['text'] }}</span>
                </div>
                @endforeach
            </div>

            {{-- Stats Row --}}
            <div class="grid grid-cols-3 gap-4 pt-4 border-t border-white/10">
                @foreach([
                    ['value' => '99.9%', 'label' => 'Uptime'],
                    ['value' => 'Real-Time', 'label' => 'Data Sync'],
                    ['value' => 'Aman', 'label' => 'Terenkripsi'],
                ] as $stat)
                <div>
                    <p class="text-2xl font-black text-white">{{ $stat['value'] }}</p>
                    <p class="text-gray-500 text-xs mt-0.5">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom: Copyright --}}
        <div class="text-gray-600 text-sm">
            &copy; {{ date('Y') }} {{ $globalSettings['school_name'] ?? 'E-Absensi Siswa' }}. All rights reserved.
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         RIGHT PANEL: LOGIN FORM
    ═══════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 z-10 relative">

        {{-- Glassmorphism Card --}}
        <div class="w-full max-w-md">

            {{-- Mobile Logo --}}
            <div class="lg:hidden text-center mb-8">
                @if($globalSettings['logo_url'])
                    <img src="{{ $globalSettings['logo_url'] }}" alt="Logo" class="h-14 w-14 rounded-2xl object-cover mx-auto mb-3 border border-white/10">
                @else
                    <div class="h-14 w-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-xl">
                        <i class="fas fa-clipboard-check text-white text-2xl"></i>
                    </div>
                @endif
                <h2 class="text-xl font-bold text-white">{{ $globalSettings['school_name'] ?? 'E-Absensi Siswa' }}</h2>
            </div>

            {{-- Card --}}
            <div class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-3xl p-8 shadow-2xl shadow-black/50">

                {{-- Header --}}
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl mb-4 shadow-lg shadow-indigo-900/50">
                        <i class="fas fa-sign-in-alt text-white text-lg"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">Selamat Datang</h2>
                    <p class="text-gray-400 text-sm mt-1">Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                {{-- Alerts --}}
                @if (session('status') || session('success'))
                    <div class="mb-6 flex items-start space-x-3 bg-green-500/10 border border-green-500/20 rounded-2xl p-4">
                        <i class="fas fa-check-circle text-green-400 mt-0.5 flex-shrink-0"></i>
                        <p class="text-sm text-green-300">{{ session('status') ?? session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 flex items-start space-x-3 bg-red-500/10 border border-red-500/20 rounded-2xl p-4">
                        <i class="fas fa-exclamation-circle text-red-400 mt-0.5 flex-shrink-0"></i>
                        <p class="text-sm text-red-300">{{ $errors->first() }}</p>
                    </div>
                @endif

                {{-- Form --}}
                <form action="{{ route('login') }}" method="POST" class="space-y-5" id="loginForm">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-500 text-sm"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 rounded-xl pl-11 pr-4 py-3.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50
                                       hover:border-white/20 transition-all duration-200"
                                placeholder="nama@email.com" value="{{ old('email') }}">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-500 text-sm"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 rounded-xl pl-11 pr-12 py-3.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50
                                       hover:border-white/20 transition-all duration-200"
                                placeholder="••••••••">
                            {{-- Toggle Password Visibility --}}
                            <button type="button" id="togglePassword"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-gray-300 transition-colors">
                                <i class="fas fa-eye text-sm" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-2.5 cursor-pointer group">
                            <div class="relative">
                                <input id="remember-me" name="remember" type="checkbox"
                                       class="sr-only peer">
                                <div class="w-9 h-5 bg-white/10 border border-white/20 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all duration-200 cursor-pointer" onclick="document.getElementById('remember-me').click()"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-all duration-200 peer-checked:translate-x-4 pointer-events-none"></div>
                            </div>
                            <span class="text-sm text-gray-400 group-hover:text-gray-300 transition-colors">Ingat Saya</span>
                        </label>

                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-400 hover:text-indigo-300 font-medium transition-colors">
                            Lupa password?
                        </a>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" id="loginBtn"
                            class="w-full relative overflow-hidden bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500
                                   text-white font-bold py-3.5 px-6 rounded-xl text-sm
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:ring-offset-2 focus:ring-offset-transparent
                                   transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl hover:shadow-indigo-900/50
                                   group mt-2">
                        <span class="relative z-10 flex items-center justify-center space-x-2">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Masuk Sekarang</span>
                        </span>
                        {{-- Shimmer Effect --}}
                        <div class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/10 to-transparent skew-x-12"></div>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/10"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-transparent px-3 text-gray-500">Belum punya akun?</span>
                    </div>
                </div>

                {{-- Register Link --}}
                @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="w-full flex items-center justify-center space-x-2 py-3 px-6 rounded-xl border border-white/10 text-gray-300 text-sm font-medium
                          hover:bg-white/5 hover:border-white/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-user-plus text-indigo-400"></i>
                    <span>Daftar Akun Baru</span>
                </a>
                @endif
            </div>

            {{-- Back to Home --}}
            <div class="text-center mt-6">
                <a href="{{ url('/') }}" class="inline-flex items-center space-x-2 text-sm text-gray-600 hover:text-gray-400 transition-colors">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Kembali ke Halaman Utama</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Toggle Password Visibility
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.className = isPassword ? 'fas fa-eye-slash text-sm' : 'fas fa-eye text-sm';
        });
    }

    // Submit Loading State
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="relative z-10 flex items-center justify-center space-x-2"><i class="fas fa-circle-notch fa-spin"></i><span>Memproses...</span></span>';
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    });
</script>
@endsection