<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lognity - Learn & Grow</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Force light mode by default unless user already picked dark
        if (localStorage.theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        }
    </script>
</head>
<body class="antialiased font-sans bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 selection:bg-indigo-500 selection:text-white transition-colors duration-300">

    <div class="relative min-h-screen flex flex-col justify-center items-center overflow-hidden">
        
        <!-- Background Blobs (Estetik & Fun) -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-[-10%] right-[-10%] w-96 h-96 bg-yellow-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        </div>

        <!-- Navbar Sederhana -->
        <nav class="absolute top-0 w-full p-6 flex justify-between items-center z-50">
            <div class="text-2xl font-black tracking-tighter text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                <span class="text-4xl">🚀</span> LOGNITY
            </div>
            <div class="space-x-2 sm:space-x-4 flex items-center">
                <!-- Theme Toggle Button -->
                <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none rounded-full p-2.5 transition">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                </button>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full font-bold text-gray-600 dark:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/50 transition">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 hover:scale-105 transition transform hidden sm:inline-block">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="text-center px-6 max-w-4xl mx-auto z-10">
            <div class="mb-8 inline-block animate-bounce">
                <span class="px-4 py-2 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-sm font-semibold text-indigo-500">
                    ✨ Komunitas Belajar Paling Seru
                </span>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight">
                Belajar Jadi Lebih <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">
                    Happy & Interaktif
                </span>
            </h1>

            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-10 max-w-2xl mx-auto leading-relaxed">
                Gabung bersama teman-teman di <strong>LOGNITY</strong>. Diskusi materi, upload tugas, kumpulkan poin, dan naikkan levelmu sekarang juga! 
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-bold text-lg hover:-translate-y-1 shadow-xl hover:shadow-2xl transition-all duration-300 flex items-center justify-center gap-2">
                    Mulai Petualangan ⚡
                </a>
                <a href="{{ route('about') }}" class="w-full sm:w-auto px-8 py-4 bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-2xl font-bold text-lg text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-gray-800 transition-all duration-300">
                    Pelajari Dulu
                </a>
            </div>
        </main>
    </div>
    
    <!-- CSS Tambahan untuk Animasi Blob (Opsional, bisa ditaruh di app.css) -->
    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>

    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            // toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('theme')) {
                if (localStorage.getItem('theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            // if NOT set via local storage previously
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            }
        });
    </script>
</body>
</html>