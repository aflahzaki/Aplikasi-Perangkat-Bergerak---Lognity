<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Back Link -->
        <a href="{{ route('material.index') }}" class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-lognity-600 mb-6 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Perpustakaan
        </a>

        <div class="bg-white dark:bg-dark-card rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-lognity-600 to-fun-purple p-8 text-center text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                <h2 class="text-2xl font-bold relative z-10">Upload Materi Baru</h2>
                <p class="text-white/80 text-sm mt-1 relative z-10">Bagikan ilmu, dapatkan poin (+20 XP)!</p>
            </div>
            
            <div class="p-8">
                <form wire:submit.prevent="upload" class="space-y-6">
                    
                    <!-- Judul -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 ml-1">Judul Materi</label>
                        <input type="text" wire:model="title" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white p-3 text-sm focus:ring-lognity-500 focus:border-lognity-500 transition shadow-sm" placeholder="Contoh: Rangkuman Aljabar Linear Bab 1">
                        @error('title') <span class="text-fun-pink text-xs font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 ml-1">Deskripsi Singkat</label>
                        <textarea wire:model="description" rows="3" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white p-3 text-sm focus:ring-lognity-500 focus:border-lognity-500 transition shadow-sm" placeholder="Jelaskan isi materinya..."></textarea>
                        @error('description') <span class="text-fun-pink text-xs font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- File Upload Zone -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 ml-1">File Materi</label>
                        
                        <div class="relative group">
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-8 text-center bg-gray-50 dark:bg-gray-800/50 group-hover:border-lognity-400 group-hover:bg-lognity-50 dark:group-hover:bg-gray-800 transition-all cursor-pointer">
                                
                                <input type="file" wire:model="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-0">
                                
                                <div class="space-y-2 pointer-events-none">
                                    <div class="mx-auto h-12 w-12 text-gray-400 group-hover:text-lognity-500 transition">
                                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="font-bold text-lognity-600">Klik untuk upload</span> atau drag and drop
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, DOC, PPT, ZIP (Max 10MB)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Loading Indicator -->
                        <div wire:loading wire:target="file" class="w-full mt-2">
                            <div class="flex items-center gap-2 text-xs font-bold text-lognity-600 animate-pulse">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Sedang memproses file...
                            </div>
                        </div>
                        
                        @error('file') <span class="text-fun-pink text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                        
                        <!-- Preview Filename if exists -->
                        @if ($file)
                            <div class="mt-2 text-xs font-bold text-green-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                File siap: {{ $file->getClientOriginalName() }}
                            </div>
                        @endif
                    </div>

                    <!-- Tags -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 ml-1">Tags</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400">#</span>
                            <input type="text" wire:model="tags" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white p-3 pl-7 text-sm focus:ring-lognity-500 focus:border-lognity-500 transition shadow-sm" placeholder="matematika, semester 1, rangkuman (pisahkan dengan koma)">
                        </div>
                        @error('tags') <span class="text-fun-pink text-xs font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 flex items-center justify-end border-t border-gray-100 dark:border-gray-700">
                         <a href="{{ route('material.index') }}" class="mr-4 text-sm font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">Batal</a>
                        <button type="submit" wire:loading.attr="disabled" class="bg-gray-900 dark:bg-white dark:text-black text-white px-8 py-3 rounded-xl font-bold hover:bg-lognity-600 dark:hover:bg-gray-200 transition shadow-lg transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                            Upload Sekarang
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>