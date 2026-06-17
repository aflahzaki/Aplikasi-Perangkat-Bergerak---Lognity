<div class="py-10">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="relative bg-gradient-to-r from-lognity-600 to-fun-purple dark:from-lognity-800 dark:to-purple-900 rounded-3xl p-8 sm:p-10 shadow-xl overflow-hidden text-white mb-8">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-40 h-40 bg-fun-pink opacity-20 rounded-full blur-2xl"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="p-3 bg-white/20 backdrop-blur-md text-white rounded-2xl shadow-lg border border-white/30">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold drop-shadow-md">Log & Manajemen Poin</h2>
                    <p class="text-white/90">Pantau dan atur poin pengguna secara manual.</p>
                </div>
            </div>
        </div>

        <!-- FORM EDIT POIN CARD -->
        <div class="bg-white dark:bg-dark-card rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden mb-10 relative">
            <div class="absolute top-0 left-0 w-2 h-full bg-indigo-500"></div>
            
            <div class="p-8">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                    <span>🛠</span> Edit Poin Manual
                </h3>
                
                @if (session()->has('message'))
                    <div class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl mb-6 font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('message') }}
                    </div>
                @endif
                
                <form wire:submit.prevent="updatePoints" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <!-- 1. Custom Search User Component -->
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 ml-1">Target Username</label>
                            
                            @if($selectedUserId)
                                <div class="flex items-center justify-between p-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold">
                                            {{ substr($selectedUserName, 0, 1) }}
                                        </div>
                                        <span class="font-bold text-indigo-700 dark:text-indigo-300">{{ $selectedUserName }}</span>
                                    </div>
                                    <button type="button" wire:click="$set('selectedUserId', null)" class="text-red-500 text-xs font-bold hover:underline">Ganti</button>
                                </div>
                            @else
                                <div class="relative">
                                    <input wire:model.live.debounce.300ms="searchUserQuery" type="text" 
                                        class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 p-3 pl-10 text-sm shadow-sm transition" 
                                        placeholder="Ketik nama user..." autocomplete="off">
                                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                
                                <!-- Dropdown Hasil Search -->
                                @if(!empty($searchUserQuery) && count($searchResults) > 0)
                                    <div class="absolute z-20 w-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl mt-2 max-h-60 overflow-y-auto">
                                        @foreach($searchResults as $user)
                                            <div wire:click="selectUser({{ $user->user_id }}, '{{ $user->username }}')" 
                                                 class="p-3 hover:bg-indigo-50 dark:hover:bg-gray-700 cursor-pointer border-b dark:border-gray-700 last:border-0 transition flex justify-between items-center group">
                                                <div>
                                                    <div class="font-bold text-sm text-gray-800 dark:text-gray-200">{{ $user->username }}</div>
                                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                                </div>
                                                <span class="text-xs font-bold bg-gray-100 dark:bg-gray-900 px-2 py-1 rounded text-gray-600 dark:text-gray-400 group-hover:bg-white">
                                                    {{ $user->points }} XP
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif(!empty($searchUserQuery))
                                    <div class="absolute z-20 w-full bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl shadow-xl mt-2 p-4 text-xs text-gray-500 text-center">User tidak ditemukan.</div>
                                @endif
                            @endif
                            @error('selectedUserId') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- 2. Input Poin -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 ml-1">Jumlah Poin (+/-)</label>
                            <input wire:model="pointsAmount" type="number" placeholder="Contoh: 50 atau -20" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 p-3 text-sm shadow-sm transition">
                            @error('pointsAmount') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- 3. Alasan -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 ml-1">Alasan Penyesuaian</label>
                            <input wire:model="reason" type="text" placeholder="Misal: Bonus kontributor aktif" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 p-3 text-sm shadow-sm transition">
                            @error('reason') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="submit" class="bg-gray-900 dark:bg-white dark:text-black text-white px-6 py-3 rounded-xl hover:bg-indigo-600 dark:hover:bg-indigo-400 transition shadow-lg font-bold text-sm transform active:scale-95">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Log -->
        <div class="bg-white dark:bg-dark-card shadow-sm rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h3 class="font-bold text-gray-700 dark:text-gray-300">Riwayat Perubahan Poin</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktivitas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-dark-card divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($log->timestamp)->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $log->username }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $log->activity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold 
                                        {{ $log->points_change > 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $log->points_change > 0 ? '+' : '' }}{{ $log->points_change }} XP
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $logs->links() }}</div>
    </div>
</div>