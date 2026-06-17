<div>
    <!-- Hero Section Leaderboard -->
    <div class="relative bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 rounded-3xl p-8 sm:p-10 shadow-xl overflow-hidden text-white mb-8">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white opacity-20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-40 h-40 bg-yellow-200 opacity-20 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-2 drop-shadow-md">Hall of Fame 🏆</h2>
                <p class="text-white/90 text-lg">Para pembelajar paling aktif dan kontributif di Lognity.</p>
            </div>
            <div class="bg-white/20 backdrop-blur-md p-4 rounded-2xl border border-white/30 text-center w-full md:w-auto">
                <div class="text-xs uppercase tracking-wider font-bold text-white/80 mb-1">Rank Anda</div>
                <div class="text-3xl font-extrabold text-white">
                    @php
                        $myRank = \App\Models\User::orderBy('points', 'desc')->pluck('user_id')->search(auth()->id()) + 1;
                    @endphp
                    #{{ $myRank ?: '-' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Leaderboard List -->
    <div class="bg-white dark:bg-dark-card rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider font-bold">
                        <th class="py-4 px-6 text-center w-16">Rank</th>
                        <th class="py-4 px-6">Mahasiswa</th>
                        <th class="py-4 px-6 text-center">Level</th>
                        <th class="py-4 px-6 text-right">Total XP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ auth()->id() === $user->user_id ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                            <td class="py-4 px-6 text-center">
                                @php
                                    $rank = $users->firstItem() + $index;
                                @endphp
                                @if($rank === 1)
                                    <div class="w-10 h-10 mx-auto bg-gradient-to-tr from-yellow-300 to-yellow-500 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-lg shadow-yellow-500/40">1</div>
                                @elseif($rank === 2)
                                    <div class="w-10 h-10 mx-auto bg-gradient-to-tr from-gray-300 to-gray-400 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-lg shadow-gray-400/40">2</div>
                                @elseif($rank === 3)
                                    <div class="w-10 h-10 mx-auto bg-gradient-to-tr from-orange-300 to-orange-500 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-lg shadow-orange-500/40">3</div>
                                @else
                                    <div class="w-10 h-10 mx-auto bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full flex items-center justify-center font-bold text-lg">{{ $rank }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <a href="{{ route('user.show', $user->user_id) }}" class="flex items-center gap-4 group">
                                    <div class="relative w-12 h-12 flex-shrink-0">
                                        <img src="{{ $user->profil_url }}" class="w-12 h-12 rounded-full object-cover border-2 border-transparent group-hover:border-lognity-500 transition-colors shadow-sm">
                                        @if($rank <= 3)
                                            <div class="absolute -top-1 -right-1 text-xs">👑</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-lg group-hover:text-lognity-500 transition-colors flex items-center gap-2">
                                            {{ $user->username }}
                                            @if(auth()->id() === $user->user_id)
                                                <span class="bg-lognity-100 dark:bg-lognity-900/50 text-lognity-600 dark:text-lognity-400 text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wider font-bold">You</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ $user->role }}</div>
                                    </div>
                                </a>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full text-xs font-bold uppercase tracking-wider">
                                    {{ $user->current_level }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="font-extrabold text-xl text-gray-800 dark:text-gray-100">{{ number_format($user->points) }} <span class="text-sm text-lognity-500 dark:text-lognity-400 font-bold">XP</span></div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                Belum ada data mahasiswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/30">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
