<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem absensi siswa digital berbasis QR Code dengan notifikasi real-time untuk sekolah modern.">

    @php
        use Illuminate\Support\Facades\Storage;
        $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray(); 
        $schoolName = $settings['school_name'] ?? 'E-Absensi Siswa';
        $schoolLogoPath = $settings['school_logo'] ?? null;
        $defaultLogo = asset('images/default_logo.png'); 
        $finalLogo = ($schoolLogoPath && Storage::disk('public')->exists($schoolLogoPath)) ? asset('storage/' . $schoolLogoPath) : $defaultLogo;
    @endphp
    
    <title>{{ $schoolName }} — Sistem Absensi Digital</title>
    <link rel="icon" type="image/png" href="{{ $finalLogo }}">

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root { --font: 'Plus Jakarta Sans', sans-serif; }
        body { font-family: var(--font); background-color: #030712; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 99px; }

        /* ── Animated gradient text ── */
        .gradient-text {
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 50%, #60a5fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% auto;
            animation: gradientShift 4s ease infinite;
        }
        @keyframes gradientShift {
            0% { background-position: 0% center; }
            50% { background-position: 100% center; }
            100% { background-position: 0% center; }
        }

        /* ── Navbar scroll effect ── */
        .nav-scrolled {
            background: rgba(3, 7, 18, 0.95) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        /* ── Glow effects ── */
        .glow-indigo { box-shadow: 0 0 40px rgba(99,102,241,0.3); }
        .glow-purple { box-shadow: 0 0 40px rgba(168,85,247,0.3); }

        /* ── Card hover ── */
        .feature-card:hover { transform: translateY(-8px); }
        .feature-card { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }

        /* ── Floating animation ── */
        @keyframes floatY {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }
        .float-anim { animation: floatY 4s ease-in-out infinite; }
        .float-anim-slow { animation: floatY 6s ease-in-out infinite 1s; }

        /* ── Orb pulse ── */
        @keyframes orbPulse {
            0%, 100% { opacity: 0.15; transform: scale(1); }
            50% { opacity: 0.25; transform: scale(1.1); }
        }
        .orb { animation: orbPulse 6s ease-in-out infinite; }
        .orb-2 { animation: orbPulse 8s ease-in-out infinite 2s; }
        .orb-3 { animation: orbPulse 10s ease-in-out infinite 4s; }

        /* ── Number counter ── */
        .stat-number { font-variant-numeric: tabular-nums; }

        /* ── Step connector ── */
        .step-line::after {
            content: '';
            position: absolute;
            top: 20px;
            left: calc(50% + 28px);
            width: calc(100% - 56px);
            height: 1px;
            background: linear-gradient(90deg, rgba(99,102,241,0.5), rgba(168,85,247,0.5));
        }

        /* ── Shimmer button ── */
        .btn-shimmer {
            position: relative;
            overflow: hidden;
        }
        .btn-shimmer::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: skewX(-15deg);
            transition: left 0.6s ease;
        }
        .btn-shimmer:hover::after { left: 100%; }
    </style>
</head>

<body class="antialiased text-white overflow-x-hidden" x-data="{ scrolled: false, mobileOpen: false }" @scroll.window="scrolled = (window.pageYOffset > 40)">

    {{-- GLOBAL LOADER --}}
    @include('layouts.partials.loader')

    {{-- ════════════════════════════════════════════════════════
         BACKGROUND ORBS (Fixed)
    ════════════════════════════════════════════════════════ --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="orb absolute -top-60 -left-60 w-[600px] h-[600px] bg-indigo-600 rounded-full blur-[150px]"></div>
        <div class="orb-2 absolute top-1/3 -right-60 w-[500px] h-[500px] bg-purple-600 rounded-full blur-[150px]"></div>
        <div class="orb-3 absolute -bottom-60 left-1/3 w-[400px] h-[400px] bg-blue-600 rounded-full blur-[150px]"></div>
        {{-- Grid --}}
        <div class="absolute inset-0" style="background-image: linear-gradient(rgba(99,102,241,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(99,102,241,0.04) 1px, transparent 1px); background-size: 60px 60px;"></div>
    </div>

    {{-- ════════════════════════════════════════════════════════
         NAVBAR
    ════════════════════════════════════════════════════════ --}}
    <nav :class="scrolled ? 'nav-scrolled' : 'bg-transparent py-2'"
         class="fixed top-0 w-full z-50 transition-all duration-500 py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">

                {{-- Logo --}}
                <a href="#" class="flex items-center space-x-3 group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-indigo-500 rounded-xl blur-md opacity-0 group-hover:opacity-50 transition-opacity"></div>
                        <div class="relative h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            @if($finalLogo && $schoolLogoPath)
                                <img src="{{ $finalLogo }}" alt="Logo" class="h-7 w-7 object-contain rounded-lg">
                            @else
                                <i class="fas fa-clipboard-check text-white text-lg"></i>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="block text-white font-bold text-base leading-tight tracking-tight">{{ $schoolName }}</span>
                        <span class="text-indigo-400 text-[10px] font-semibold uppercase tracking-widest">Absensi Digital</span>
                    </div>
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center space-x-1">
                    <a href="#features" class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">Fitur</a>
                    <a href="#how" class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">Cara Kerja</a>
                    <a href="#stats" class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">Statistik</a>

                    <div class="h-5 w-px bg-white/10 mx-3"></div>

                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="btn-shimmer px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-900/50 transition-all duration-300 hover:-translate-y-0.5">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="btn-shimmer px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-900/50 transition-all duration-300 hover:-translate-y-0.5">
                            <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                        </a>
                    @endauth
                </div>

                {{-- Mobile Toggle --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2.5 text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl transition-all">
                    <i :class="mobileOpen ? 'fa-times' : 'fa-bars'" class="fas text-lg w-5 h-5 flex items-center justify-center"></i>
                </button>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                 @click.away="mobileOpen = false"
                 class="md:hidden mt-3 bg-gray-900/95 backdrop-blur-xl border border-white/10 rounded-2xl p-3 space-y-1"
                 style="display: none;">
                <a href="#features" @click="mobileOpen=false" class="block px-4 py-2.5 text-sm font-medium text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition">Fitur</a>
                <a href="#how" @click="mobileOpen=false" class="block px-4 py-2.5 text-sm font-medium text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition">Cara Kerja</a>
                <a href="#stats" @click="mobileOpen=false" class="block px-4 py-2.5 text-sm font-medium text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition">Statistik</a>
                <div class="border-t border-white/10 my-1 pt-1">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm font-bold text-indigo-400 hover:bg-indigo-500/10 rounded-xl transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block px-4 py-2.5 text-sm font-bold text-indigo-400 hover:bg-indigo-500/10 rounded-xl transition">Masuk ke Sistem</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ════════════════════════════════════════════════════════
         HERO SECTION
    ════════════════════════════════════════════════════════ --}}
    <section class="relative min-h-screen flex items-center pt-24 pb-20 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid lg:grid-cols-2 gap-16 items-center">

                {{-- Left: Text Content --}}
                <div class="text-center lg:text-left">
                    {{-- Badge --}}
                    <div class="inline-flex items-center space-x-2 bg-indigo-500/10 border border-indigo-500/20 rounded-full px-4 py-2 mb-8">
                        <span class="h-2 w-2 bg-green-400 rounded-full animate-pulse"></span>
                        <span class="text-indigo-300 text-xs font-bold uppercase tracking-widest">Sistem Aktif & Real-Time</span>
                    </div>

                    <h1 class="text-5xl lg:text-6xl xl:text-7xl font-black text-white leading-[1.05] tracking-tight mb-6">
                        Absensi Sekolah
                        <span class="block gradient-text">Lebih Cerdas</span>
                    </h1>

                    <p class="text-gray-400 text-lg lg:text-xl leading-relaxed mb-10 max-w-xl mx-auto lg:mx-0">
                        {{ $settings['site_description'] ?? 'Platform digital terintegrasi untuk memantau kehadiran siswa dengan QR Code, laporan real-time, dan notifikasi otomatis ke orang tua.' }}
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 mb-12">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="btn-shimmer w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-8 py-4 text-base font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-2xl shadow-indigo-900/50 hover:from-indigo-500 hover:to-purple-500 transition-all duration-300 hover:-translate-y-1">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Buka Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="btn-shimmer w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-8 py-4 text-base font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-2xl shadow-indigo-900/50 hover:from-indigo-500 hover:to-purple-500 transition-all duration-300 hover:-translate-y-1">
                                <i class="fas fa-qrcode"></i>
                                <span>Mulai Sekarang</span>
                            </a>
                            <a href="#features"
                               class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-8 py-4 text-base font-semibold text-gray-300 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 hover:text-white hover:border-white/20 transition-all duration-300">
                                <span>Pelajari Fitur</span>
                                <i class="fas fa-arrow-down text-sm"></i>
                            </a>
                        @endauth
                    </div>

                    {{-- Trust Badges --}}
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-6">
                        @foreach([
                            ['icon' => 'fa-shield-alt', 'text' => 'Data Aman'],
                            ['icon' => 'fa-bolt', 'text' => 'Real-Time'],
                            ['icon' => 'fa-mobile-alt', 'text' => 'Mobile Friendly'],
                        ] as $badge)
                        <div class="flex items-center space-x-2 text-gray-500">
                            <i class="fas {{ $badge['icon'] }} text-indigo-500 text-sm"></i>
                            <span class="text-sm font-medium">{{ $badge['text'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Visual Card --}}
                <div class="relative flex justify-center lg:justify-end">
                    {{-- Main Dashboard Card --}}
                    <div class="float-anim relative w-full max-w-md">
                        {{-- Glow --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-purple-600/20 rounded-3xl blur-2xl scale-110"></div>

                        <div class="relative bg-gray-900/80 backdrop-blur-xl border border-white/10 rounded-3xl p-6 shadow-2xl">
                            {{-- Card Header --}}
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Live Absensi</p>
                                    <p class="text-white font-bold text-lg">Hari Ini</p>
                                </div>
                                <div class="flex items-center space-x-1.5 bg-green-500/10 border border-green-500/20 rounded-full px-3 py-1.5">
                                    <span class="h-1.5 w-1.5 bg-green-400 rounded-full animate-pulse"></span>
                                    <span class="text-green-400 text-xs font-bold">LIVE</span>
                                </div>
                            </div>

                            {{-- Stats Row --}}
                            <div class="grid grid-cols-3 gap-3 mb-6">
                                @foreach([
                                    ['label' => 'Hadir', 'value' => '142', 'color' => 'emerald'],
                                    ['label' => 'Terlambat', 'value' => '8', 'color' => 'amber'],
                                    ['label' => 'Alpa', 'value' => '3', 'color' => 'red'],
                                ] as $s)
                                <div class="bg-{{ $s['color'] }}-500/10 border border-{{ $s['color'] }}-500/20 rounded-2xl p-3 text-center">
                                    <p class="text-xl font-black text-{{ $s['color'] }}-400">{{ $s['value'] }}</p>
                                    <p class="text-{{ $s['color'] }}-500/70 text-[10px] font-semibold mt-0.5">{{ $s['label'] }}</p>
                                </div>
                                @endforeach
                            </div>

                            {{-- Recent Activity --}}
                            <div class="space-y-3">
                                @foreach([
                                    ['name' => 'Ahmad Fauzi', 'class' => 'X IPA 1', 'status' => 'Hadir', 'time' => '07:12', 'color' => 'emerald'],
                                    ['name' => 'Siti Rahayu', 'class' => 'XI IPS 2', 'status' => 'Hadir', 'time' => '07:15', 'color' => 'emerald'],
                                    ['name' => 'Budi Santoso', 'class' => 'XII IPA 3', 'status' => 'Terlambat', 'time' => '07:48', 'color' => 'amber'],
                                ] as $item)
                                <div class="flex items-center justify-between bg-white/3 rounded-xl px-4 py-2.5 border border-white/5">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-8 w-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                            {{ substr($item['name'], 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-white text-xs font-semibold">{{ $item['name'] }}</p>
                                            <p class="text-gray-500 text-[10px]">{{ $item['class'] }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-{{ $item['color'] }}-400 text-[10px] font-bold bg-{{ $item['color'] }}-500/10 px-2 py-0.5 rounded-full">{{ $item['status'] }}</span>
                                        <p class="text-gray-600 text-[10px] mt-0.5 font-mono">{{ $item['time'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            {{-- QR Scan Button --}}
                            <div class="mt-5 bg-gradient-to-r from-indigo-600/20 to-purple-600/20 border border-indigo-500/20 rounded-2xl p-4 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 bg-indigo-600/30 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-qrcode text-indigo-400 text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-white text-xs font-bold">Scan QR Code</p>
                                        <p class="text-gray-500 text-[10px]">Kamera siap digunakan</p>
                                    </div>
                                </div>
                                <div class="h-2.5 w-2.5 bg-green-400 rounded-full animate-pulse"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Floating Notification Card --}}
                    <div class="float-anim-slow absolute -bottom-6 -left-6 bg-gray-900/90 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-xl max-w-[200px]">
                        <div class="flex items-start space-x-3">
                            <div class="h-8 w-8 bg-green-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fab fa-whatsapp text-green-400 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-white text-xs font-bold">Notif Terkirim</p>
                                <p class="text-gray-500 text-[10px] mt-0.5">Orang tua Ahmad diberitahu</p>
                                <p class="text-gray-600 text-[10px] mt-1 font-mono">07:12 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════
         STATS SECTION
    ════════════════════════════════════════════════════════ --}}
    <section id="stats" class="relative z-10 py-16 border-y border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach([
                    ['value' => '99.9%', 'label' => 'Uptime Sistem', 'icon' => 'fa-server', 'color' => 'indigo'],
                    ['value' => '<2 Dtk', 'label' => 'Kecepatan Scan', 'icon' => 'fa-bolt', 'color' => 'amber'],
                    ['value' => 'Real-Time', 'label' => 'Sinkronisasi Data', 'icon' => 'fa-sync-alt', 'color' => 'emerald'],
                    ['value' => '100%', 'label' => 'Data Terenkripsi', 'icon' => 'fa-shield-alt', 'color' => 'purple'],
                ] as $stat)
                <div class="text-center group">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-{{ $stat['color'] }}-500/10 border border-{{ $stat['color'] }}-500/20 rounded-2xl mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }}-400 text-lg"></i>
                    </div>
                    <p class="stat-number text-3xl lg:text-4xl font-black text-white mb-1">{{ $stat['value'] }}</p>
                    <p class="text-gray-500 text-sm font-medium">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════
         FEATURES SECTION
    ════════════════════════════════════════════════════════ --}}
    <section id="features" class="relative z-10 py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Section Header --}}
            <div class="text-center max-w-2xl mx-auto mb-16">
                <div class="inline-flex items-center space-x-2 bg-indigo-500/10 border border-indigo-500/20 rounded-full px-4 py-2 mb-6">
                    <i class="fas fa-star text-indigo-400 text-xs"></i>
                    <span class="text-indigo-300 text-xs font-bold uppercase tracking-widest">Fitur Unggulan</span>
                </div>
                <h2 class="text-4xl lg:text-5xl font-black text-white mb-4 tracking-tight">
                    Semua yang Anda
                    <span class="gradient-text"> Butuhkan</span>
                </h2>
                <p class="text-gray-400 text-lg leading-relaxed">
                    Dirancang khusus untuk kebutuhan sekolah modern dengan teknologi terkini.
                </p>
            </div>

            {{-- Feature Cards --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach([
                    [
                        'icon' => 'fa-qrcode',
                        'color' => 'indigo',
                        'title' => 'Scan QR Code Instan',
                        'desc' => 'Absensi siswa dalam hitungan detik. Anti-titip absen dengan teknologi QR unik per siswa yang diperbarui setiap hari.',
                        'badge' => 'Paling Populer'
                    ],
                    [
                        'icon' => 'fa-whatsapp fab',
                        'color' => 'emerald',
                        'title' => 'Notifikasi WhatsApp',
                        'desc' => 'Kirim pesan otomatis ke orang tua saat siswa masuk atau pulang. Transparan, real-time, dan terpantau.',
                        'badge' => null
                    ],
                    [
                        'icon' => 'fa-chart-line',
                        'color' => 'purple',
                        'title' => 'Laporan & Analitik',
                        'desc' => 'Rekap kehadiran harian, bulanan, dan semester. Export PDF siap cetak untuk keperluan administrasi sekolah.',
                        'badge' => null
                    ],
                    [
                        'icon' => 'fa-user-shield',
                        'color' => 'amber',
                        'title' => 'Multi-Role Akses',
                        'desc' => 'Sistem akses bertingkat untuk Super Admin, Wali Kelas, dan Orang Tua. Setiap peran melihat data yang relevan.',
                        'badge' => null
                    ],
                    [
                        'icon' => 'fa-file-medical-alt',
                        'color' => 'rose',
                        'title' => 'Pengajuan Izin Online',
                        'desc' => 'Orang tua dapat mengajukan izin atau sakit secara digital. Wali kelas menyetujui langsung dari dashboard.',
                        'badge' => null
                    ],
                    [
                        'icon' => 'fa-calendar-check',
                        'color' => 'cyan',
                        'title' => 'Jadwal Pelajaran',
                        'desc' => 'Kelola jadwal pelajaran per kelas dan per guru. Terintegrasi langsung dengan sistem absensi harian.',
                        'badge' => null
                    ],
                ] as $feature)
                <div class="feature-card relative group bg-gray-900/50 backdrop-blur-sm border border-white/8 rounded-3xl p-7 hover:border-{{ $feature['color'] }}-500/30 hover:bg-gray-900/80 cursor-default">
                    {{-- Glow on hover --}}
                    <div class="absolute inset-0 rounded-3xl bg-{{ $feature['color'] }}-500/0 group-hover:bg-{{ $feature['color'] }}-500/5 transition-all duration-500"></div>

                    {{-- Badge --}}
                    @if($feature['badge'])
                    <div class="absolute top-5 right-5 bg-indigo-500/20 border border-indigo-500/30 rounded-full px-3 py-1">
                        <span class="text-indigo-300 text-[10px] font-bold uppercase tracking-wider">{{ $feature['badge'] }}</span>
                    </div>
                    @endif

                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-{{ $feature['color'] }}-500/10 border border-{{ $feature['color'] }}-500/20 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
                            <i class="{{ str_contains($feature['icon'], 'fab') ? $feature['icon'] : 'fas ' . $feature['icon'] }} text-{{ $feature['color'] }}-400 text-2xl"></i>
                        </div>
                        <h3 class="text-white font-bold text-lg mb-3">{{ $feature['title'] }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════
         HOW IT WORKS
    ════════════════════════════════════════════════════════ --}}
    <section id="how" class="relative z-10 py-24 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center max-w-2xl mx-auto mb-16">
                <div class="inline-flex items-center space-x-2 bg-purple-500/10 border border-purple-500/20 rounded-full px-4 py-2 mb-6">
                    <i class="fas fa-route text-purple-400 text-xs"></i>
                    <span class="text-purple-300 text-xs font-bold uppercase tracking-widest">Cara Kerja</span>
                </div>
                <h2 class="text-4xl lg:text-5xl font-black text-white mb-4 tracking-tight">
                    Mudah &
                    <span class="gradient-text"> Cepat</span>
                </h2>
                <p class="text-gray-400 text-lg">Hanya 3 langkah untuk mencatat kehadiran siswa secara digital.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                {{-- Connector line (desktop) --}}
                <div class="hidden md:block absolute top-10 left-1/3 right-1/3 h-px bg-gradient-to-r from-indigo-500/50 via-purple-500/50 to-indigo-500/50"></div>

                @foreach([
                    ['num' => '01', 'icon' => 'fa-laptop', 'color' => 'indigo', 'title' => 'Buka Menu Scan', 'desc' => 'Admin atau Wali Kelas membuka halaman scan QR Code di browser. Kamera langsung aktif otomatis.'],
                    ['num' => '02', 'icon' => 'fa-id-card', 'color' => 'purple', 'title' => 'Siswa Scan Kartu', 'desc' => 'Siswa mengarahkan kartu QR ke kamera laptop atau PC. Sistem mengenali identitas dalam <2 detik.'],
                    ['num' => '03', 'icon' => 'fa-check-double', 'color' => 'emerald', 'title' => 'Data Tersimpan', 'desc' => 'Absensi tercatat otomatis. Notifikasi WhatsApp terkirim ke orang tua. Laporan langsung terupdate.'],
                ] as $step)
                <div class="relative text-center group">
                    {{-- Step Number --}}
                    <div class="relative inline-flex items-center justify-center w-20 h-20 mb-6">
                        <div class="absolute inset-0 bg-{{ $step['color'] }}-500/10 border-2 border-{{ $step['color'] }}-500/30 rounded-full group-hover:scale-110 transition-transform duration-300"></div>
                        <div class="absolute inset-0 bg-{{ $step['color'] }}-500/5 rounded-full blur-xl group-hover:bg-{{ $step['color'] }}-500/15 transition-all duration-300"></div>
                        <i class="fas {{ $step['icon'] }} text-{{ $step['color'] }}-400 text-2xl relative z-10"></i>
                        <div class="absolute -top-2 -right-2 w-7 h-7 bg-{{ $step['color'] }}-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-[10px] font-black">{{ $step['num'] }}</span>
                        </div>
                    </div>

                    <h3 class="text-white font-bold text-xl mb-3">{{ $step['title'] }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-xs mx-auto">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════
         CTA SECTION
    ════════════════════════════════════════════════════════ --}}
    <section class="relative z-10 py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="relative bg-gradient-to-br from-indigo-900/50 to-purple-900/50 backdrop-blur-xl border border-indigo-500/20 rounded-3xl p-12 lg:p-16 overflow-hidden">
                {{-- Decorative --}}
                <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-indigo-600 rounded-full blur-[100px] opacity-20"></div>

                <div class="relative z-10">
                    <div class="inline-flex items-center space-x-2 bg-white/10 border border-white/20 rounded-full px-4 py-2 mb-8">
                        <i class="fas fa-rocket text-amber-400 text-xs"></i>
                        <span class="text-white text-xs font-bold uppercase tracking-widest">Mulai Sekarang</span>
                    </div>

                    <h2 class="text-4xl lg:text-5xl font-black text-white mb-6 tracking-tight leading-tight">
                        Siap Modernisasi<br>
                        <span class="gradient-text">Sistem Absensi Anda?</span>
                    </h2>

                    <p class="text-gray-300 text-lg mb-10 max-w-xl mx-auto leading-relaxed">
                        Bergabunglah dan rasakan kemudahan mengelola kehadiran siswa secara digital, akurat, dan efisien.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="btn-shimmer w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-10 py-4 text-base font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-2xl shadow-indigo-900/50 hover:from-indigo-500 hover:to-purple-500 transition-all duration-300 hover:-translate-y-1">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Buka Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="btn-shimmer w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-10 py-4 text-base font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-2xl shadow-indigo-900/50 hover:from-indigo-500 hover:to-purple-500 transition-all duration-300 hover:-translate-y-1">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Masuk ke Sistem</span>
                            </a>
                            <a href="{{ route('register') }}"
                               class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-10 py-4 text-base font-semibold text-white bg-white/10 border border-white/20 rounded-2xl hover:bg-white/15 transition-all duration-300">
                                <i class="fas fa-user-plus"></i>
                                <span>Daftar Gratis</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════
         FOOTER
    ════════════════════════════════════════════════════════ --}}
    <footer class="relative z-10 border-t border-white/5 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-10 mb-10">

                {{-- Brand --}}
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            @if($finalLogo && $schoolLogoPath)
                                <img src="{{ $finalLogo }}" alt="Logo" class="h-7 w-7 object-contain rounded-lg">
                            @else
                                <i class="fas fa-clipboard-check text-white"></i>
                            @endif
                        </div>
                        <div>
                            <span class="text-white font-bold text-sm">{{ $schoolName }}</span>
                            <p class="text-gray-600 text-xs">Sistem Absensi Digital</p>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Platform absensi digital modern untuk sekolah yang lebih efisien dan transparan.
                    </p>
                </div>

                {{-- Links --}}
                <div>
                    <h5 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Navigasi</h5>
                    <ul class="space-y-2.5">
                        <li><a href="#features" class="text-gray-500 hover:text-indigo-400 text-sm transition-colors flex items-center space-x-2"><i class="fas fa-chevron-right text-xs text-indigo-600"></i><span>Fitur Unggulan</span></a></li>
                        <li><a href="#how" class="text-gray-500 hover:text-indigo-400 text-sm transition-colors flex items-center space-x-2"><i class="fas fa-chevron-right text-xs text-indigo-600"></i><span>Cara Kerja</span></a></li>
                        <li><a href="{{ route('login') }}" class="text-gray-500 hover:text-indigo-400 text-sm transition-colors flex items-center space-x-2"><i class="fas fa-chevron-right text-xs text-indigo-600"></i><span>Login Admin</span></a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h5 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Kontak</h5>
                    <ul class="space-y-3">
                        @if(!empty($settings['school_email']))
                            <li class="flex items-center space-x-3 text-gray-500 text-sm">
                                <div class="w-7 h-7 bg-indigo-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope text-indigo-400 text-xs"></i>
                                </div>
                                <span>{{ $settings['school_email'] }}</span>
                            </li>
                        @endif
                        @if(!empty($settings['school_phone']))
                            <li class="flex items-center space-x-3 text-gray-500 text-sm">
                                <div class="w-7 h-7 bg-emerald-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-phone text-emerald-400 text-xs"></i>
                                </div>
                                <span>{{ $settings['school_phone'] }}</span>
                            </li>
                        @endif
                        @if(!empty($settings['school_address']))
                            <li class="flex items-start space-x-3 text-gray-500 text-sm">
                                <div class="w-7 h-7 bg-rose-500/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fas fa-map-marker-alt text-rose-400 text-xs"></i>
                                </div>
                                <span>{{ $settings['school_address'] }}</span>
                            </li>
                        @endif

                        {{-- Social --}}
                        <li class="flex items-center space-x-3 pt-2">
                            @if(!empty($settings['social_facebook']))
                                <a href="{{ $settings['social_facebook'] }}" class="w-9 h-9 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-gray-400 hover:text-blue-400 hover:border-blue-500/30 transition-all">
                                    <i class="fab fa-facebook text-sm"></i>
                                </a>
                            @endif
                            @if(!empty($settings['social_instagram']))
                                <a href="{{ $settings['social_instagram'] }}" class="w-9 h-9 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-gray-400 hover:text-pink-400 hover:border-pink-500/30 transition-all">
                                    <i class="fab fa-instagram text-sm"></i>
                                </a>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-white/5 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-gray-600 text-sm">&copy; {{ date('Y') }} {{ $schoolName }}. All rights reserved.</p>
                <p class="text-gray-700 text-xs">Powered by <span class="text-indigo-500 font-semibold">E-Absensi</span> × Ryan Gabriel</p>
            </div>
        </div>
    </footer>

    {{-- Alpine.js already loaded via app.js --}}
</body>
</html>