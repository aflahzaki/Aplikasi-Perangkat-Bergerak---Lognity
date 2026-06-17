<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header -->
        <div class="relative bg-gradient-to-r from-lognity-600 to-fun-purple dark:from-lognity-800 dark:to-purple-900 rounded-3xl p-8 sm:p-10 shadow-xl overflow-hidden text-white">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-40 h-40 bg-fun-pink opacity-20 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <h2 class="text-3xl font-extrabold mb-2 drop-shadow-md flex items-center gap-3">
                    <span>👥</span> Manajemen User
                </h2>
                <p class="text-white/90">Kelola akses, status ban, dan data pengguna Lognity.</p>
            </div>
        </div>

        @if(auth()->user()->role === 'Superadmin')
            <div class="bg-white dark:bg-dark-card p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500 opacity-5 rounded-full blur-2xl -mr-10 -mt-10"></div>
                <h3 class="font-extrabold text-lg text-gray-900 dark:text-white mb-6 relative z-10 flex items-center gap-2">
                    <span class="bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 p-2 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg></span>
                    Buat Akun Admin Baru
                </h3>
                <form wire:submit.prevent="createAdmin" class="grid grid-cols-1 md:grid-cols-4 gap-4 relative z-10 items-start">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Username</label>
                        <input wire:model="newUsername" type="text" placeholder="Username" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition">
                        @error('newUsername') <span class="text-fun-pink text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Email</label>
                        <input wire:model="newEmail" type="email" placeholder="Email" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition">
                        @error('newEmail') <span class="text-fun-pink text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Password</label>
                        <input wire:model="newPassword" type="password" placeholder="Password" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition">
                        @error('newPassword') <span class="text-fun-pink text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-end h-full">
                        <button type="submit" class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-6 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transition transform active:scale-95 text-sm h-[46px] flex items-center justify-center">
                            Tambah Admin
                        </button>
                    </div>
                </form>
            </div>
        @endif

        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" class="bg-green-100 dark:bg-green-900/30 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded-2xl relative flex items-center gap-3 shadow-sm">
                <span class="block sm:inline font-bold">{{ session('message') }}</span>
                <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3"><svg class="h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" class="bg-red-100 dark:bg-red-900/30 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded-2xl relative flex items-center gap-3 shadow-sm">
                <span class="block sm:inline font-bold">{{ session('error') }}</span>
                <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3"><svg class="h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg></button>
            </div>
        @endif

        <div class="flex items-center gap-4">
            <div class="relative w-full md:w-1/3">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari User..." class="w-full pl-12 pr-4 py-3 bg-white dark:bg-dark-card border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-lognity-500 focus:border-lognity-500 dark:text-white transition-all shadow-sm">
            </div>
        </div>

        <div class="bg-white dark:bg-dark-card shadow-sm rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50/50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $user->profil_url }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->username }}</div>
                                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-bold uppercase tracking-wider">{{ $user->role }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_banned)
                                        <span class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800/50 rounded-full text-xs font-bold flex w-fit items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                                            Banned ({{ $user->ban_expiration ? \Carbon\Carbon::parse($user->ban_expiration)->diffForHumans() : 'Permanen' }})
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800/50 rounded-full text-xs font-bold flex w-fit items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                            Aktif
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($user->role === 'User')
                                            @if($user->is_banned)
                                                <button wire:click="unbanUser({{ $user->user_id }})" class="bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/40 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                    Unban
                                                </button>
                                            @else
                                                <button wire:click="confirmBan({{ $user->user_id }}, '{{ $user->username }}')" class="bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 hover:bg-orange-100 dark:hover:bg-orange-900/40 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                    Ban User
                                                </button>
                                            @endif
                                        @endif

                                        @if(auth()->user()->role === 'Superadmin')
                                            @if($user->role !== 'Superadmin')
                                                <button wire:click="deleteUser({{ $user->user_id }})" 
                                                        onclick="confirm('⚠ PERINGATAN KERAS!\n\nMenghapus akun {{ $user->username }} akan menghapus SEMUA data terkait:\n- Materi Upload\n- Request\n- Jawaban & Poin\n\nTindakan ini TIDAK BISA DIBATALKAN.\nYakin ingin menghapus?') || event.stopImmediatePropagation()"
                                                        class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 p-1.5 rounded-lg transition" 
                                                        title="Hapus Akun Permanen">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL BAN -->
        @if($showBanModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeBanModal"></div>
                <div class="bg-white dark:bg-dark-card p-6 sm:p-8 rounded-3xl shadow-2xl w-full max-w-md relative z-10 animate-blob border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-6 text-red-600">
                        <div class="bg-red-100 dark:bg-red-900/30 p-2 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg></div>
                        <h3 class="text-xl font-bold">Ban: <span class="text-gray-900 dark:text-white">{{ $targetBanUsername }}</span></h3>
                    </div>
                    
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider">Durasi Hukuman</p>

                    <div class="space-y-3 mb-8">
                        @foreach([1 => '1 Hari', 3 => '3 Hari', 7 => '7 Hari', 30 => '30 Hari'] as $val => $label)
                            <label class="flex items-center gap-3 border border-gray-200 dark:border-gray-700 p-4 rounded-2xl cursor-pointer hover:bg-red-50 dark:hover:bg-red-900/20 transition group">
                                <input type="radio" wire:model="banDuration" value="{{ $val }}" class="text-red-600 focus:ring-red-500 w-5 h-5 bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-600">
                                <span class="font-bold text-gray-700 dark:text-gray-300 group-hover:text-red-600 dark:group-hover:text-red-400 transition">{{ $label }}</span>
                            </label>
                        @endforeach
                        <label class="flex items-center gap-3 border border-red-200 dark:border-red-800/50 p-4 rounded-2xl cursor-pointer bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 transition group">
                            <input type="radio" wire:model="banDuration" value="permanent" class="text-red-800 focus:ring-red-900 w-5 h-5">
                            <span class="font-extrabold text-red-800 dark:text-red-400 tracking-wide uppercase">Permanen (Selamanya)</span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button wire:click="closeBanModal" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-200 transition">Batal</button>
                        <button wire:click="applyBan" class="px-5 py-2.5 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 shadow-md transform active:scale-95 transition">Eksekusi Ban</button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>