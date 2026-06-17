<div class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
    @if(Auth::check())
        @if($isOpen)
            <div class="bg-white dark:bg-dark-card rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 w-80 sm:w-96 mb-4 overflow-hidden flex flex-col transition-all duration-300" style="height: 500px; max-height: calc(100vh - 120px);">
                
                @if($view === 'list')
                    <!-- Header List -->
                    <div class="bg-lognity-600 dark:bg-lognity-800 p-4 text-white flex justify-between items-center rounded-t-2xl">
                        <h3 class="font-bold font-quicksand flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                            Pesan
                        </h3>
                        <button wire:click="togglePopup" class="text-white/80 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Chat List -->
                    <div class="flex-1 overflow-y-auto" wire:poll.5s>
                        @if($chats->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500 p-6 text-center">
                                <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                <p class="text-sm">Belum ada obrolan.</p>
                            </div>
                        @else
                            @foreach($chats as $chat)
                                <div wire:click="openChatWithUser({{ $chat['user']->user_id }})" class="flex items-center gap-3 p-3 border-b border-gray-50 dark:border-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <img src="{{ $chat['user']->profil_url }}" class="w-12 h-12 rounded-full object-cover bg-gray-100">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-center mb-1">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm truncate">{{ $chat['user']->username }}</h4>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-400">{{ $chat['time'] }}</span>
                                                <button wire:click.stop="deleteChat({{ $chat['user']->user_id }})" class="text-gray-400 hover:text-red-500 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate {{ $chat['unread'] > 0 ? 'font-semibold text-gray-800 dark:text-gray-300' : '' }}">{{ $chat['last_message'] }}</p>
                                    </div>
                                    @if($chat['unread'] > 0)
                                        <span class="bg-fun-pink text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $chat['unread'] }}</span>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                @elseif($view === 'chat' && $activeUser)
                    <!-- Header Chat -->
                    <div class="bg-lognity-600 dark:bg-lognity-800 p-3 text-white flex justify-between items-center rounded-t-2xl">
                        <div class="flex items-center gap-3">
                            <button wire:click="backToList" class="text-white/80 hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <img src="{{ $activeUser->profil_url }}" class="w-8 h-8 rounded-full bg-white/20">
                            <h3 class="font-bold font-quicksand text-sm">{{ $activeUser->username }}</h3>
                        </div>
                        <button wire:click="togglePopup" class="text-white/80 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900/30 flex flex-col gap-3" wire:poll.3s id="chat-messages-container">
                        @foreach($messages as $msg)
                            @if($msg->sender_id == Auth::id())
                                <!-- My message -->
                                <div class="self-end max-w-[80%]">
                                    <div class="bg-lognity-500 text-white p-3 rounded-2xl rounded-tr-sm text-sm shadow-sm">
                                        {{ $msg->message }}
                                    </div>
                                    <span class="text-[10px] text-gray-400 mt-1 block text-right">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                            @else
                                <!-- Their message -->
                                <div class="self-start max-w-[80%]">
                                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-gray-800 dark:text-gray-200 p-3 rounded-2xl rounded-tl-sm text-sm shadow-sm">
                                        {{ $msg->message }}
                                    </div>
                                    <span class="text-[10px] text-gray-400 mt-1 block">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                            @endif
                        @endforeach
                        
                        <!-- Auto scroll to bottom script -->
                        <script>
                            setTimeout(function() {
                                var container = document.getElementById('chat-messages-container');
                                if(container) {
                                    container.scrollTop = container.scrollHeight;
                                }
                            }, 50);
                        </script>
                    </div>

                    <!-- Input Area -->
                    <div class="p-3 bg-white dark:bg-dark-card border-t border-gray-100 dark:border-gray-800 rounded-b-2xl">
                        <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                            <input wire:model="messageInput" type="text" placeholder="Ketik pesan..." class="flex-1 bg-gray-100 dark:bg-gray-800 border-none rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-lognity-500 dark:text-white" required>
                            <button type="submit" class="bg-fun-pink hover:bg-pink-600 text-white p-2 rounded-full transition-colors flex-shrink-0">
                                <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @endif

        <!-- Floating Button -->
        <button wire:click="togglePopup" class="bg-fun-pink hover:bg-pink-600 text-white p-4 rounded-full shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative group z-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($isOpen)
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                @endif
            </svg>
            @if(!$isOpen)
                <!-- Optional: Unread badge logic could go here, for now static visual -->
                <span class="absolute top-0 right-0 flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-fun-yellow opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-fun-yellow border-2 border-fun-pink"></span>
                </span>
            @endif
        </button>
    @endif
</div>
