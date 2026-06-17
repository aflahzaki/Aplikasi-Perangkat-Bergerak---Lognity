<div class="py-10">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="relative bg-gradient-to-r from-lognity-600 to-fun-purple dark:from-lognity-800 dark:to-purple-900 rounded-3xl p-8 sm:p-10 shadow-xl overflow-hidden text-white mb-8">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-40 h-40 bg-fun-pink opacity-20 rounded-full blur-2xl"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="p-3 bg-white/20 backdrop-blur-md text-white rounded-2xl shadow-lg border border-white/30">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold drop-shadow-md">Laporan Masuk</h2>
                    <p class="text-white/90">Tinjau laporan pelanggaran komunitas dan berikan tindakan.</p>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl mb-6 font-bold flex items-center gap-2">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
            </div>
        @endif

        <div class="bg-white dark:bg-dark-card shadow-sm rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pelapor</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Target</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alasan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-dark-card divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($reports as $rpt)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-800 dark:text-gray-200">{{ $rpt->reporter ? $rpt->reporter->username : 'User Terhapus' }}</div>
                                    <div class="text-xs text-gray-400">{{ $rpt->created_at->diffForHumans() }}</div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border
                                        {{ $rpt->target_type == 'Request' ? 'bg-indigo-50 text-indigo-600 border-indigo-200' : '' }}
                                        {{ $rpt->target_type == 'Interaction' ? 'bg-purple-50 text-purple-600 border-purple-200' : '' }}
                                        {{ $rpt->target_type == 'Material' ? 'bg-green-50 text-green-600 border-green-200' : '' }}">
                                        {{ $rpt->target_type_label }}
                                    </span>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 mt-1 italic truncate max-w-xs">
                                        "{{ Str::limit($rpt->target_content, 40) }}"
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-red-600 dark:text-red-400 font-medium">
                                    {{ $rpt->reason }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs rounded-full font-bold
                                        {{ $rpt->status == 'Pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $rpt->status == 'Resolved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $rpt->status == 'Dismissed' ? 'bg-gray-100 text-gray-600' : '' }}">
                                        {{ $rpt->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button wire:click="openDetailModal({{ $rpt->report_id }})" class="text-indigo-600 hover:text-indigo-900 font-bold hover:underline">
                                        Periksa
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $reports->links() }}</div>
    </div>

    <!-- MODAL DETAIL -->
    @if($showDetailModal && $selectedReport)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" wire:click="closeDetailModal"></div>

            <div class="bg-white dark:bg-dark-card rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto relative z-10 animate-blob border border-white/10">
                
                <!-- Modal Header -->
                <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <span class="text-red-500">⚠</span> Laporan #{{ $selectedReport->report_id }}
                    </h3>
                    <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition text-2xl">&times;</button>
                </div>

                <div class="p-8 space-y-8">
                    
                    <!-- Section: Pelapor & Alasan -->
                    <div class="bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/50 rounded-2xl p-5 flex flex-col md:flex-row gap-6">
                        <div class="flex-1">
                            <span class="block text-xs font-bold text-red-400 uppercase tracking-widest mb-1">Pelapor</span>
                            <div class="font-bold text-gray-800 dark:text-gray-200 text-lg">{{ $selectedReport->reporter->username ?? 'Unknown' }}</div>
                        </div>
                        <div class="flex-1 border-t md:border-t-0 md:border-l border-red-200 dark:border-red-800 pt-4 md:pt-0 md:pl-6">
                            <span class="block text-xs font-bold text-red-400 uppercase tracking-widest mb-1">Keluhan</span>
                            <div class="font-bold text-red-600 dark:text-red-400 italic">"{{ $selectedReport->reason }}"</div>
                        </div>
                    </div>

                    <!-- Section: Konten Bermasalah -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Konten Dilaporkan ({{ $selectedReport->target_type_label }})</h4>
                        <div class="p-6 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl">
                            <div class="text-gray-800 dark:text-gray-200 whitespace-pre-line leading-relaxed">
                                {{ $selectedReport->target_content }}
                            </div>

                            @php
                                $file = null;
                                if($selectedReport->targetRequest) $file = $selectedReport->targetRequest->attachment_file;
                                elseif($selectedReport->targetMaterial) $file = $selectedReport->targetMaterial->file_path;
                            @endphp
                            
                            @if($file)
                                 <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <a href="{{ asset('storage/'.$file) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 hover:underline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        Lihat Lampiran File
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Section: Tersangka (Offender) -->
                    @php
                        $offender = null;
                        if($selectedReport->targetRequest) $offender = $selectedReport->targetRequest->user;
                        elseif($selectedReport->targetInteraction) $offender = $selectedReport->targetInteraction->user;
                        elseif($selectedReport->targetMaterial) $offender = $selectedReport->targetMaterial->uploader;
                    @endphp

                    <div class="bg-gray-100 dark:bg-gray-800/50 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-4">
                            @if($offender)
                                <div class="w-12 h-12 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-gray-500 font-bold text-xl">
                                    {{ substr($offender->username, 0, 1) }}
                                </div>
                                <div>
                                    <span class="block text-xs uppercase font-bold text-gray-400 mb-0.5">Pemilik Konten</span>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-800 dark:text-white text-lg">{{ $offender->username }}</span>
                                        @if($offender->is_banned)
                                            <span class="bg-red-600 text-white text-[10px] px-2 py-0.5 rounded font-bold uppercase">BANNED</span>
                                        @else
                                            <span class="bg-green-500 text-white text-[10px] px-2 py-0.5 rounded font-bold uppercase">AKTIF</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-500 font-bold">User Tidak Ditemukan</span>
                            @endif
                        </div>
                        
                        @if($offender)
                            <button wire:click="openBanModalFromReport" class="px-4 py-2 bg-white dark:bg-gray-700 border border-red-200 text-red-600 dark:text-red-400 rounded-xl text-xs font-bold hover:bg-red-50 dark:hover:bg-gray-600 transition shadow-sm">
                                ⚖️ {{ $offender->is_banned ? 'UPDATE HUKUMAN' : 'BERI HUKUMAN (BAN)' }}
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="px-8 py-6 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 flex flex-wrap justify-end gap-3">
                    @if($selectedReport->status == 'Pending')
                        <button wire:click="dismissReport({{ $selectedReport->report_id }})" class="px-5 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-600 dark:text-gray-300 font-bold text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                            Abaikan (Dismiss)
                        </button>
                        <button wire:click="deleteContent({{ $selectedReport->report_id }})" 
                                onclick="confirm('Yakin hapus konten ini? Tindakan ini tidak bisa dibatalkan.') || event.stopImmediatePropagation()"
                                class="px-5 py-2.5 bg-red-100 text-red-700 rounded-xl font-bold text-sm hover:bg-red-200 transition">
                            Hapus Konten
                        </button>
                        <button wire:click="markResolved({{ $selectedReport->report_id }})" class="px-5 py-2.5 bg-green-600 text-white rounded-xl font-bold text-sm hover:bg-green-700 shadow-lg transition">
                            ✔ Tandai Selesai
                        </button>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-bold text-sm uppercase">
                            Status: {{ $selectedReport->status }}
                        </span>
                        <button wire:click="closeDetailModal" class="px-5 py-2.5 bg-gray-800 dark:bg-white dark:text-black text-white rounded-xl font-bold text-sm">Tutup</button>
                    @endif
                </div>

            </div>
        </div>
    @endif

    <!-- MODAL BAN (Menggunakan Style Kartu Pilihan) -->
    @if($showBanModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/90 backdrop-blur-md">
            <div class="bg-white dark:bg-dark-card p-8 rounded-3xl shadow-2xl w-full max-w-md animate-fade-in-down border-2 border-red-500">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                         <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Konfirmasi Hukuman</h3>
                    <p class="text-gray-500 text-sm mt-1">Target: <span class="font-bold text-gray-800 dark:text-gray-200">{{ $targetBanUsername }}</span></p>
                </div>

                <div class="space-y-3 mb-8">
                    @foreach([1 => '1 Hari (Ringan)', 3 => '3 Hari (Sedang)', 7 => '1 Minggu (Berat)', 30 => '1 Bulan (Sangat Berat)'] as $val => $label)
                    <label class="flex items-center p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition group {{ $banDuration == $val ? 'ring-2 ring-red-500 border-transparent bg-red-50 dark:bg-red-900/20' : '' }}">
                        <input type="radio" name="ban_duration" wire:model="banDuration" value="{{ $val }}" class="text-red-600 focus:ring-red-500">
                        <span class="ml-3 font-bold text-sm text-gray-700 dark:text-gray-300 group-hover:text-red-600">{{ $label }}</span>
                    </label>
                    @endforeach
                    
                    <label class="flex items-center p-3 rounded-xl border border-red-200 bg-red-50 dark:bg-red-900/20 cursor-pointer hover:bg-red-100 dark:hover:bg-red-900/40 transition group {{ $banDuration == 'permanent' ? 'ring-2 ring-red-600' : '' }}">
                        <input type="radio" name="ban_duration" wire:model="banDuration" value="permanent" class="text-red-800 focus:ring-red-900">
                        <span class="ml-3 font-bold text-sm text-red-800 dark:text-red-400">⛔ PERMANEN (Blokir Akses)</span>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button wire:click="closeBanModal" class="flex-1 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-gray-600 transition">Batal</button>
                    <button wire:click="applyBan" class="flex-1 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-lg transition transform active:scale-95">
                        EKSEKUSI
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>