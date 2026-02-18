@extends('layouts.guest')

@section('title', 'Daftar Akun Baru')

@section('content')
<div class="min-h-screen flex bg-gray-950 text-white relative overflow-hidden">

    {{-- ═══════════════════════════════════════════════════════════
         ANIMATED BACKGROUND ELEMENTS
    ═══════════════════════════════════════════════════════════ --}}
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-purple-600 rounded-full opacity-20 blur-[120px] animate-pulse"></div>
        <div class="absolute top-1/3 -left-40 w-80 h-80 bg-indigo-600 rounded-full opacity-15 blur-[100px] animate-pulse" style="animation-delay: 1.5s;"></div>
        <div class="absolute -bottom-40 right-1/3 w-72 h-72 bg-blue-600 rounded-full opacity-10 blur-[100px] animate-pulse" style="animation-delay: 0.5s;"></div>
        <div class="absolute inset-0" style="background-image: linear-gradient(rgba(139,92,246,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(139,92,246,0.04) 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         LEFT PANEL: BRANDING (Hidden on Mobile)
    ═══════════════════════════════════════════════════════════ --}}
    <div class="hidden lg:flex lg:w-[45%] relative flex-col justify-between p-14 z-10">

        {{-- Top: Logo --}}
        <div class="flex items-center space-x-3">
            @if($globalSettings['logo_url'])
                <img src="{{ $globalSettings['logo_url'] }}" alt="Logo" class="h-11 w-11 rounded-2xl object-cover border border-white/10 shadow-xl">
            @else
                <div class="h-11 w-11 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-xl shadow-purple-900/50">
                    <i class="fas fa-user-graduate text-white text-lg"></i>
                </div>
            @endif
            <div>
                <span class="text-white font-bold text-base tracking-wide">{{ $globalSettings['school_name'] ?? 'E-Absensi' }}</span>
                <p class="text-purple-400 text-xs font-medium">Sistem Absensi Digital</p>
            </div>
        </div>

        {{-- Center: Hero Content --}}
        <div class="space-y-8">
            <div class="inline-flex items-center space-x-2 bg-purple-500/10 border border-purple-500/20 rounded-full px-4 py-1.5">
                <i class="fas fa-user-plus text-purple-400 text-xs"></i>
                <span class="text-purple-300 text-xs font-semibold tracking-wider uppercase">Bergabung Sekarang</span>
            </div>

            <div>
                <h1 class="text-5xl xl:text-6xl font-black text-white leading-[1.1] tracking-tight mb-5">
                    Mulai
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-indigo-400 to-blue-400">
                        Perjalanan Anda
                    </span>
                </h1>
                <p class="text-gray-400 text-lg leading-relaxed max-w-md">
                    Buat akun baru untuk mulai mengelola atau memantau aktivitas akademik secara digital. Cepat, mudah, dan aman.
                </p>
            </div>

            {{-- Steps --}}
            <div class="space-y-4">
                @foreach([
                    ['num' => '01', 'title' => 'Isi Formulir', 'desc' => 'Lengkapi data diri Anda dengan benar'],
                    ['num' => '02', 'title' => 'Pilih Peran', 'desc' => 'Guru, Wali Kelas, atau Orang Tua'],
                    ['num' => '03', 'title' => 'Akun Aktif', 'desc' => 'Mulai gunakan sistem setelah disetujui'],
                ] as $step)
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-9 h-9 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center">
                        <span class="text-xs font-black text-purple-400">{{ $step['num'] }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">{{ $step['title'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $step['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom --}}
        <div class="text-gray-600 text-sm">
            &copy; {{ date('Y') }} {{ $globalSettings['school_name'] ?? 'E-Absensi Siswa' }}. All rights reserved.
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         RIGHT PANEL: REGISTER FORM
    ═══════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col justify-center items-center px-6 py-10 z-10 relative overflow-y-auto">

        <div class="w-full max-w-md">

            {{-- Mobile Logo --}}
            <div class="lg:hidden text-center mb-8">
                @if($globalSettings['logo_url'])
                    <img src="{{ $globalSettings['logo_url'] }}" alt="Logo" class="h-14 w-14 rounded-2xl object-cover mx-auto mb-3 border border-white/10">
                @else
                    <div class="h-14 w-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-xl">
                        <i class="fas fa-user-graduate text-white text-2xl"></i>
                    </div>
                @endif
                <h2 class="text-xl font-bold text-white">{{ $globalSettings['school_name'] ?? 'E-Absensi Siswa' }}</h2>
            </div>

            {{-- Glassmorphism Card --}}
            <div class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-3xl p-8 shadow-2xl shadow-black/50">

                {{-- Header --}}
                <div class="mb-7">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl mb-4 shadow-lg shadow-purple-900/50">
                        <i class="fas fa-user-plus text-white text-lg"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">Buat Akun Baru</h2>
                    <p class="text-gray-400 text-sm mt-1">Lengkapi formulir di bawah ini dengan benar</p>
                </div>

                {{-- Error Alert --}}
                @if ($errors->any())
                    <div class="mb-6 flex items-start space-x-3 bg-red-500/10 border border-red-500/20 rounded-2xl p-4">
                        <i class="fas fa-exclamation-circle text-red-400 mt-0.5 flex-shrink-0"></i>
                        <div>
                            <p class="text-sm font-semibold text-red-300 mb-1">Mohon periksa inputan Anda:</p>
                            <ul class="text-xs text-red-400 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Form --}}
                <form action="{{ route('register') }}" method="POST" class="space-y-4" id="registerForm">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-500 text-sm"></i>
                            </div>
                            <input id="name" name="name" type="text" autocomplete="name" required autofocus
                                class="w-full bg-white/5 border @error('name') border-red-500/50 @else border-white/10 @enderror text-white placeholder-gray-500 rounded-xl pl-11 pr-4 py-3 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50
                                       hover:border-white/20 transition-all duration-200"
                                placeholder="Contoh: Budi Santoso" value="{{ old('name') }}">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-500 text-sm"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="w-full bg-white/5 border @error('email') border-red-500/50 @else border-white/10 @enderror text-white placeholder-gray-500 rounded-xl pl-11 pr-4 py-3 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50
                                       hover:border-white/20 transition-all duration-200"
                                placeholder="nama@email.com" value="{{ old('email') }}">
                        </div>
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-300 mb-2">Mendaftar Sebagai</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-id-badge text-gray-500 text-sm"></i>
                            </div>
                            <select id="role" name="role" required
                                class="w-full bg-white/5 border @error('role') border-red-500/50 @else border-white/10 @enderror text-gray-300 rounded-xl pl-11 pr-10 py-3 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50
                                       hover:border-white/20 transition-all duration-200 cursor-pointer appearance-none"
                                style="background-color: rgba(255,255,255,0.05);">
                                <option value="" disabled selected style="background:#1a1d27; color:#9ca3af;">Pilih Peran...</option>
                                <option value="wali_kelas" {{ old('role') == 'wali_kelas' ? 'selected' : '' }} style="background:#1a1d27; color:#f1f5f9;">Guru / Wali Kelas</option>
                                <option value="orang_tua" {{ old('role') == 'orang_tua' ? 'selected' : '' }} style="background:#1a1d27; color:#f1f5f9;">Orang Tua / Wali Murid</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Password Fields --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-500 text-sm"></i>
                                </div>
                                <input id="password" name="password" type="password" required autocomplete="new-password"
                                    class="w-full bg-white/5 border @error('password') border-red-500/50 @else border-white/10 @enderror text-white placeholder-gray-500 rounded-xl pl-11 pr-4 py-3 text-sm
                                           focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50
                                           hover:border-white/20 transition-all duration-200"
                                    placeholder="••••••••">
                            </div>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">Konfirmasi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-500 text-sm"></i>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                    class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 rounded-xl pl-11 pr-4 py-3 text-sm
                                           focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50
                                           hover:border-white/20 transition-all duration-200"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    {{-- Password Strength Indicator --}}
                    <div id="passwordStrength" class="hidden">
                        <div class="flex space-x-1 mb-1">
                            <div class="h-1 flex-1 rounded-full bg-white/10" id="str1"></div>
                            <div class="h-1 flex-1 rounded-full bg-white/10" id="str2"></div>
                            <div class="h-1 flex-1 rounded-full bg-white/10" id="str3"></div>
                            <div class="h-1 flex-1 rounded-full bg-white/10" id="str4"></div>
                        </div>
                        <p class="text-xs text-gray-500" id="strLabel">Kekuatan password</p>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" id="registerBtn"
                            class="w-full relative overflow-hidden bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500
                                   text-white font-bold py-3.5 px-6 rounded-xl text-sm
                                   focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:ring-offset-2 focus:ring-offset-transparent
                                   transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl hover:shadow-purple-900/50
                                   group mt-2">
                        <span class="relative z-10 flex items-center justify-center space-x-2">
                            <i class="fas fa-user-plus"></i>
                            <span>Daftar Sekarang</span>
                        </span>
                        <div class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/10 to-transparent skew-x-12"></div>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-5">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/10"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-transparent px-3 text-gray-500">Sudah punya akun?</span>
                    </div>
                </div>

                {{-- Login Link --}}
                <a href="{{ route('login') }}"
                   class="w-full flex items-center justify-center space-x-2 py-3 px-6 rounded-xl border border-white/10 text-gray-300 text-sm font-medium
                          hover:bg-white/5 hover:border-white/20 hover:text-white transition-all duration-200">
                    <i class="fas fa-sign-in-alt text-purple-400"></i>
                    <span>Masuk ke Akun</span>
                </a>
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
    // Password Strength Checker
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('passwordStrength');
    const bars = [document.getElementById('str1'), document.getElementById('str2'), document.getElementById('str3'), document.getElementById('str4')];
    const strLabel = document.getElementById('strLabel');

    const strengthColors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
    const strengthLabels = ['Sangat Lemah', 'Lemah', 'Cukup', 'Kuat'];

    passwordInput.addEventListener('input', function() {
        const val = this.value;
        if (val.length === 0) { strengthBar.classList.add('hidden'); return; }
        strengthBar.classList.remove('hidden');

        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        bars.forEach((bar, i) => {
            bar.className = 'h-1 flex-1 rounded-full transition-all duration-300 ' + (i < score ? strengthColors[score - 1] : 'bg-white/10');
        });
        strLabel.textContent = strengthLabels[score - 1] || 'Kekuatan password';
        strLabel.className = 'text-xs ' + (score <= 1 ? 'text-red-400' : score === 2 ? 'text-orange-400' : score === 3 ? 'text-yellow-400' : 'text-green-400');
    });

    // Submit Loading State
    document.getElementById('registerForm').addEventListener('submit', function() {
        const btn = document.getElementById('registerBtn');
        if (this.checkValidity()) {
            btn.disabled = true;
            btn.innerHTML = '<span class="relative z-10 flex items-center justify-center space-x-2"><i class="fas fa-circle-notch fa-spin"></i><span>Memproses...</span></span>';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        }
    });
</script>
@endsection