{{-- resources/views/layouts/partials/header.blade.php (DARK MODE UPDATE) --}}

@php
    $user = Auth::user();
    $userName = $user->name ?? 'User';
    $userRole = Str::upper($user->role ?? 'USER');
@endphp

<header class="fixed top-0 left-0 md:left-64 right-0 z-40 
             bg-white/90 dark:bg-gray-900/95 backdrop-blur-md 
             border-b border-gray-200/50 dark:border-gray-700/50 shadow-sm 
             transition-all duration-300 ease-in-out">
    <nav class="flex items-center justify-between h-[4rem] px-4 sm:px-6 lg:px-8">
        
        {{-- Sisi Kiri: Toggle Sidebar (Mobile) & Navigasi Dasar --}}
        <div class="flex items-center space-x-4">
            
            {{-- Tombol Toggle Sidebar (HANYA terlihat di Mobile/Tablet) --}}
            <button id="sidebar-toggle-btn" 
                    class="p-2 text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 md:hidden focus:outline-none focus:ring-2 focus:ring-indigo-500/80 rounded-lg transition-colors" 
                    aria-label="Toggle Menu">
                <i class="fas fa-bars text-xl"></i>
            </button>

            {{-- Navigasi Dasar (Dashboard) --}}
            <a href="{{ route('dashboard') }}" 
               class="hidden sm:inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg transition duration-150 
               text-gray-600 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-800/70
               @if(Request::is('*/dashboard')) 
                   bg-indigo-100/50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 font-semibold shadow-sm
               @endif">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
            
            {{-- Waktu Server Langsung --}}
            <span class="hidden lg:inline-flex items-center text-sm font-mono text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-700 shadow-inner transition-colors">
                <i class="fas fa-clock mr-2 text-indigo-500 dark:text-indigo-400"></i>
                <span id="live-server-time">{{ \Carbon\Carbon::now()->format('H:i:s') }}</span> 
            </span>

        </div>

        {{-- Sisi Kanan: Dark Mode Toggle, Notifikasi & Profil --}}
        <div class="flex items-center space-x-2 sm:space-x-3">
            
            {{-- ☀️🌙 DARK MODE TOGGLE BUTTON --}}
            <button id="dark-mode-toggle"
                    class="relative p-2.5 rounded-xl text-gray-500 dark:text-gray-400 
                           hover:text-indigo-600 dark:hover:text-indigo-400 
                           hover:bg-indigo-50 dark:hover:bg-indigo-900/30
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/50
                           transition-all duration-300 group"
                    aria-label="Toggle Dark Mode"
                    title="Toggle Dark Mode">
                {{-- Sun Icon (shown in dark mode) --}}
                <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden text-amber-400 group-hover:rotate-45 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                {{-- Moon Icon (shown in light mode) --}}
                <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 group-hover:-rotate-12 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            {{-- Dropdown Notifikasi --}}
            <div class="relative" id="notification-dropdown">
                <button class="notification-dropdown-btn p-2 text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500/80 transition-colors" 
                        aria-expanded="false" 
                        title="Notifikasi">
                    <i class="far fa-bell text-xl"></i>
                    <span class="absolute top-1 right-1 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-900 shadow-md" 
                          id="notification-badge" 
                          style="display: none;">
                    </span>
                    <span class="sr-only" id="notification-count-text">0 notifikasi</span>
                </button>
                
                {{-- Dropdown Content --}}
                <div class="absolute right-0 mt-3 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl ring-1 ring-black/5 dark:ring-white/10 hidden z-50 transform origin-top-right"
                     id="notification-list">
                    
                    {{-- Header Notifikasi --}}
                    <div class="px-4 py-3 text-sm font-bold text-gray-700 dark:text-gray-200 border-b border-gray-100 dark:border-gray-700">
                        <i class="fas fa-bell text-indigo-500 mr-2"></i> <span id="dropdown-header-count">Memuat Notifikasi...</span>
                    </div>
                    
                    <div class="py-1 max-h-80 overflow-y-auto" id="dynamic-notifications">
                        {{-- KONTEN NOTIFIKASI DYNAMIC --}}
                    </div>
                    
                    {{-- Link Lihat Semua --}}
                    <a href="{{ route('report.index') }}" 
                       class="block w-full text-center px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 font-semibold hover:bg-indigo-50/50 dark:hover:bg-indigo-900/30 border-t border-gray-100 dark:border-gray-700 rounded-b-xl transition-colors">
                        Lihat Semua Laporan <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
            
            {{-- Dropdown Profil & Logout --}}
            <div class="relative">
                <button class="profile-dropdown-btn flex items-center p-2 text-gray-600 dark:text-gray-300 hover:text-indigo-800 dark:hover:text-indigo-300 rounded-lg transition duration-150 focus:outline-none" 
                        aria-expanded="false">
                    <i class="far fa-user w-5 h-5 mr-1 text-indigo-600 dark:text-indigo-400"></i>
                    <span class="hidden sm:inline text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $userName }}</span>
                    <i class="fas fa-chevron-down text-xs ml-2 opacity-70"></i>
                </button>
                
                {{-- Dropdown Content --}}
                <div class="absolute right-0 mt-3 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-2xl ring-1 ring-black/5 dark:ring-white/10 hidden z-50 transform origin-top-right" 
                     id="profile-dropdown-content">
                    
                    <div class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200 border-b border-gray-100 dark:border-gray-700 rounded-t-xl">
                        Login sebagai: <strong class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $userRole }}</strong>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150">
                        <i class="fas fa-user-edit mr-3 w-4 text-indigo-500 dark:text-indigo-400"></i> Kelola Profil
                    </a>
                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                    
                    <a href="#" class="flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 font-semibold hover:bg-red-50 dark:hover:bg-red-900/20 rounded-b-xl transition duration-150" 
                       onclick="event.preventDefault(); document.getElementById('logout-form-header-tailwind').submit();">
                        <i class="fas fa-sign-out-alt mr-3 w-4"></i> Logout
                    </a>
                    
                    <form id="logout-form-header-tailwind" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>

        </div>
    </nav>
</header>