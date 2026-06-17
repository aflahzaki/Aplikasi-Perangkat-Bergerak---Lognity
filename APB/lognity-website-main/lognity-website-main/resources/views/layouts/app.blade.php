<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
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
            [x-cloak] { display: none !important; }
        </style>

        <!-- SCRIPT PENTING: Mencegah Flash Putih saat refresh -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <!-- HAPUS x-data DARI SINI, biarkan class saja -->
    <body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-dark-bg dark:text-dark-text transition-colors duration-300">
        
        <!-- Background Blobs Animation -->
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-fun-purple/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob dark:bg-purple-900/20"></div>
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-fun-pink/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000 dark:bg-pink-900/20"></div>
            <div class="absolute -bottom-8 left-1/3 w-96 h-96 bg-fun-yellow/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000 dark:bg-yellow-900/20"></div>
        </div>

        <div class="min-h-screen flex flex-col">
            
            <livewire:layout.navigation />

            <!-- Admin Toolbar -->
            @if(auth()->check() && in_array(auth()->user()->role, ['Admin', 'Superadmin']))
                <div class="bg-gradient-to-r from-lognity-600 to-fun-purple dark:from-lognity-800 dark:to-purple-900 text-white shadow-md relative z-20 transition-colors duration-300">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between text-sm font-medium overflow-x-auto no-scrollbar">
                        <span class="bg-white/20 px-2 py-1 rounded-md text-xs uppercase tracking-wider mr-4 backdrop-blur-sm flex-shrink-0">
                            {{ auth()->user()->role }} Area
                        </span>
                        <div class="flex space-x-6">
                            <a href="{{ route('admin.users') }}" class="hover:text-fun-yellow transition {{ request()->routeIs('admin.users') ? 'text-fun-yellow border-b-2 border-fun-yellow' : '' }}">
                                {{ __('Manage Users') }}
                            </a>
                            <a href="{{ route('admin.reports') }}" class="hover:text-fun-yellow transition {{ request()->routeIs('admin.reports') ? 'text-fun-yellow border-b-2 border-fun-yellow' : '' }}">
                                {{ __('Laporan') }}
                            </a>
                            @if(auth()->user()->role === 'Superadmin')
                                <a href="{{ route('admin.logs') }}" class="hover:text-fun-yellow transition {{ request()->routeIs('admin.logs') ? 'text-fun-yellow border-b-2 border-fun-yellow' : '' }}">
                                    {{ __('Point Logs') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white/60 dark:bg-dark-card/60 backdrop-blur-lg shadow-sm border-b border-gray-100 dark:border-gray-700/50 sticky top-16 z-10 transition-colors duration-300">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                            {{ $header }}
                        </h2>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-grow container mx-auto px-4 sm:px-6 lg:px-8 py-8 transition-all duration-300">
                {{ $slot }}
            </main>

            <footer class="py-6 text-center text-sm text-gray-400 dark:text-gray-600">
                &copy; {{ date('Y') }} Lognity. Made with AresDev.
            </footer>
        </div>

        <livewire:social.chat-popup />
    </body>
</html>