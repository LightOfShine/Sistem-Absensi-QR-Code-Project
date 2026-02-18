<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        use Illuminate\Support\Facades\Storage;
        $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray(); 
        $schoolName = $settings['school_name'] ?? config('app.name', 'E-Absensi');
        $schoolLogoPath = $settings['school_logo'] ?? 'default/favicon.ico'; 
        $faviconUrl = asset('images/default/favicon.ico'); 
        if (!empty($schoolLogoPath) && $schoolLogoPath != 'default/favicon.ico' && Storage::disk('public')->exists($schoolLogoPath)) {
            $faviconUrl = asset('storage/' . $schoolLogoPath);
        }
    @endphp
    
    <title>@yield('title') - {{ $schoolName }}</title>

    <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $faviconUrl }}" type="image/x-icon">

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('template/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    
    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) 

    {{-- Custom CSS --}}
    @stack('css')

    <style>
        /* Auth page specific: prevent transition flash */
        html { background-color: #030712; }
        
        /* Custom input autofill style for dark backgrounds */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: #f1f5f9;
            -webkit-box-shadow: 0 0 0px 1000px rgba(255,255,255,0.05) inset;
            transition: background-color 5000s ease-in-out 0s;
            caret-color: #f1f5f9;
        }

        /* Smooth orb animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float 8s ease-in-out infinite 2s; }
    </style>

</head>
<body class="font-inter antialiased bg-gray-950 text-white">
    
    {{-- GLOBAL LOADER --}}
    @include('layouts.partials.loader')

    {{-- YIELD CONTENT DIRECTLY (Full Width Control) --}}
    @yield('content')
    
    {{-- REQUIRED SCRIPTS --}}
    <script src="{{ asset('template/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    {{-- Custom JavaScript --}}
    @yield('js')

</body>
</html>