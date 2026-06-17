<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LOGNITY') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Quicksand', sans-serif; }
        </style>

        <!-- CRITICAL: Inline Script agar Login/Register mengikuti tema -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50 dark:bg-dark-bg transition-colors duration-300">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            
            <!-- Background Animation -->
            <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-fun-purple/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob dark:bg-purple-900/20"></div>
                <div class="absolute top-0 right-1/4 w-96 h-96 bg-fun-pink/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000 dark:bg-pink-900/20"></div>
                <div class="absolute -bottom-8 left-1/3 w-96 h-96 bg-fun-yellow/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000 dark:bg-yellow-900/20"></div>
            </div>

            <!-- Logo Area -->
            <div class="mb-6 animate-bounce">
                <a href="/" wire:navigate class="flex items-center gap-2 group">
                    <div class="w-12 h-12 bg-gradient-to-tr from-lognity-500 to-fun-purple rounded-2xl flex items-center justify-center shadow-lg transform group-hover:rotate-12 transition duration-300">
                        <span class="text-white font-bold text-2xl">L</span>
                    </div>
                    <span class="text-3xl font-bold text-gray-800 dark:text-white tracking-tight">LOGNITY</span>
                </a>
            </div>

            <!-- Card Container -->
            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white/70 dark:bg-dark-card/70 backdrop-blur-xl shadow-2xl overflow-hidden sm:rounded-3xl border border-white/50 dark:border-gray-700/50 transform transition-all hover:scale-[1.01] duration-300">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} LOGNITY Community
            </div>
        </div>
    </body>
</html>