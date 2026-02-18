{{-- resources/views/layouts/partials/footer.blade.php (DARK MODE UPDATE) --}}

@php
    $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray();
    $schoolName = $settings['school_name'] ?? 'E-Absensi Sekolah';
@endphp

<footer class="mt-8 pt-4 pb-20 md:pb-6 
                bg-gray-100/50 dark:bg-gray-900/50 
                border-t border-gray-200 dark:border-gray-700/50 
                text-sm text-gray-600 dark:text-gray-400 
                px-4 sm:px-6 
                transition-colors duration-300">

    <div class="flex flex-col sm:flex-row justify-between items-start w-full max-w-7xl mx-auto">
        
        {{-- Sisi Kanan: Versi Aplikasi --}}
        <div class="order-1 sm:order-2 mb-2 sm:mb-0 text-xs text-right w-full sm:w-auto">
             <span class="text-gray-400 dark:text-gray-500">Versi: </span>
             <strong class="font-medium text-gray-500 dark:text-gray-400">v1.1.0</strong>
        </div>
        
        {{-- Sisi Kiri: Copyright Info --}}
        <div class="order-2 sm:order-1 text-left w-full sm:w-auto">
            <strong class="font-semibold text-gray-700 dark:text-gray-300">
                Copyright &copy; {{ date('Y') }} 
                <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-bold transition-colors">
                    {{ $schoolName }}
                </a>.
            </strong> 
            <span class="block sm:inline text-xs text-gray-500 dark:text-gray-500 font-light mt-1 sm:mt-0">
                All rights reserved. Dibuat dengan <i class="fas fa-heart text-red-500"></i>
            </span>
        </div>
    </div>
</footer>