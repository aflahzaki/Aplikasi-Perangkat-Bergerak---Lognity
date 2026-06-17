<div class="py-10">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Notifikasi -->
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" class="bg-green-100 dark:bg-green-900/30 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded-2xl relative mb-6 flex items-center gap-3 shadow-sm" role="alert">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="block sm:inline">{{ session('message') }}</span>
                <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- KOLOM KIRI (PROFILE CARD) -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white dark:bg-dark-card rounded-3xl p-8 shadow-lg border border-gray-100 dark:border-gray-700 sticky top-24 text-center relative overflow-hidden">
                    <!-- Decor background -->
                    <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-lognity-100 to-transparent dark:from-lognity-900/20"></div>

                    <div class="relative z-10">
                        <!-- Foto Profil -->
                        <div class="relative inline-block group mb-4">
                            <div class="absolute -inset-1 bg-gradient-to-tr from-lognity-400 to-fun-pink rounded-full blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                            @if($photo)
                                <img src="{{ $photo->temporaryUrl() }}" class="relative w-36 h-36 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-xl">
                            @else
                                <img src="{{ auth()->user()->profil_url }}" class="relative w-36 h-36 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-xl">
                            @endif
                            
                            <!-- Level Badge -->
                            <div class="absolute bottom-1 right-1 bg-gray-900 text-white text-xs font-bold px-3 py-1 rounded-full border-2 border-white dark:border-gray-800 shadow-lg">
                                Lvl. {{ $user->current_level }}
                            </div>
                        </div>

                        <h2 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">{{ $user->username }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $user->email }}</p>

                        <!-- Stats Row -->
                        <div class="flex justify-center gap-4 mt-6 border-t border-b border-gray-100 dark:border-gray-700 py-4">
                            <div class="text-center px-2">
                                <span class="block font-extrabold text-xl text-gray-800 dark:text-white">{{ $totalRequests }}</span>
                                <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Request</span>
                            </div>
                            <div class="text-center px-2 border-l border-r border-gray-100 dark:border-gray-700">
                                <span class="block font-extrabold text-xl text-green-500">{{ $totalMaterials }}</span>
                                <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Upload</span>
                            </div>
                            <div class="text-center px-2">
                                <span class="block font-extrabold text-xl text-fun-purple">{{ $totalAnswers }}</span>
                                <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Jawab</span>
                            </div>
                        </div>

                        <!-- Form Edit -->
                        <div class="mt-6 text-left">
                            <form wire:submit.prevent="updateProfile" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Username</label>
                                    <input wire:model="username" type="text" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 focus:ring-lognity-500 focus:border-lognity-500 text-sm py-2.5 px-4 transition">
                                    @error('username') <span class="text-fun-pink text-xs font-bold ml-1">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Email</label>
                                    <input wire:model="email" type="email" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 focus:ring-lognity-500 focus:border-lognity-500 text-sm py-2.5 px-4 transition">
                                    @error('email') <span class="text-fun-pink text-xs font-bold ml-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Ganti Foto</label>
                                    <input wire:model="photo" type="file" class="block w-full text-xs text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-xs file:font-semibold
                                      file:bg-lognity-50 file:text-lognity-700
                                      hover:file:bg-lognity-100
                                      dark:file:bg-gray-700 dark:file:text-white
                                    "/>
                                    @error('photo') <span class="text-fun-pink text-xs font-bold ml-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="pt-2 flex flex-col gap-3">
                                    <button type="submit" class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-3 rounded-xl hover:bg-lognity-600 dark:hover:bg-gray-200 transition text-sm font-bold shadow-md hover:shadow-lg transform active:scale-95">
                                        Simpan Perubahan
                                    </button>
                                    @if ($currentPhoto && !$photo)
                                        <button type="button" wire:click="deleteProfilePhoto" wire:confirm="Yakin ingin menghapus foto?" class="w-full bg-red-50 text-red-600 py-3 rounded-xl hover:bg-red-100 transition text-sm font-bold">
                                            Hapus Foto
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN (TABS CONTENT) -->
            <div class="lg:col-span-8">
                
                <!-- NAVIGATION PILLS -->
                <div class="flex flex-wrap gap-2 mb-6 p-1.5 bg-white dark:bg-dark-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 w-fit">
                    @foreach(['overview' => '📊 Overview', 'badges' => '🏆 Badges', 'requests' => '📝 Requests', 'interactions' => '💬 Interaksi'] as $key => $label)
                        <button wire:click="setTab('{{ $key }}')" 
                            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 {{ $activeTab === $key ? 'bg-lognity-500 text-white shadow-md transform scale-105' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <!-- CONTENT AREA -->
                <div class="transition-all duration-500 ease-in-out">
                    
                    <!-- TAB 1: OVERVIEW -->
                    @if($activeTab === 'overview')
                        <div class="space-y-6 animate-fade-in-up">
                            <!-- Level Progress -->
                            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl p-8 text-white shadow-lg relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -mr-16 -mt-16"></div>
                                <div class="relative z-10">
                                    <div class="flex justify-between items-end mb-3">
                                        <div>
                                            <h3 class="font-bold text-xl">Level Progress</h3>
                                            <p class="text-white/80 text-sm">Rank Saat Ini: <span class="font-extrabold text-yellow-300 uppercase">{{ $user->current_level }}</span></p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-4xl font-extrabold">{{ number_format($user->points) }}</span>
                                            <span class="text-sm font-bold opacity-80">XP</span>
                                        </div>
                                    </div>
                                    <!-- Custom Progress Bar -->
                                    <div class="w-full bg-black/20 rounded-full h-4 p-1 backdrop-blur-sm">
                                        <div class="bg-gradient-to-r from-yellow-300 to-yellow-500 h-2 rounded-full shadow-lg transition-all duration-1000 relative" style="width: {{ $user->level_progress }}%">
                                            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full shadow"></div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-right mt-3 text-white/70">
                                        @if($user->points_to_next_level > 0)
                                            Butuh <strong>{{ number_format($user->points_to_next_level) }} XP</strong> lagi untuk naik level! 🚀
                                        @else Anda adalah Legend! Max Level tercapai. @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Security Forms -->
                            <div class="grid grid-cols-1 gap-6">
                                <div class="p-8 bg-white dark:bg-dark-card shadow-sm border border-gray-100 dark:border-gray-700 rounded-3xl">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                                        🔒 Update Password
                                    </h3>
                                    <livewire:profile.update-password-form />
                                </div>
                                <div class="p-8 bg-white dark:bg-dark-card shadow-sm border border-gray-100 dark:border-gray-700 rounded-3xl">
                                    <h3 class="text-lg font-bold text-red-500 mb-4 flex items-center gap-2">
                                        ⚠ Zona Bahaya
                                    </h3>
                                    <p class="text-gray-500 text-sm mb-4">Menghapus akun akan menghilangkan semua data dan poin Anda secara permanen.</p>
                                    <livewire:profile.delete-user-form />
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- TAB 2: BADGES -->
                    @if($activeTab === 'badges')
                        <div class="bg-white dark:bg-dark-card shadow-sm border border-gray-100 dark:border-gray-700 rounded-3xl p-8 animate-fade-in-up">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">🏅 Hall of Badges</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Koleksi lencana prestasi Anda di LOGNITY.</p>
                            
                            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                                @foreach($badges as $badge)
                                    <div class="group relative rounded-2xl p-4 flex flex-col items-center text-center transition-all duration-300 {{ $badge['unlocked'] ? 'bg-white dark:bg-gray-800 border-2 border-lognity-100 dark:border-gray-600 shadow-md hover:-translate-y-1' : 'bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 opacity-60 grayscale' }}">
                                        
                                        <!-- Icon Circle -->
                                        <div class="w-16 h-16 flex items-center justify-center rounded-full text-3xl mb-3 shadow-inner {{ $badge['color'] }} {{ $badge['unlocked'] ? '' : 'bg-gray-200 text-gray-400' }}">
                                            {{ $badge['icon'] }}
                                        </div>
                                        
                                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200 mb-1 leading-tight">
                                            {{ $badge['name'] }}
                                        </h4>
                                        
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mb-3 px-2 leading-relaxed h-8 flex items-center justify-center">
                                            {{ $badge['desc'] }}
                                        </p>

                                        <!-- Progress Bar -->
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-auto overflow-hidden">
                                            <div class="h-2 rounded-full {{ $badge['unlocked'] ? 'bg-green-500' : 'bg-lognity-400' }}" 
                                                 style="width: {{ $badge['percent'] }}%"></div>
                                        </div>
                                        <p class="text-[10px] mt-2 font-mono {{ $badge['unlocked'] ? 'text-green-600 font-bold' : 'text-gray-400' }}">
                                            {{ number_format($badge['current']) }} / {{ number_format($badge['target']) }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- TAB 3: REQUESTS -->
                    @if($activeTab === 'requests')
                        <div class="space-y-4 animate-fade-in-up">
                            @forelse($myRequests as $req)
                                <div class="bg-white dark:bg-dark-card p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1 pr-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-xs font-bold px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300">
                                                    {{ $req->category }}
                                                </span>
                                                <span class="text-xs text-gray-400">• {{ $req->created_at->format('d M Y') }}</span>
                                            </div>
                                            <a href="{{ route('forum.show', $req->request_id) }}" class="text-lg font-bold text-gray-800 dark:text-white group-hover:text-lognity-600 transition block mb-1">
                                                {{ Str::limit($req->description, 100) }}
                                            </a>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                                {{ $req->answers_count }} Jawaban
                                            </div>
                                        </div>
                                        <div>
                                            <span class="px-3 py-1 text-xs rounded-full font-bold {{ $req->status == 'Open' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                                {{ $req->status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-16 bg-white dark:bg-dark-card rounded-3xl border border-dashed border-gray-300 dark:border-gray-700">
                                    <div class="text-4xl mb-3">📝</div>
                                    <p class="text-gray-500">Belum ada request yang dibuat.</p>
                                </div>
                            @endforelse
                            <div class="mt-4">{{ $myRequests->links() }}</div>
                        </div>
                    @endif

                    <!-- TAB 4: INTERACTIONS -->
                    @if($activeTab === 'interactions')
                        <div class="space-y-4 animate-fade-in-up">
                            @forelse($myInteractions as $int)
                                <div class="bg-white dark:bg-dark-card p-6 rounded-2xl shadow-sm border-l-4 {{ $int->is_accepted_answer ? 'border-green-500' : 'border-lognity-500' }} border-gray-100 dark:border-gray-700 hover:shadow-md transition">
                                    <div class="flex items-center gap-2 text-xs text-gray-400 mb-3">
                                        <span>{{ $int->created_at->diffForHumans() }}</span>
                                        @if($int->is_accepted_answer) 
                                            <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-0.5 rounded font-bold text-[10px] uppercase">Jawaban Terbaik (+50 XP)</span> 
                                        @else 
                                            <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded font-bold text-[10px] uppercase">Komentar</span>
                                        @endif
                                    </div>
                                    
                                    <p class="text-gray-700 dark:text-gray-300 text-sm mb-4 leading-relaxed italic bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                                        "{{ Str::limit($int->content, 120) }}"
                                    </p>
                                    
                                    <div class="text-xs flex items-center gap-1 text-gray-500 dark:text-gray-400">
                                        <span>Pada diskusi:</span>
                                        @if($int->request)
                                            <a href="{{ route('forum.show', $int->request_id) }}" class="text-lognity-600 dark:text-lognity-400 font-bold hover:underline">
                                                {{ Str::limit($int->request->description, 60) }}
                                            </a>
                                        @else
                                            <span class="text-red-400 italic">Konten telah dihapus</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-16 bg-white dark:bg-dark-card rounded-3xl border border-dashed border-gray-300 dark:border-gray-700">
                                    <div class="text-4xl mb-3">💬</div>
                                    <p class="text-gray-500">Belum ada interaksi.</p>
                                </div>
                            @endforelse
                            <div class="mt-4">{{ $myInteractions->links() }}</div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>