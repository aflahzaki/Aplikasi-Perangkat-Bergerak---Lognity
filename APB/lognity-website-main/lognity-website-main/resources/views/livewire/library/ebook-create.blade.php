<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">📚 Upload Buku Resmi</h2>
            
            <form wire:submit.prevent="store" class="space-y-5">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Judul Buku</label>
                        <input wire:model="title" type="text" class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Penulis</label>
                        <input wire:model="author" type="text" class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white">
                        @error('author') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                    <select wire:model="category" class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white">
                        <option>Umum</option>
                        <option>Teknologi</option>
                        <option>Bisnis</option>
                        <option>Sains</option>
                        <option>Seni & Desain</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Deskripsi</label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Cover Buku (Gambar)</label>
                        <input wire:model="cover" type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('cover') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">File E-Book (PDF/EPUB)</label>
                        <input wire:model="file" type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="file" class="text-indigo-500 text-xs font-bold mt-1">Mengupload buku...</div>
                    </div>
                </div>

                <div class="pt-4 text-right">
                    <button type="submit" wire:loading.attr="disabled" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                        Simpan ke Perpustakaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>