<!-- 👇 ROOT WRAPPER (Sangat Penting untuk Livewire) 👇 -->
<div> 
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Notifikasi Flash Message -->
            @if (session()->has('message'))
                <div x-data="{ show: true }" x-show="show" class="bg-green-100 dark:bg-green-900/30 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded-2xl relative mb-6 flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="block sm:inline font-bold">{{ session('message') }}</span>
                    <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </button>
                </div>
            @endif

            <!-- BAGIAN 1: PERTANYAAN UTAMA (REQUEST) -->
            <div class="bg-white dark:bg-dark-card rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Header Gradient Accent -->
                <div class="h-2 bg-gradient-to-r from-lognity-500 to-fun-purple"></div>
                <div class="p-6 sm:p-10">
                    
                    <!-- Header: User Info + Status + Report -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 border-b border-gray-100 dark:border-gray-700 pb-6">
                        <!-- Kiri: Info User -->
                        <div class="flex items-center gap-4">
                            <a href="{{ route('user.show', $request->user->user_id) }}" class="relative group">
                                <div class="absolute -inset-1 bg-gradient-to-r from-lognity-400 to-fun-pink rounded-full blur opacity-25 group-hover:opacity-75 transition duration-500"></div>
                                <img src="{{ $request->user->profil_url }}" alt="Avatar" class="relative w-14 h-14 rounded-full object-cover border-2 border-white dark:border-gray-800 shadow-md">
                            </a>
                            <div>
                                <a href="{{ route('user.show', $request->user->user_id) }}" class="font-extrabold text-xl text-gray-900 dark:text-white hover:text-lognity-600 dark:hover:text-lognity-400 transition">{{ $request->user->username }}</a>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded uppercase tracking-wider text-[10px] font-bold">{{ $request->user->current_level }}</span>
                                    <span>• {{ $request->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Kanan: Status & Report -->
                        <div class="flex items-center gap-3">
                            <!-- Badge Status -->
                            <span class="px-4 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider {{ $request->status == 'Open' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700' }}">
                                {{ $request->status }}
                            </span>

                            <div class="flex gap-2">
                                <!-- Tombol Lapor Request -->
                                @if(Auth::id() !== $request->user_id)
                                    <button type="button" wire:click="openReportModal('request', {{ $request->request_id }})" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition cursor-pointer" title="Laporkan Konten">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </button>
                                @endif

                                @if(Auth::id() === $request->user_id)
                                    <button type="button" wire:click="openEditRequestModal" class="p-2 text-gray-400 hover:text-lognity-600 hover:bg-lognity-50 dark:hover:bg-lognity-900/20 rounded-lg transition cursor-pointer" title="Edit Request">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                @endif

                                @if(Auth::id() === $request->user_id || Auth::user()->isAdmin())
                                    <button type="button" wire:click="deleteCurrentRequest" wire:confirm="Yakin ingin menghapus Request ini? Semua jawaban juga akan terhapus." class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition cursor-pointer" title="Hapus Request">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Judul / Deskripsi -->
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-gray-100 mb-6 leading-relaxed whitespace-pre-line">{{ $request->description }}</h1>

                    <!-- LOGIKA TAMPILAN LAMPIRAN REQUEST -->
                    @if($request->attachment_file)
                        @php
                            $ext = strtolower(pathinfo($request->attachment_file, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp

                        <div class="mt-8 p-5 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700/50">
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-lognity-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Lampiran File
                            </p>
                            
                            @if($isImage)
                                <a href="{{ asset('storage/' . $request->attachment_file) }}" target="_blank" class="block rounded-xl overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700 max-w-2xl">
                                    <img src="{{ asset('storage/' . $request->attachment_file) }}" alt="Lampiran" class="w-full hover:scale-105 transition duration-500">
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $request->attachment_file) }}" target="_blank" class="inline-flex items-center p-4 bg-white dark:bg-dark-card rounded-xl border border-gray-200 dark:border-gray-700 hover:border-lognity-400 dark:hover:border-lognity-500 transition shadow-sm group">
                                    <div class="bg-red-50 dark:bg-red-900/20 text-red-500 p-3 rounded-lg mr-4 group-hover:scale-110 transition">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold text-gray-900 dark:text-white group-hover:text-lognity-600 transition">Download File</span>
                                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $ext }} Document</span>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endif
                </div> 
            </div> 

            <!-- JUMLAH JAWABAN -->
            <div class="flex items-center gap-3">
                <div class="h-8 w-1 bg-lognity-500 rounded-full"></div>
                <h3 class="text-2xl font-extrabold text-gray-800 dark:text-white">
                    {{ $request->answers->count() }} Jawaban
                </h3>
            </div>

            <!-- LIST JAWABAN -->
            <div class="space-y-6">
                @forelse($request->answers as $answer)
                    <!-- Tambahkan wire:key agar livewire tidak bingung -->
                    <div wire:key="answer-{{ $answer->interaction_id }}" class="bg-white dark:bg-dark-card p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 transition duration-300 relative overflow-hidden
                        {{ $answer->is_accepted_answer ? 'ring-2 ring-green-500' : '' }}">
                        
                        @if($answer->is_accepted_answer)
                            <div class="absolute top-0 right-0 w-32 h-32 bg-green-400 opacity-10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                        @endif
                        
                        <div class="relative z-10 flex flex-col sm:flex-row sm:justify-between items-start gap-4 mb-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('user.show', $answer->user->user_id) }}">
                                    <img src="{{ $answer->user->profil_url }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-700 shadow-sm">
                                </a>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('user.show', $answer->user->user_id) }}" class="font-bold text-gray-900 dark:text-white hover:text-lognity-600 dark:hover:text-lognity-400 transition">{{ $answer->user->username }}</a>
                                        @if($answer->user_id === $request->user_id)
                                            <span class="px-2 py-0.5 bg-lognity-100 dark:bg-lognity-900/50 text-lognity-700 dark:text-lognity-400 text-[10px] font-bold rounded uppercase tracking-wider">Pemilik Request</span>
                                        @endif
                                    </div>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $answer->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                @if($answer->is_accepted_answer)
                                    <span class="flex items-center gap-1.5 text-green-700 dark:text-green-400 font-bold text-xs bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800/50 px-3 py-1.5 rounded-full uppercase tracking-wider shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Accepted Answer
                                    </span>
                                @endif

                                <!-- TOMBOL LAPOR JAWABAN -->
                                @if($answer->user_id !== Auth::id())
                                    <button type="button" wire:click="openReportModal('interaction', {{ $answer->interaction_id }})" class="p-1.5 text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 dark:bg-gray-800 dark:hover:bg-red-900/20 rounded-lg transition cursor-pointer" title="Laporkan Jawaban">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="relative z-10 text-gray-800 dark:text-gray-300 mt-2 mb-6 leading-relaxed whitespace-pre-line w-full text-[15px]">
                            @if($editingInteractionId === $answer->interaction_id)
                                <div class="mt-4">
                                    <textarea wire:model="editingContent" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition p-4 shadow-inner" rows="4"></textarea>
                                    @error('editingContent') <span class="text-fun-pink text-xs font-bold block mt-2">{{ $message }}</span> @enderror
                                    <div class="mt-3 flex gap-2">
                                        <button wire:click="updateInteraction" class="px-5 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold text-xs rounded-xl shadow hover:shadow-lg transition">Simpan</button>
                                        <button wire:click="cancelEdit" class="px-5 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs rounded-xl transition">Batal</button>
                                    </div>
                                </div>
                            @else
                                {{ $answer->content }}
                            @endif

                            <div class="flex gap-4 mt-6">
                                @if(Auth::id() === $answer->user_id)
                                    <button type="button" wire:click="editInteraction({{ $answer->interaction_id }})" class="text-lognity-600 dark:text-lognity-400 hover:text-lognity-800 dark:hover:text-lognity-300 transition cursor-pointer text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Edit
                                    </button>
                                @endif
                                @if(Auth::id() === $answer->user_id || Auth::user()->isAdmin())
                                    <button type="button" wire:click="deleteInteraction({{ $answer->interaction_id }})" wire:confirm="Yakin ingin menghapus jawaban ini?" class="text-red-500 hover:text-red-700 transition cursor-pointer text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus
                                    </button>
                                @endif
                            </div>

                            <!-- LAMPIRAN JAWABAN -->
                            @if($answer->material) 
                                @php
                                    $matExt = strtolower(pathinfo($answer->material->file_path, PATHINFO_EXTENSION));
                                    $matIsImage = in_array($matExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp
                                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700/50 inline-block">
                                    <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Lampiran Jawaban</p>
                                    @if($matIsImage)
                                        <a href="{{ asset('storage/' . $answer->material->file_path) }}" target="_blank" class="block rounded-lg overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700 w-fit">
                                            <img src="{{ asset('storage/' . $answer->material->file_path) }}" class="max-w-xs w-full hover:scale-105 transition duration-500">
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $answer->material->file_path) }}" target="_blank" class="flex items-center gap-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-gray-700 p-3 rounded-xl shadow-sm hover:border-lognity-400 transition group w-fit pr-6">
                                            <div class="bg-fun-purple/10 text-fun-purple p-2 rounded-lg group-hover:scale-110 transition">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900 dark:text-white max-w-[200px] truncate group-hover:text-lognity-500 transition">
                                                    {{ $answer->material->title }}
                                                </div>
                                                <div class="text-[10px] uppercase font-bold text-gray-500 dark:text-gray-400 mt-0.5">Download {{ $matExt }}</div>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if(Auth::id() === $request->user_id && $request->status === 'Open' && $answer->user_id !== Auth::id())
                            <div class="border-t pt-4 mt-2 border-gray-100 dark:border-gray-700 relative z-10">
                                <button wire:click="markAsAccepted({{ $answer->interaction_id }})" 
                                        onclick="confirm('Tandai ini sebagai jawaban terbaik? (+50 XP untuk penjawab)') || event.stopImmediatePropagation()"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/40 text-xs font-bold rounded-lg transition-colors border border-green-200 dark:border-green-800/50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Terima Jawaban Ini
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-16 bg-white dark:bg-dark-card rounded-3xl border border-dashed border-gray-300 dark:border-gray-700">
                        <div class="text-4xl mb-4 opacity-50">🌱</div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Belum Ada Jawaban</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Jadilah yang pertama membantu dan dapatkan 50 XP jika jawabanmu terpilih!</p>
                    </div>
                @endforelse
            </div>

            <!-- FORM TULIS JAWABAN -->
            @if($request->status === 'Open')
                <div class="bg-white dark:bg-dark-card p-6 sm:p-10 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 mt-10 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-lognity-400 opacity-5 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    <h3 class="font-extrabold text-xl text-gray-900 dark:text-white mb-6 relative z-10 flex items-center gap-2">
                        <span>💡</span> Tulis Jawaban
                    </h3>
                    <form wire:submit.prevent="submitAnswer" class="relative z-10">
                        <textarea wire:model="content" rows="4" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-4 shadow-inner text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition mb-4" placeholder="Berikan solusi terperinci untuk pertanyaan ini..."></textarea>
                        @error('content') <span class="text-fun-pink text-xs font-bold block mb-4 ml-2">{{ $message }}</span> @enderror

                        <div class="mb-6 bg-lognity-50 dark:bg-gray-800/50 p-4 rounded-xl border border-lognity-100 dark:border-gray-700">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-3">Lampirkan Materi/File (Opsional)</label>
                            <input type="file" wire:model="file" class="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-lognity-200 file:text-lognity-800 hover:file:bg-lognity-300 dark:file:bg-gray-700 dark:file:text-white transition cursor-pointer"/>
                            <div wire:loading wire:target="file" class="text-lognity-500 text-xs font-bold mt-3 flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-lognity-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Uploading file...
                            </div>
                            @error('file') <span class="text-fun-pink text-xs font-bold block mt-3">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" wire:loading.attr="disabled" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-8 py-3.5 rounded-xl font-bold shadow-md hover:shadow-xl hover:-translate-y-0.5 transform transition disabled:opacity-50 disabled:cursor-not-allowed">
                                Kirim Jawaban
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-gray-100 dark:bg-gray-800/80 p-6 rounded-2xl text-center border border-gray-200 dark:border-gray-700 mt-10 flex flex-col items-center gap-3">
                    <div class="bg-gray-200 dark:bg-gray-700 p-3 rounded-full text-2xl">🔒</div>
                    <div class="text-gray-500 dark:text-gray-400 font-bold">Topik ini telah diselesaikan (Resolved).</div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 max-w-sm">Anda tidak dapat lagi mengirim jawaban baru ke diskusi yang sudah memiliki jawaban terbaik yang diterima.</p>
                </div>
            @endif

        </div>
    </div>

    <!-- REPORT MODAL OVERLAY -->
    @if($showReportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeReportModal"></div>
            <div class="bg-white dark:bg-dark-card rounded-3xl shadow-2xl p-6 w-full max-w-md mx-4 relative z-10 animate-blob">
                <div class="flex items-center gap-3 mb-4 text-red-600">
                    <div class="bg-red-100 p-2 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>
                    <h3 class="text-lg font-bold">Laporkan Konten</h3>
                </div>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Jelaskan alasan Anda melaporkan konten ini.</p>
                
                <textarea wire:model="reportReason" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl mb-2 focus:ring-red-500 focus:border-red-500 dark:text-white text-sm p-4 transition" rows="3" placeholder="Contoh: Spam, Kata kasar, SARA..."></textarea>
                @error('reportReason') <span class="text-red-500 text-xs block mb-4 font-bold">{{ $message }}</span> @enderror

                <div class="flex justify-end gap-3 mt-4">
                    <button wire:click="closeReportModal" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-200 transition">Batal</button>
                    <button wire:click="submitReport" class="px-5 py-2.5 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 shadow-lg shadow-red-200 dark:shadow-none transition transform active:scale-95">Kirim Laporan</button>
                </div>
            </div>
        </div>
    @endif

    <!-- EDIT REQUEST MODAL -->
    @if($showEditRequestModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeEditRequestModal"></div>
            <div class="bg-white dark:bg-dark-card rounded-3xl shadow-2xl p-6 sm:p-8 w-full max-w-lg mx-4 relative z-10 animate-blob border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 mb-6 text-lognity-600 dark:text-lognity-400">
                    <div class="bg-lognity-100 dark:bg-lognity-900/30 p-2 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></div>
                    <h3 class="text-xl font-bold">Edit Request</h3>
                </div>
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1">Deskripsi</label>
                        <textarea wire:model="editReqDescription" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition" rows="4"></textarea>
                        @error('editReqDescription') <span class="text-fun-pink text-xs block mt-1 ml-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1">Kategori</label>
                        <select wire:model="editReqCategory" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition appearance-none">
                            <option value="Catatan">Catatan</option>
                            <option value="Tugas">Tugas</option>
                            <option value="Jawaban UTS/UAS">Jawaban UTS/UAS</option>
                            <option value="Diskusi">Diskusi</option>
                            <option value="Lain-Lain">Lain-Lain</option>
                        </select>
                        @error('editReqCategory') <span class="text-fun-pink text-xs block mt-1 ml-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1">Fakultas / Prodi</label>
                            <input type="text" wire:model="editReqFaculty" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1">Mata Kuliah</label>
                            <input type="text" wire:model="editReqCourseName" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button wire:click="closeEditRequestModal" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-200 transition">Batal</button>
                    <button wire:click="updateRequest" class="px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition transform active:scale-95">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    @endif
</div> 
<!-- 👆 PENUTUP ROOT DIV 👆 -->