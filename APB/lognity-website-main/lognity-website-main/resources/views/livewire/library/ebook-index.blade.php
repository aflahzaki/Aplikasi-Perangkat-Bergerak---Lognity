<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header & Action (Hero Section) -->
        <div class="relative bg-gradient-to-r from-lognity-600 to-fun-purple dark:from-lognity-800 dark:to-purple-900 rounded-3xl p-8 sm:p-10 shadow-xl overflow-hidden text-white mb-8">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-40 h-40 bg-fun-pink opacity-20 rounded-full blur-2xl"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h2 class="text-3xl md:text-4xl font-extrabold mb-2 drop-shadow-md flex items-center gap-3">
                        <span>🏛️</span> E-Library Kampus
                    </h2>
                    <p class="text-white/90 text-lg">Koleksi buku, modul, dan referensi belajar untuk mahasiswa.</p>
                </div>
                
                <!-- Tombol Tambah (Hanya Admin) -->
                @if(in_array(Auth::user()->role, ['Admin', 'Superadmin']))
                    <a href="{{ route('library.create') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-md text-white px-6 py-3 rounded-xl font-bold shadow-lg border border-white/30 transition transform hover:-translate-y-1 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Buku
                    </a>
                @endif
            </div>
        </div>

        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" class="bg-green-100 dark:bg-green-900/30 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded-2xl relative flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="block sm:inline font-bold">{{ session('message') }}</span>
                <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </button>
            </div>
        @endif

        <!-- Search & Filter -->
        <div class="bg-white dark:bg-dark-card p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row gap-4 relative z-10">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul atau penulis..." class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition-all">
            </div>
            
            <div class="relative md:w-64">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                </div>
                <select wire:model.live="filterCategory" class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition-all appearance-none cursor-pointer">
                    <option value="">Semua Kategori</option>
                    <option>Teknologi</option>
                    <option>Bisnis</option>
                    <option>Sains</option>
                    <option>Umum</option>
                </select>
            </div>
        </div>

        <!-- Grid Buku -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($ebooks as $book)
                <div class="group bg-white dark:bg-dark-card rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full transform hover:-translate-y-1">
                    
                    <!-- Cover Image -->
                    <div class="h-56 overflow-hidden bg-gray-100 dark:bg-gray-800 relative">
                        @if($book->cover_path)
                            <img src="{{ asset('storage/' . $book->cover_path) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                            <!-- Overlay Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        @else
                            <!-- Placeholder Cover jika tidak ada gambar -->
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900">
                                <svg class="w-16 h-16 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                <span class="text-xs font-bold tracking-widest uppercase opacity-50">{{ $book->category }}</span>
                            </div>
                        @endif
                        
                        <!-- Badge Kategori -->
                        <div class="absolute top-3 right-3 bg-black/60 dark:bg-black/80 text-white text-[10px] px-2.5 py-1 rounded-md font-bold uppercase tracking-wider backdrop-blur-md shadow-sm">
                            {{ $book->category }}
                        </div>

                        <!-- Hover Action (Read Button) -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                            <div class="bg-white/20 backdrop-blur-md border border-white/40 text-white px-4 py-2 rounded-full font-bold shadow-lg flex items-center gap-2 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Preview
                            </div>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="p-5 flex-1 flex flex-col relative z-10 bg-white dark:bg-dark-card">
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg leading-snug mb-1 line-clamp-2 group-hover:text-lognity-600 dark:group-hover:text-lognity-400 transition" title="{{ $book->title }}">
                            {{ $book->title }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-4 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ Str::limit($book->author, 25) }}
                        </p>
                        
                        <!-- Tombol Action -->
                        <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700/50 flex justify-between items-center">
                            <!-- Download Button -->
                            <a href="{{ asset('storage/' . $book->file_path) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 font-bold text-sm hover:text-indigo-800 dark:hover:text-indigo-300 flex items-center transition bg-indigo-50 dark:bg-indigo-900/30 px-3 py-1.5 rounded-lg">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Unduh
                            </a>

                            <!-- Hapus (Admin Only) -->
                            @if(in_array(Auth::user()->role, ['Admin', 'Superadmin']))
                                <button wire:click="deleteEbook({{ $book->id }})" wire:confirm="Hapus buku ini?" class="text-red-400 hover:text-red-600 dark:hover:text-red-300 p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 bg-white dark:bg-dark-card rounded-3xl border border-dashed border-gray-300 dark:border-gray-700">
                    <div class="text-5xl mb-4 opacity-50">📚</div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Buku Tidak Ditemukan</h3>
                    <p class="text-gray-500 dark:text-gray-400">Silakan coba dengan kata kunci atau kategori yang berbeda.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-8">
            {{ $ebooks->links() }}
        </div>
    </div>
</div>