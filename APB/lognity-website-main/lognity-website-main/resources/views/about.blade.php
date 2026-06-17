<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tentang LOGNITY - Logic & Unity</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Quicksand', sans-serif; }
        </style>

        <!-- Script Anti-FOUC (Flash of Unstyled Content) untuk Dark Mode -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-dark-bg dark:text-gray-100 transition-colors duration-300">
        
        <!-- Background Blobs (Estetik) -->
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-fun-purple/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob dark:bg-purple-900/20"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-fun-pink/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000 dark:bg-pink-900/20"></div>
        </div>

        <!-- Navbar Sederhana -->
        <nav class="w-full py-6 px-4 sm:px-8 flex justify-between items-center backdrop-blur-sm sticky top-0 z-50">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-tr from-lognity-500 to-fun-purple rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-xl">L</span>
                </div>
                <span class="font-bold text-2xl tracking-tight text-gray-800 dark:text-white">LOGNITY</span>
            </div>
            <a href="/" class="text-sm font-bold text-gray-500 hover:text-lognity-600 dark:text-gray-400 dark:hover:text-white transition">
                &larr; Kembali ke Beranda
            </a>
        </nav>

        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-24">
            
            <!-- SECTION 1: HERO (Intro) -->
            <div class="text-center space-y-6">
                <span class="inline-block py-1 px-3 rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300 text-sm font-bold uppercase tracking-wider mb-2 animate-bounce">
                    🚀 Welcome to the Future of Learning
                </span>
                <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 dark:text-white leading-tight">
                    Belajar Itu Harus <span class="text-transparent bg-clip-text bg-gradient-to-r from-lognity-500 to-fun-pink">Happy</span> & <span class="text-transparent bg-clip-text bg-gradient-to-r from-fun-purple to-fun-yellow">Seru!</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    LOGNITY menggabungkan <strong>Logic</strong> dan <strong>Community</strong>. Kami percaya bahwa berbagi ilmu tidak harus membosankan. Di sini, setiap kontribusimu dihargai.
                </p>
            </div>

            <!-- SECTION 2: FITUR UTAMA (Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1: Forum -->
                <div class="bg-white dark:bg-dark-card p-8 rounded-[30px] shadow-xl border border-gray-100 dark:border-gray-700 hover:-translate-y-2 transition duration-300 group">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition">
                        💬
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800 dark:text-white">Forum Diskusi Interaktif</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                        Bingung dengan tugas kuliah? Buat <strong>Request</strong> di forum. Teman-teman lain akan membantumu menjawab. Jawaban terbaik akan mendapatkan reward!
                    </p>
                </div>

                <!-- Card 2: Material -->
                <div class="bg-white dark:bg-dark-card p-8 rounded-[30px] shadow-xl border border-gray-100 dark:border-gray-700 hover:-translate-y-2 transition duration-300 group">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition">
                        📚
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800 dark:text-white">Perpustakaan Materi</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                        Akses ribuan catatan, rangkuman, dan soal latihan. Kamu juga bisa <strong>Upload Materi</strong> milikmu untuk membantu orang lain.
                    </p>
                </div>

                <!-- Card 3: Gamifikasi -->
                <div class="bg-white dark:bg-dark-card p-8 rounded-[30px] shadow-xl border border-gray-100 dark:border-gray-700 hover:-translate-y-2 transition duration-300 group">
                    <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition">
                        🏆
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800 dark:text-white">Sistem Level & Poin</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                        Setiap upload dan jawaban yang membantu akan memberimu <strong>XP (Poin)</strong>. Naikkan levelmu dari <em>Newbie</em> hingga menjadi <em>Legend</em>!
                    </p>
                </div>
            </div>

            <!-- SECTION 3: HOW IT WORKS (Gamification Detail) -->
            <div class="bg-gradient-to-r from-lognity-600 to-fun-purple rounded-[40px] p-8 md:p-16 text-white text-center relative overflow-hidden shadow-2xl">
                <!-- Decor -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl -mr-20 -mt-20"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-fun-yellow opacity-20 rounded-full blur-3xl -ml-20 -mb-20"></div>

                <div class="relative z-10">
                    <h2 class="text-3xl md:text-4xl font-extrabold mb-6">Mulai Petualanganmu!</h2>
                    
                    <div class="flex flex-col md:flex-row justify-center items-center gap-8 mb-10">
                        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 w-64">
                            <div class="text-4xl font-bold mb-1">+20 XP</div>
                            <div class="text-sm opacity-90">Setiap Upload Materi</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 w-64">
                            <div class="text-4xl font-bold mb-1">+50 XP</div>
                            <div class="text-sm opacity-90">Jawaban Terbaik (Accepted)</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 w-64">
                            <div class="text-4xl font-bold mb-1">Badges</div>
                            <div class="text-sm opacity-90">Koleksi Lencana Unik</div>
                        </div>
                    </div>

                    <a href="{{ route('register') }}" class="inline-block bg-white text-lognity-600 px-8 py-4 rounded-full font-extrabold text-lg shadow-lg hover:bg-gray-100 hover:scale-105 transition transform duration-200">
                        Gabung Komunitas Sekarang
                    </a>
                    <p class="mt-4 text-sm opacity-70">Gratis selamanya untuk mahasiswa.</p>
                </div>
            </div>

            <!-- Footer Simple -->
            <div class="text-center text-gray-400 text-sm pb-10">
                &copy; {{ date('Y') }} LOGNITY. Dibuat dengan 💙 dan Kopi.
            </div>

        </main>
    </body>
</html>