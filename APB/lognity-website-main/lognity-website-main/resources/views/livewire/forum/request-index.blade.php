<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Notifikasi -->
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" class="bg-green-100 dark:bg-green-900/30 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded-2xl relative mb-6 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="block sm:inline font-bold">{{ session('message') }}</span>
                <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- KOLOM KIRI: FILTER & SEARCH -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Card Pencarian & Filter -->
                <div class="bg-white dark:bg-dark-card p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 sticky top-24">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-2">
                        <span>🔍</span> Cari & Filter
                    </h3>
                    
                    <!-- Search -->
                    <div class="mb-5">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">Kata Kunci / Matkul</label>
                        <input wire:model.live.debounce.300ms="search" type="text" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition-all py-2.5 px-4" placeholder="Cari...">
                    </div>

                    <!-- Kategori -->
                    <div class="mb-5">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">Kategori</label>
                        <select wire:model.live="filterCategory" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition-all py-2.5 px-4 appearance-none">
                            <option value="">Semua Kategori</option>
                            <option>Catatan</option>
                            <option>Tugas</option>
                            <option>Jawaban UTS/UAS</option>
                            <option>Kuis</option>
                            <option>Presentasi</option>
                            <option>Mindmap</option>
                            <option>Diskusi</option>
                            <option>Latihan</option>
                            <option>Proyek</option>
                            <option>Lain-Lain</option>
                        </select>
                    </div>

                    <!-- Fakultas -->
                    <div class="mb-5">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">Fakultas</label>
                        <select wire:model.live="filterFaculty" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition-all py-2.5 px-4 appearance-none">
                            <option value="">Semua Fakultas</option>
                            <option value="Teknik">Teknik</option>
                            <option value="Ekonomi">Ekonomi</option>
                            <option value="Ilmu Komputer">Ilmu Komputer</option>
                            <option value="Kedokteran">Kedokteran</option>
                        </select>
                    </div>

                    <!-- Urutkan -->
                    <div class="mb-6">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">Urutkan</label>
                        <select wire:model.live="sort" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition-all py-2.5 px-4 appearance-none">
                            <option value="latest">Terbaru</option>
                            <option value="popular">Terpopuler (Upvotes)</option>
                            <option value="oldest">Terlama</option>
                        </select>
                    </div>

                    <button wire:click="$set('search', ''); $set('filterCategory', ''); $set('filterFaculty', '')" 
                            class="w-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold text-sm py-3 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- KOLOM KANAN: FORM CREATE & LIST -->
            <div class="lg:col-span-3 space-y-6">
                
                <!-- FORM BUAT REQUEST BARU -->
                <div x-data="{ open: false }" class="bg-white dark:bg-dark-card p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-center cursor-pointer group" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="bg-lognity-100 dark:bg-lognity-900/30 p-3 rounded-2xl text-lognity-600 dark:text-lognity-400 group-hover:bg-lognity-200 dark:group-hover:bg-lognity-900/50 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-800 dark:text-white group-hover:text-lognity-600 dark:group-hover:text-lognity-400 transition">Butuh Materi? Buat Request!</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tanyakan komunitas untuk mendapatkan referensi atau jawaban.</p>
                            </div>
                        </div>
                        <div class="text-lognity-600 dark:text-lognity-400 bg-lognity-50 dark:bg-lognity-900/20 p-2 rounded-full transition transform" :class="{'rotate-180': open}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    
                    <div x-show="open" x-collapse class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-6">
                        <form wire:submit.prevent="createRequest">
                            <textarea wire:model="description" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition-all p-4 mb-4" placeholder="Deskripsikan requestmu dengan detail..." rows="3"></textarea>
                            @error('description') <span class="text-fun-pink text-xs font-bold block mb-4 ml-2">{{ $message }}</span> @enderror

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1 block">Kategori</label>
                                    <select wire:model="category" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition-all py-3 px-4 appearance-none">
                                        <option>Lain-Lain</option>
                                        <option>Catatan</option>
                                        <option>Tugas</option>
                                        <option>Jawaban UTS/UAS</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1 block">Fakultas (Opsional)</label>
                                    <input wire:model="faculty" type="text" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition-all py-3 px-4" placeholder="Contoh: Fasilkom">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1 block">Mata Kuliah (Opsional)</label>
                                    <input wire:model="course_name" type="text" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition-all py-3 px-4" placeholder="Contoh: Aljabar Linier">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1 block">Lampiran</label>
                                    <input type="file" wire:model="attachment" class="w-full text-xs text-gray-500 dark:text-gray-400
                                      file:mr-4 file:py-2.5 file:px-4
                                      file:rounded-xl file:border-0
                                      file:text-xs file:font-bold
                                      file:bg-lognity-50 file:text-lognity-700
                                      hover:file:bg-lognity-100
                                      dark:file:bg-gray-700 dark:file:text-white
                                      transition cursor-pointer
                                    ">
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-6 py-3 rounded-xl font-bold hover:bg-lognity-600 dark:hover:bg-gray-200 transition shadow-md hover:shadow-lg transform active:scale-95 flex items-center gap-2">
                                    <span>Post Request</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- LIST REQUEST -->
                <div class="space-y-5">
                    @forelse($requests as $req)
                        <div class="bg-white dark:bg-dark-card p-6 sm:p-8 rounded-3xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-gray-700 group flex gap-4 sm:gap-6">
                            
                            <!-- Tombol Upvote (Kiri) -->
                            <div class="flex flex-col items-center pt-1">
                                <button wire:click="toggleUpvote({{ $req->request_id }})" 
                                    class="flex flex-col items-center bg-gray-50 dark:bg-gray-800 hover:bg-orange-50 dark:hover:bg-orange-900/20 px-3 py-4 rounded-2xl transition-colors duration-300 border border-transparent {{ $req->is_upvoted ? 'border-orange-200 dark:border-orange-800/50 bg-orange-50 dark:bg-orange-900/20' : 'hover:border-orange-200 dark:hover:border-orange-800/50' }}">
                                    
                                    <svg class="w-8 h-8 mb-2 transition {{ $req->is_upvoted ? 'text-orange-500 fill-current' : 'text-gray-400 group-hover:text-orange-400' }}" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    </svg>
                                    
                                    <span class="font-extrabold text-lg {{ $req->is_upvoted ? 'text-orange-600 dark:text-orange-400' : 'text-gray-600 dark:text-gray-400' }}">
                                        {{ $req->upvotes_count }}
                                    </span>
                                </button>
                            </div>

                            <!-- Deskripsi & Detail -->
                            <div class="flex-1 min-w-0">
                                <!-- Meta Top -->
                                <div class="flex items-center flex-wrap gap-2 text-xs mb-3">
                                    <a href="{{ route('user.show', $req->user->user_id) }}" class="font-bold text-lognity-600 dark:text-lognity-400 hover:underline flex items-center gap-1.5">
                                        <img src="{{ $req->user->profil_url }}" class="w-5 h-5 rounded-full object-cover">
                                        {{ $req->user->username }}
                                    </a>
                                    <span class="text-gray-300 dark:text-gray-600">•</span>
                                    <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-md font-bold">{{ $req->category }}</span>
                                    @if($req->course_name) 
                                        <span class="text-gray-300 dark:text-gray-600">•</span>
                                        <span class="text-gray-500 dark:text-gray-400 font-medium truncate max-w-[150px] sm:max-w-[200px]">{{ $req->course_name }}</span> 
                                    @endif
                                    <span class="ml-auto text-gray-400 dark:text-gray-500 font-medium">{{ $req->created_at->diffForHumans() }}</span>
                                </div>
                                
                                <!-- Judul/Deskripsi -->
                                <a href="{{ route('forum.show', $req->request_id) }}" wire:navigate class="block text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 hover:text-lognity-600 dark:hover:text-lognity-400 transition-colors mb-4 line-clamp-3 leading-snug">
                                    {{ $req->description }}
                                </a>
                                
                                <!-- Tags Bawah -->
                                <div class="flex flex-wrap items-center justify-between gap-4 mt-auto">
                                    <div class="flex space-x-4 text-sm">
                                        <span class="flex items-center gap-1.5 font-bold {{ $req->answers_count > 0 ? 'text-fun-purple dark:text-purple-400' : 'text-gray-500 dark:text-gray-400' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                            {{ $req->answers_count }} Jawaban
                                        </span>
                                        @if($req->faculty)
                                            <span class="flex items-center gap-1.5 font-medium text-gray-500 dark:text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                {{ $req->faculty }}
                                            </span>
                                        @endif
                                    </div>

                                    <a href="{{ route('forum.show', $req->request_id) }}" class="px-5 py-2.5 bg-lognity-50 dark:bg-lognity-900/30 text-lognity-700 dark:text-lognity-300 text-sm font-bold rounded-xl hover:bg-lognity-100 dark:hover:bg-lognity-900/50 transition-colors">
                                        Lihat Diskusi
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-20 bg-white dark:bg-dark-card rounded-3xl border border-dashed border-gray-300 dark:border-gray-700">
                            <div class="text-5xl mb-4 opacity-50">📭</div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Request Tidak Ditemukan</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">Tidak ada request yang cocok dengan kriteria filter Anda.</p>
                            <button wire:click="$set('search', ''); $set('filterCategory', ''); $set('filterFaculty', '')" class="px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-bold hover:bg-lognity-600 dark:hover:bg-gray-200 transition">
                                Bersihkan Filter
                            </button>
                        </div>
                    @endforelse
                    
                    <div class="mt-8">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('alert', (data) => {
            alert(data[0].message);
        });
    });
</script>