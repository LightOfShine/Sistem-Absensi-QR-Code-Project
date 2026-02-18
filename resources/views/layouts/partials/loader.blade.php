<div id="global-loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white dark:bg-gray-950 transition-opacity duration-500 ease-in-out">
    <div class="flex flex-col items-center">
        {{-- Spinner Modern --}}
        <div class="relative w-16 h-16">
            <div class="absolute inset-0 rounded-full border-4 border-gray-200 dark:border-gray-700"></div>
            <div class="absolute inset-0 rounded-full border-4 border-indigo-600 dark:border-indigo-400 border-t-transparent animate-spin"></div>
        </div>
        
        {{-- Loading Text --}}
        <h3 class="mt-4 text-lg font-semibold text-gray-700 dark:text-gray-200 animate-pulse">
            Memuat @yield('title', 'Halaman')...
        </h3>
        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">E-Absensi Siswa</p>
    </div>
</div>

<style>
    /* Mencegah scroll saat loading */
    body.loading {
        overflow: hidden !important;
    }
</style>

<script>
    (function() {
        const loader = document.getElementById('global-loader');
        if (loader) {
            document.body.classList.add('loading');

            const dismissLoader = () => {
                if (!loader) return;
                
                loader.classList.add('opacity-0', 'pointer-events-none');
                document.body.classList.remove('loading');
                
                setTimeout(() => {
                    loader.remove();
                }, 500);
            };

            window.addEventListener('load', dismissLoader);
            setTimeout(dismissLoader, 3000);
        }
    })();
</script>
