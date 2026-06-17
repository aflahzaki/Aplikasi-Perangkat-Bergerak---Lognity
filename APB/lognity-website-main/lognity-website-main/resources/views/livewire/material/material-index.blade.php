<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span>📚</span> Perpustakaan Materi
                </h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                    Cari, unduh, dan bagikan materi kuliah dengan teman seangkatan.
                </p>
            </div>
            
            <a href="{{ route('material.create') }}" class="bg-gradient-to-r from-lognity-600 to-fun-purple text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Upload Materi
            </a>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white dark:bg-dark-card p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-8 max-w-2xl">
            <div class="relative flex items-center">
                <svg class="w-5 h-5 text-gray-400 absolute left-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full border-none rounded-xl py-3 pl-12 pr-4 bg-transparent focus:ring-0 text-gray-800 dark:text-white placeholder-gray-400" placeholder="Cari judul materi, mata kuliah, atau tags...">
            </div>
        </div>
        
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" class="bg-green-100 dark:bg-green-900/30 border border-green-400 text-green-700 dark:text-green-300 px-6 py-4 rounded-2xl mb-8 flex justify-between items-center shadow-sm font-bold animate-fade-in-down">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('message') }}
                </span>
                <button @click="show = false" class="text-green-600 hover:text-green-800">✕</button>
            </div>
        @endif

        <!-- Grid Materi -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($materials as $mat)
                @php
                    $ext = strtolower(pathinfo($mat->file_path, PATHINFO_EXTENSION));
                    // Menentukan warna icon berdasarkan tipe file
                    $iconColor = match($ext) {
                        'pdf' => 'text-red-500 bg-red-50 dark:bg-red-900/20',
                        'doc', 'docx' => 'text-blue-500 bg-blue-50 dark:bg-blue-900/20',
                        'xls', 'xlsx', 'csv' => 'text-green-500 bg-green-50 dark:bg-green-900/20',
                        'ppt', 'pptx' => 'text-orange-500 bg-orange-50 dark:bg-orange-900/20',
                        'zip', 'rar' => 'text-gray-500 bg-gray-50 dark:bg-gray-800',
                        default => 'text-indigo-500 bg-indigo-50 dark:bg-indigo-900/20',
                    };
                @endphp

                <div class="group bg-white dark:bg-dark-card rounded-3xl shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col justify-between border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden relative">
                    
                    <!-- Decorative Top Border -->
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-lognity-400 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <!-- Icon File -->
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center {{ $iconColor }}">
                                <span class="text-xs font-extrabold uppercase">{{ $ext }}</span>
                            </div>
                            
                            <!-- Download Count Badge -->
                            <div class="flex items-center gap-1 text-xs font-bold text-gray-400 bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                {{ $mat->download_count }}
                            </div>
                        </div>

                        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2 leading-tight group-hover:text-lognity-600 dark:group-hover:text-lognity-400 transition-colors">
                            {{ Str::limit($mat->title, 50) }}
                        </h3>
                        
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-gray-200 overflow-hidden">
                                <a href="{{ route('user.show', $mat->uploader->user_id) }}">
                                    <img src="{{ $mat->uploader->profil_url }}" class="w-full h-full object-cover hover:opacity-80 transition">
                                </a>
                            </div>
                            <a href="{{ route('user.show', $mat->uploader->user_id) }}" class="text-xs text-gray-500 font-medium hover:text-indigo-600 hover:underline">{{ $mat->uploader->username }}</a>
                            <span class="text-gray-300">•</span>
                            <span class="text-xs text-gray-400">{{ $mat->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 line-clamp-2 leading-relaxed">
                            {{ $mat->description ?? 'Tidak ada deskripsi.' }}
                        </p>
                        
                        <!-- Tags -->
                        <div class="flex flex-wrap gap-2 mb-2">
                            @foreach(explode(',', $mat->tags) as $tag)
                                @if(trim($tag))
                                    <span class="text-[10px] bg-lognity-50 dark:bg-gray-800 text-lognity-600 dark:text-gray-300 px-2 py-1 rounded-md font-bold uppercase tracking-wide border border-lognity-100 dark:border-gray-700">
                                        #{{ trim($tag) }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Footer Button -->
                    <div class="px-6 pb-6 pt-0">
                        <button wire:click="download({{ $mat->material_id }})" class="w-full py-2.5 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold text-sm border border-gray-200 dark:border-gray-700 hover:bg-lognity-600 hover:text-white hover:border-lognity-600 dark:hover:bg-white dark:hover:text-black transition-all flex items-center justify-center gap-2 group/btn">
                            <span>Download File</span>
                            <svg class="w-4 h-4 transition-transform group-hover/btn:translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-20 bg-white dark:bg-dark-card rounded-3xl border border-dashed border-gray-300 dark:border-gray-700">
                    <div class="text-5xl mb-4 opacity-50">📂</div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Perpustakaan Kosong</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada materi yang diupload atau tidak ditemukan.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $materials->links() }}
        </div>
    </div>
</div>