{{-- resources/views/layouts/partials/sidebar.blade.php (DARK MODE UPDATE) --}}

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Request;

    $user = Auth::user();
    $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray();
    $schoolName = $settings['school_name'] ?? 'E-Absensi Siswa';
    $schoolLogoPath = $settings['school_logo'] ?? null;
    $scanIconColor = 'text-red-400';

    $isAbsensiAdminActive = Request::is('admin/absensi/scan-kelas');
    $isManajemenDataActive =
        Request::is('admin/classes*') ||
        Request::is('admin/students*') ||
        Request::is('admin/teachers*') ||
        Request::is('admin/parents*') ||
        Request::is('admin/users*');
    $isAbsensiWaliKelasActive = Request::is('walikelas/absensi*');

    function isActive($path)
    {
        return Request::is($path);
    }

    // Active & Default classes - sidebar is always dark
    $activeClass = 'bg-indigo-600 text-white font-semibold shadow-lg shadow-indigo-900/50';
    $defaultClass = 'text-gray-300 hover:bg-white/10 hover:text-white';
    $activeSubClass = 'bg-indigo-700/60 text-white font-medium';
    $defaultSubClass = 'text-gray-400 hover:bg-white/10 hover:text-white';
@endphp

<aside id="main-sidebar"
    class="fixed top-0 left-0 z-50 h-full w-64 
           bg-gradient-to-b from-gray-900 via-gray-900 to-gray-950
           transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out 
           shadow-[4px_0_30px_rgba(0,0,0,0.3)] border-r border-gray-800/80">

    {{-- 1. BRAND LINK (Logo & Nama Sekolah) --}}
    <a href="{{ route('dashboard') }}" class="flex items-center p-4 py-3.5 border-b border-gray-800/80 bg-gray-950/30 hover:bg-gray-800/30 transition-colors">

        {{-- LOGO / ICON --}}
        @if ($schoolLogoPath)
            <img src="{{ asset('storage/' . $schoolLogoPath) }}" alt="{{ $schoolName }}"
                class="h-9 w-9 rounded-xl object-cover mr-3 border-2 border-indigo-500/50 shadow-lg">
        @else
            <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg shadow-indigo-900/50 flex-shrink-0">
                <i class="fas fa-clipboard-check text-white text-base"></i>
            </div>
        @endif

        {{-- TEXT (Nama Sekolah) --}}
        <div class="flex flex-col overflow-hidden leading-snug">
            <span class="text-white text-sm font-extrabold whitespace-nowrap overflow-ellipsis tracking-tight">
                {{ Str::limit($schoolName, 20) }}
            </span>
            <small class="text-indigo-400 text-xs font-medium mt-0.5">E-Absensi Digital</small>
        </div>
    </a>

    {{-- 2. NAVIGASI MENU UTAMA --}}
    <div class="overflow-y-auto h-[calc(100vh-62px)] p-3">
        <nav>
            <ul class="space-y-1">

                @if ($user)
                    {{-- AREA SUPER ADMIN --}}
                    @if ($user->isSuperAdmin())
                        {{-- Header Menu --}}
                        <li class="px-2 pt-4 pb-1.5 text-[10px] font-bold text-indigo-400/80 uppercase tracking-widest flex justify-between items-center">
                            <span>Administrasi Pusat</span>
                            <i class="fas fa-cogs text-indigo-500/50 text-xs"></i>
                        </li>

                        {{-- Dashboard --}}
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ isActive('admin/dashboard') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-tachometer-alt w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm">Dashboard</span>
                            </a>
                        </li>

                        {{-- DROPDOWN MANAJEMEN DATA --}}
                        <li class="relative">
                            <button
                                onclick="document.getElementById('submenu-manajemen-data').classList.toggle('hidden');"
                                class="w-full flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ $isManajemenDataActive ? $activeClass : $defaultClass }}">
                                <i class="fas fa-database w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="flex-1 text-sm whitespace-nowrap text-left">Manajemen Data</span>
                                <i class="fas fa-angle-left ml-auto transition-transform duration-200 text-xs {{ $isManajemenDataActive ? '-rotate-90' : 'rotate-0' }}"></i>
                            </button>

                            <ul id="submenu-manajemen-data"
                                class="pl-3 pt-1 space-y-0.5 {{ $isManajemenDataActive ? 'block' : 'hidden' }}">
                                @foreach ([
                    'classes.index' => ['route' => 'classes.index', 'text' => 'Data Kelas'],
                    'students.index' => ['route' => 'students.index', 'text' => 'Data Siswa'],
                    'teachers.index' => ['route' => 'teachers.index', 'text' => 'Data Wali Kelas'],
                    'parents.index' => ['route' => 'parents.index', 'text' => 'Data Orang Tua'],
                    'admin.users.index' => ['route' => 'admin.users.index', 'text' => 'Manajemen Pengguna'],
                ] as $routeKey => $item)
                                    <li>
                                        <a href="{{ route($routeKey) }}"
                                            class="flex items-center px-3 py-2 rounded-lg transition duration-150 text-sm 
                                                    {{ Request::is('admin/' . str_replace('.index', '*', Str::after($routeKey, 'admin.'))) ? $activeSubClass : $defaultSubClass }}">
                                            <i class="far fa-circle text-[8px] w-3 h-3 mr-3 opacity-60 flex-shrink-0"></i>
                                            <span>{{ $item['text'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                                {{-- MANAJEMEN PELAJARAN --}}
                                <li>
                                    <a href="{{ route('admin.subjects.index') }}"
                                        class="flex items-center px-3 py-2 rounded-lg transition duration-150 text-sm 
                                                {{ isActive('admin/subjects*') ? $activeSubClass : $defaultSubClass }}">
                                        <i class="fas fa-book-open text-[8px] w-3 h-3 mr-3 opacity-60 flex-shrink-0"></i>
                                        <span>Mata Pelajaran</span>
                                    </a>
                                </li>
                                {{-- MANAJEMEN JADWAL --}}
                                <li>
                                    <a href="{{ route('admin.schedules.index') }}"
                                        class="flex items-center px-3 py-2 rounded-lg transition duration-150 text-sm 
                                                {{ isActive('admin/schedules*') ? $activeSubClass : $defaultSubClass }}">
                                        <i class="far fa-calendar-alt text-[8px] w-3 h-3 mr-3 opacity-60 flex-shrink-0"></i>
                                        <span>Atur Jadwal</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- HEADER: LAPORAN & PENGATURAN --}}
                        <li class="px-2 pt-4 pb-1.5 text-[10px] font-bold text-indigo-400/80 uppercase tracking-widest border-t border-gray-800/80 mt-2 flex justify-between items-center">
                            <span>Laporan & Pengaturan</span>
                            <i class="fas fa-chart-line text-indigo-500/50 text-xs"></i>
                        </li>

                        {{-- Laporan Absensi & Pengaturan Umum --}}
                        @foreach ([
            'report.index' => ['icon' => 'fas fa-chart-line', 'text' => 'Laporan Absensi', 'route_path' => 'admin/report*'],
            'settings.index' => ['icon' => 'fas fa-cog', 'text' => 'Pengaturan Umum', 'route_path' => 'admin/settings*'],
        ] as $routeKey => $item)
                            <li>
                                <a href="{{ route($routeKey) }}"
                                    class="flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ isActive($item['route_path']) ? $activeClass : $defaultClass }}">
                                    <i class="{{ $item['icon'] }} w-5 h-5 mr-3 flex-shrink-0"></i>
                                    <span class="text-sm">{{ $item['text'] }}</span>
                                </a>
                            </li>
                        @endforeach

                        {{-- Kelola Pengumuman --}}
                        <li>
                            <a href="{{ route('announcements.index') }}"
                               class="flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ isActive('admin/announcements*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-bullhorn w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm">Kelola Pengumuman</span>
                            </a>
                        </li>

                        {{-- HEADER: OPERASI UTAMA (Absensi) --}}
                        <li class="px-2 pt-4 pb-1.5 text-[10px] font-bold text-indigo-400/80 uppercase tracking-widest border-t border-gray-800/80 mt-2 flex justify-between items-center">
                            <span>Operasi Utama</span>
                            <i class="fas fa-camera text-indigo-500/50 text-xs"></i>
                        </li>

                        {{-- Absensi QR Scan --}}
                        <li>
                            <a href="{{ route('admin.absensi.scan') }}"
                                class="flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ $isAbsensiAdminActive ? $activeClass : $defaultClass }}">
                                <i class="fas fa-qrcode w-5 h-5 mr-3 {{ $scanIconColor }} flex-shrink-0"></i>
                                <span class="text-sm">Absensi QR Scan</span>
                            </a>
                        </li>

                        {{-- AREA WALI KELAS --}}
                    @elseif($user->isWaliKelas())
                        <li class="px-2 pt-4 pb-1.5 text-[10px] font-bold text-indigo-400/80 uppercase tracking-widest flex justify-between items-center">
                            <span>Area Wali Kelas</span>
                            <i class="fas fa-user-tie text-indigo-500/50 text-xs"></i>
                        </li>
                        
                        {{-- Dashboard Kelas --}}
                        <li>
                            <a href="{{ route('walikelas.dashboard') }}"
                                class="flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ isActive('walikelas/dashboard') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-home w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm">Dashboard Kelas</span>
                            </a>
                        </li>
                        
                        {{-- Data Siswa Kelas yang Diampu --}}
                        <li>
                            <a href="{{ route('walikelas.students.index') }}"
                                class="flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ isActive('walikelas/students*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-users w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm">Data Siswa Kelas</span>
                            </a>
                        </li>
                        
                        {{-- PERMINTAAN IZIN --}}
                        <li>
                            <a href="{{ route('walikelas.izin.index') }}"
                                class="flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ isActive('walikelas/izin*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-envelope-open-text w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm">Permintaan Izin</span>
                            </a>
                        </li>
                        
                        {{-- Menu Absensi (Dropdown) --}}
                        <li class="relative">
                            <button
                                onclick="document.getElementById('submenu-walikelas-absensi').classList.toggle('hidden');"
                                class="w-full flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ $isAbsensiWaliKelasActive ? $activeClass : $defaultClass }}">
                                <i class="fas fa-calendar-check w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="flex-1 text-sm whitespace-nowrap text-left">Absensi Kelas</span>
                                <i class="fas fa-angle-left ml-auto transition-transform duration-200 text-xs {{ $isAbsensiWaliKelasActive ? '-rotate-90' : 'rotate-0' }}"></i>
                            </button>
                            <ul id="submenu-walikelas-absensi"
                                class="pl-3 pt-1 space-y-0.5 {{ $isAbsensiWaliKelasActive ? 'block' : 'hidden' }}">
                                <li>
                                    <a href="{{ route('walikelas.absensi.scan') }}"
                                        class="flex items-center px-3 py-2 rounded-lg transition duration-150 text-sm 
                                                {{ isActive('walikelas/absensi/scan') ? $activeSubClass : $defaultSubClass }}">
                                        <i class="fas fa-qrcode text-[8px] w-3 h-3 mr-3 opacity-60 flex-shrink-0"></i>
                                        <span>Scan Masuk/Pulang</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('walikelas.absensi.manual.index') }}"
                                        class="flex items-center px-3 py-2 rounded-lg transition duration-150 text-sm 
                                                {{ isActive('walikelas/absensi/manual*') ? $activeSubClass : $defaultSubClass }}">
                                        <i class="fas fa-edit text-[8px] w-3 h-3 mr-3 opacity-60 flex-shrink-0"></i>
                                        <span>Manual & Koreksi</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        {{-- Riwayat & Laporan (Dropdown) --}}
                        <li class="relative">
                            <button
                                onclick="document.getElementById('submenu-walikelas-report').classList.toggle('hidden');"
                                class="w-full flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ isActive('walikelas/report*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-chart-bar w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="flex-1 text-sm whitespace-nowrap text-left">Riwayat & Laporan</span>
                                <i class="fas fa-angle-left ml-auto transition-transform duration-200 text-xs {{ isActive('walikelas/report*') ? '-rotate-90' : 'rotate-0' }}"></i>
                            </button>
                            
                            <ul id="submenu-walikelas-report"
                                class="pl-3 pt-1 space-y-0.5 {{ isActive('walikelas/report*') ? 'block' : 'hidden' }}">
                                <li>
                                    <a href="{{ route('walikelas.report.index') }}"
                                        class="flex items-center px-3 py-2 rounded-lg transition duration-150 text-sm 
                                                {{ isActive('walikelas/report') && !isActive('walikelas/report/monthly-recap') ? $activeSubClass : $defaultSubClass }}">
                                        <i class="fas fa-list-alt text-[8px] w-3 h-3 mr-3 opacity-60 flex-shrink-0"></i>
                                        <span>Laporan Harian</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('walikelas.report.monthly_recap') }}"
                                        class="flex items-center px-3 py-2 rounded-lg transition duration-150 text-sm 
                                                {{ isActive('walikelas/report/monthly-recap') ? $activeSubClass : $defaultSubClass }}">
                                        <i class="fas fa-calendar-alt text-[8px] w-3 h-3 mr-3 opacity-60 flex-shrink-0"></i>
                                        <span>Rekap Absensi Bulanan</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- AREA ORANG TUA --}}
                    @elseif($user->isOrangTua())
                        <li class="px-2 pt-4 pb-1.5 text-[10px] font-bold text-indigo-400/80 uppercase tracking-widest flex justify-between items-center">
                            <span>Area Orang Tua</span>
                            <i class="fas fa-users text-indigo-500/50 text-xs"></i>
                        </li>

                        {{-- Dashboard --}}
                        <li>
                            <a href="{{ route('orangtua.dashboard') }}"
                                class="flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ isActive('orangtua/dashboard') && !isActive('orangtua/report*') && !isActive('orangtua/izin*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-tachometer-alt w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm">Dashboard</span>
                            </a>
                        </li>

                        {{-- Riwayat Absensi Anak (Tabel) --}}
                        <li>
                            <a href="{{ route('orangtua.report.index') }}"
                                class="flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ isActive('orangtua/report*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-list-alt w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm">Riwayat Absensi Anak</span>
                            </a>
                        </li>

                        {{-- Pengajuan Izin Online --}}
                        <li>
                            <a href="{{ route('orangtua.izin.index') }}"
                                class="flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ isActive('orangtua/izin*') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-file-medical-alt w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm">Pengajuan Izin/Sakit</span>
                            </a>
                        </li>

                        {{-- Jadwal Pelajaran --}}
                        <li>
                            <a href="{{ route('orangtua.jadwal.index') }}"
                                class="flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ isActive('orangtua/jadwal*') ? $activeClass : $defaultClass }}">
                                <i class="far fa-calendar-alt w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm">Jadwal Pelajaran</span>
                            </a>
                        </li>

                        {{-- Edit Profil --}}
                        <li class="border-t border-gray-800/80 pt-2 mt-2">
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center px-3 py-2.5 rounded-xl transition duration-150 {{ isActive('profile') ? $activeClass : $defaultClass }}">
                                <i class="fas fa-user w-5 h-5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm">Edit Profil</span>
                            </a>
                        </li>
                    @endif {{-- Tutup blok Role --}}

                @endif {{-- Tutup @if ($user) --}}
            </ul>
        </nav>
    </div>
</aside>
