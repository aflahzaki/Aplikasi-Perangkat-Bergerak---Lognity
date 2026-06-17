<div class="py-10" x-data>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-4 text-center font-bold">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- SIDEBAR: USER INFO -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white dark:bg-dark-card rounded-3xl p-8 shadow-lg border border-gray-100 dark:border-gray-700 sticky top-24 text-center overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-24 bg-lognity-50 dark:bg-gray-800"></div>
                    
                    <div class="relative z-10">
                        <!-- Foto -->
                        <div class="relative w-32 h-32 flex-shrink-0 z-10 mx-auto mb-4">
                            <img src="{{ $user->profil_url }}" class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-md">
                            
                            <!-- Level Badge -->
                            <div class="absolute -bottom-2 -right-2 bg-lognity-600 text-white text-xs font-bold px-3 py-1 rounded-full border-4 border-white dark:border-gray-800">
                                {{ $user->current_level }}
                            </div>
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->username }}</h2>
                        
                        <!-- Role Label -->
                        <div class="mt-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                                {{ $user->role === 'Admin' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $user->role }}
                            </span>
                        </div>

                        <!-- Social Buttons -->
                        @if(Auth::check() && Auth::id() !== $user->user_id)
                            <div class="mt-4 flex justify-center gap-2">
                                <button wire:click="toggleFollow" class="px-4 py-2 text-sm font-bold rounded-full transition-colors {{ $isFollowing ? 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' : 'bg-lognity-500 text-white hover:bg-lognity-600' }}">
                                    {{ $isFollowing ? 'Mengikuti' : 'Ikuti' }}
                                </button>
                                <button @click="$dispatch('open-chat', { userId: {{ $user->user_id }} })" class="px-4 py-2 text-sm font-bold border border-gray-200 dark:border-gray-700 rounded-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                    Pesan
                                </button>
                            </div>
                        @endif

                        <!-- Statistik Mini -->
                        <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-700 mt-6 pt-6 border-t border-gray-100 dark:border-gray-700 w-full text-center">
                            <div>
                                <span class="block font-bold text-xl text-gray-800 dark:text-white">{{ $stats['requests'] }}</span>
                                <span class="text-[10px] uppercase text-gray-400 font-bold tracking-wider">Req</span>
                            </div>
                            <div>
                                <span class="block font-bold text-xl text-green-500">{{ $stats['materials'] }}</span>
                                <span class="text-[10px] uppercase text-gray-400 font-bold tracking-wider">Upld</span>
                            </div>
                            <div>
                                <span class="block font-bold text-xl text-fun-purple">{{ $stats['answers'] }}</span>
                                <span class="text-[10px] uppercase text-gray-400 font-bold tracking-wider">Jwb</span>
                            </div>
                        </div>

                        <!-- TOMBOL LAPOR AKUN (Subtle) -->
                        @if(Auth::id() !== $user->user_id)
                            <button wire:click="openReportModal" class="mt-8 text-xs text-gray-400 hover:text-red-500 flex items-center justify-center gap-1 w-full transition-colors duration-300">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span>Laporkan Pengguna</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT: TABS -->
            <div class="lg:col-span-8">
                
                <!-- Nav Tabs -->
                <div class="flex flex-wrap gap-2 mb-6 p-1 bg-white dark:bg-dark-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 w-fit">
                    @foreach(['overview' => '📊 Statistik', 'requests' => '📝 Request', 'interactions' => '💬 Interaksi'] as $key => $label)
                        <button wire:click="setTab('{{ $key }}')" class="px-5 py-2 rounded-xl text-sm font-bold transition-all {{ $activeTab === $key ? 'bg-gray-900 text-white dark:bg-white dark:text-black shadow-md' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <!-- TAB 1: OVERVIEW -->
                @if($activeTab === 'overview')
                    <div class="space-y-6 animate-fade-in-up">
                        <!-- Level -->
                        <div class="bg-white dark:bg-dark-card p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="flex justify-between mb-2">
                                <h3 class="font-bold text-gray-700 dark:text-gray-200">Level Progress</h3>
                                <span class="font-bold text-lognity-600">{{ number_format($user->points) }} XP</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-lognity-400 to-lognity-600 h-3 rounded-full" style="width: {{ $user->level_progress }}%"></div>
                            </div>
                        </div>

                        <!-- Badges Grid -->
                        <div class="bg-white dark:bg-dark-card p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-6">Pencapaian Badge</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($badges as $badge)
                                    <div class="border dark:border-gray-700 rounded-2xl p-3 flex flex-col items-center text-center {{ $badge['unlocked'] ? 'bg-white dark:bg-gray-800 border-lognity-100' : 'bg-gray-50 dark:bg-gray-900 opacity-50 grayscale' }}">
                                        <div class="text-2xl mb-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-full">{{ $badge['icon'] }}</div>
                                        <div class="font-bold text-xs mb-1 text-gray-800 dark:text-gray-200">{{ $badge['name'] }}</div>
                                        
                                        <!-- Progress Bar Mini -->
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1 mt-auto">
                                            <div class="h-1 rounded-full {{ $badge['unlocked'] ? 'bg-green-500' : 'bg-lognity-400' }}" style="width: {{ $badge['percent'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- TAB 2 & 3 (Requests & Interactions) -->
                <!-- Struktur sama seperti Private Profile tapi read-only -->
                @if($activeTab === 'requests')
                    <div class="space-y-4 animate-fade-in-up">
                        @forelse($dataRequests as $req)
                            <div class="bg-white dark:bg-dark-card p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                                <div class="text-xs text-gray-400 mb-1">{{ $req->created_at->format('d M Y') }}</div>
                                <a href="{{ route('forum.show', $req->request_id) }}" class="font-bold text-lg text-lognity-600 hover:underline block mb-2">
                                    {{ Str::limit($req->description, 100) }}
                                </a>
                                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs text-gray-600 dark:text-gray-300 font-semibold">{{ $req->category }}</span>
                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-400 italic">Belum ada request.</div>
                        @endforelse
                        <div class="mt-4">{{ $dataRequests->links() }}</div>
                    </div>
                @endif

                @if($activeTab === 'interactions')
                    <div class="space-y-4 animate-fade-in-up">
                        @forelse($dataInteractions as $int)
                            <div class="bg-white dark:bg-dark-card p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                                <div class="text-xs text-gray-400 mb-2">
                                    @if($int->is_accepted_answer) <span class="text-green-600 font-bold mr-1">✓ Jawaban Terbaik</span> • @endif
                                    {{ $int->created_at->diffForHumans() }}
                                </div>
                                <p class="text-sm text-gray-800 dark:text-gray-200 italic mb-2">"{{ Str::limit($int->content, 80) }}"</p>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Pada: <a href="{{ route('forum.show', $int->request_id) }}" class="text-lognity-600 hover:underline">{{ Str::limit($int->request->description ?? 'Konten dihapus', 50) }}</a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-400 italic">Belum ada interaksi.</div>
                        @endforelse
                        <div class="mt-4">{{ $dataInteractions->links() }}</div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- MODAL REPORT AKUN -->
    @if($showReportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <!-- Backdrop Blur -->
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeReportModal"></div>
            
            <!-- Modal Content -->
            <div class="bg-white dark:bg-dark-card rounded-3xl shadow-2xl p-6 w-full max-w-md relative z-10 animate-blob">
                <div class="flex items-center gap-3 mb-4 text-red-600">
                    <div class="bg-red-100 p-2 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>
                    <h3 class="text-lg font-bold">Laporkan {{ $user->username }}</h3>
                </div>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Bantu kami menjaga komunitas tetap aman. Mengapa Anda melaporkan akun ini?
                </p>
                
                <textarea wire:model="reportReason" class="w-full border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-xl mb-2 focus:ring-red-500 focus:border-red-500 text-sm p-3" rows="4" placeholder="Contoh: Mengirim spam, berkata kasar..."></textarea>
                @error('reportReason') <span class="text-red-500 text-xs font-bold block mb-2">{{ $message }}</span> @enderror

                <div class="flex justify-end gap-3 mt-4">
                    <button wire:click="closeReportModal" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-200 transition">Batal</button>
                    <button wire:click="submitReport" class="px-5 py-2.5 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 shadow-lg shadow-red-200 dark:shadow-none transition transform active:scale-95">Kirim Laporan</button>
                </div>
            </div>
        </div>
    @endif
</div>