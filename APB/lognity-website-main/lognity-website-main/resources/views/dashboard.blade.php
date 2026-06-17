<x-app-layout>
    <x-slot name="header">
        <div class="hidden">Dashboard</div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- 1. HERO SECTION: GREETING & QUOTA -->
            <div class="relative bg-gradient-to-r from-lognity-600 to-fun-purple rounded-3xl p-8 sm:p-10 shadow-xl overflow-hidden text-white">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-40 h-40 bg-fun-yellow opacity-20 rounded-full blur-2xl"></div>

                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold mb-2">Halo, {{ Auth::user()->username }}! 👋</h2>
                        <p class="text-lognity-100 text-lg">Siap untuk berkontribusi dan belajar hari ini?</p>
                        
                        <div class="mt-6 flex flex-wrap gap-3">
                            <span class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-xl text-sm font-semibold flex items-center gap-2 border border-white/10">
                                👑 {{ Auth::user()->role }}
                            </span>
                            <span class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-xl text-sm font-semibold flex items-center gap-2 border border-white/10">
                                ⭐ Level {{ Auth::user()->current_level }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Daily Quota Tracker -->
                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/20 w-full md:w-auto min-w-[280px]">
                        <h3 class="font-bold text-sm uppercase tracking-wider mb-3 text-lognity-50">Kuota Harian</h3>
                        @php
                            $user = Auth::user();
                            $limits = \App\Models\User::LIMITS[$user->current_level];
                            $reqUsed = $user->requests()->whereDate('created_at', now())->count();
                            $intUsed = $user->interactions()->whereDate('created_at', now())->count();
                        @endphp
                        
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span>Request</span>
                                    <span>{{ $reqUsed }} / {{ $limits['request'] >= 9999 ? '∞' : $limits['request'] }}</span>
                                </div>
                                <div class="w-full bg-black/20 rounded-full h-2">
                                    <div class="bg-fun-yellow h-2 rounded-full transition-all" style="width: {{ $limits['request'] >= 9999 ? '100' : ($reqUsed / $limits['request'] * 100) }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span>Interaksi</span>
                                    <span>{{ $intUsed }} / {{ $limits['interaction'] >= 9999 ? '∞' : $limits['interaction'] }}</span>
                                </div>
                                <div class="w-full bg-black/20 rounded-full h-2">
                                    <div class="bg-fun-pink h-2 rounded-full transition-all" style="width: {{ $limits['interaction'] >= 9999 ? '100' : ($intUsed / $limits['interaction'] * 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. MAIN GRID ATAS (3 KOLOM: XP, STATUS, FORUM) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- CARD 1: XP -->
                <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition group relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition transform group-hover:scale-110">
                        <svg class="w-24 h-24 text-lognity-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.699-3.189a1 1 0 111.774.945l-1.699 3.189 3.189 1.699a1 1 0 11-.945 1.774l-3.189-1.699L13.323 11H14.65a1 1 0 110 2h-1.327l-1.582 3.954 3.189 1.699a1 1 0 11-.945 1.774l-3.189-1.699-1.699 3.189a1 1 0 11-1.774-.945l1.699-3.189L6.677 14.65V16a1 1 0 11-2 0v-1.346l-3.954-1.582-3.189 1.699a1 1 0 11-.945-1.774l3.189-1.699L1.35 9.673a1 1 0 01.945-1.774l3.189 1.699 1.582-3.954-3.189-1.699a1 1 0 11.945-1.774l3.189 1.699L9 3.323V2a1 1 0 011-1z"/></svg>
                    </div>
                    <div class="relative z-10">
                        <div class="text-sm font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider mb-2">Total XP</div>
                        <div class="text-4xl font-extrabold text-gray-800 dark:text-white flex items-baseline gap-1">
                            {{ number_format(Auth::user()->points) }}
                            <span class="text-sm text-lognity-500 dark:text-lognity-400">Pts</span>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>Progress Level</span>
                                @php $progress = min((Auth::user()->points / 8000) * 100, 100); @endphp
                                <span>{{ round($progress) }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-lognity-400 to-lognity-600 h-3 rounded-full animate-pulse" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: STATUS -->
                <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition">
                    <div class="text-sm font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider mb-2">Status Akun</div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-fun-yellow/20 text-yellow-600 dark:text-fun-yellow flex items-center justify-center text-xl">
                            🛡
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 dark:text-white text-lg">{{ Auth::user()->role }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-300">Role Aktif</div>
                        </div>
                    </div>
                    <div class="text-sm bg-gray-50 dark:bg-gray-800 p-3 rounded-xl border border-gray-100 dark:border-gray-700 dark:text-gray-200">
                        @if(Auth::user()->points < 500)
                            <span class="font-bold text-red-500">Starter:</span> Batasan 2x Upload
                        @elseif(Auth::user()->points < 2500)
                            <span class="font-bold text-green-500">Member:</span> Voting Aktif
                        @else
                            <span class="font-bold text-fun-purple">Elite:</span> Moderator Komunitas
                        @endif
                    </div>
                </div>

                <!-- CARD 3: FORUM -->
                <div class="bg-gradient-to-br from-fun-pink to-red-400 rounded-3xl p-6 shadow-lg text-white flex flex-col justify-between hover:scale-[1.02] transition-transform cursor-pointer" onclick="window.location='{{ route('forum.index') }}'">
                    <div>
                        <div class="bg-white/20 w-fit p-2 rounded-xl backdrop-blur-sm mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.772-1.168m-4-2c.288 0 .563-.095.793-.258 0 0 .966-.697 1.104-.847.142-.15.21-.4.21-.63V11a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold">Forum Diskusi</h3>
                        <p class="text-white/80 text-sm mt-1">Gabung percakapan terbaru.</p>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-sm font-bold">
                        <span>Buka Forum</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </div>
                </div>

            </div>

            <!-- 3. GRID BAWAH (LIBRARY, LEADERBOARD, SEDANG HANGAT) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- CARD E-LIBRARY -->
                <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-3xl p-8 shadow-lg text-white flex flex-col justify-between hover:scale-[1.02] hover:shadow-xl transition-all cursor-pointer border border-green-400/30" onclick="window.location='{{ route('library.index') }}'">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="bg-white/20 w-fit p-3 rounded-2xl backdrop-blur-sm mb-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <h3 class="text-3xl font-bold">E-Library</h3>
                            <p class="text-white/90 mt-2 text-sm leading-relaxed">Akses ribuan buku dan referensi belajar resmi dari kampus.</p>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center gap-2 text-sm font-bold bg-white/20 hover:bg-white/30 transition-colors w-fit px-5 py-2.5 rounded-full backdrop-blur-md">
                        <span>Jelajahi Perpustakaan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </div>
                </div>

                <!-- CARD LEADERBOARD -->
                <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-3xl p-8 shadow-lg text-white flex flex-col justify-between hover:scale-[1.02] hover:shadow-xl transition-all cursor-pointer border border-yellow-300/30" onclick="window.location='{{ route('leaderboard') }}'">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="bg-white/20 w-fit p-3 rounded-2xl backdrop-blur-sm mb-4 shadow-inner">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <h3 class="text-3xl font-bold">Leaderboard</h3>
                            <p class="text-white/90 mt-2 text-sm leading-relaxed">Lihat peringkat mahasiswa teraktif dan kejar poin XP tertinggi.</p>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center gap-2 text-sm font-bold bg-white/20 hover:bg-white/30 transition-colors w-fit px-5 py-2.5 rounded-full backdrop-blur-md">
                        <span>Lihat Peringkat</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </div>
                </div>

                <!-- SEDANG HANGAT (FEED) -->
                <div class="bg-white dark:bg-dark-card rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-center items-center text-center hover:shadow-lg transition-all relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-50 to-transparent dark:from-gray-800/50 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-16 h-16 bg-gradient-to-tr from-orange-100 to-orange-200 dark:from-orange-900/50 dark:to-orange-800/50 rounded-2xl flex items-center justify-center shrink-0 mb-4 shadow-inner">
                            <span class="text-3xl">🔥</span>
                        </div>
                        <h3 class="font-bold text-xl text-gray-800 dark:text-white">Topik Sedang Hangat</h3>
                        <p class="text-gray-500 dark:text-gray-300 mt-3 max-w-xs text-sm leading-relaxed">
                            Banyak mahasiswa sedang berdiskusi. Jangan sampai ketinggalan info kampus!
                        </p>
                        <a href="{{ route('forum.index') }}" class="mt-6 px-6 py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white text-sm font-bold rounded-full hover:bg-lognity-600 dark:hover:bg-gray-200 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            Lihat Diskusi &rarr;
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>