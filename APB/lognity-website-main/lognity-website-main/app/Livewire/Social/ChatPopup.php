<?php

namespace App\Livewire\Social;

use Livewire\Component;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ChatPopup extends Component
{
    public $isOpen = false;
    public $activeChatId = null; // User ID of the person we're chatting with
    public $messageInput = '';
    
    // UI states
    public $view = 'list'; // 'list' or 'chat'

    #[On('open-chat')]
    public function openChatWithUser($userId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $this->isOpen = true;
        $this->activeChatId = $userId;
        $this->view = 'chat';
    }

    public function togglePopup()
    {
        $this->isOpen = !$this->isOpen;
        if (!$this->isOpen) {
            $this->activeChatId = null;
            $this->view = 'list';
        }
    }

    public function backToList()
    {
        $this->activeChatId = null;
        $this->view = 'list';
    }

    public function deleteChat($otherUserId)
    {
        $userId = Auth::id();
        Message::where(function($q) use ($userId, $otherUserId) {
            $q->where('sender_id', $userId)->where('receiver_id', $otherUserId);
        })->orWhere(function($q) use ($userId, $otherUserId) {
            $q->where('sender_id', $otherUserId)->where('receiver_id', $userId);
        })->delete();

        if ($this->activeChatId == $otherUserId) {
            $this->backToList();
        }
    }

    public function sendMessage()
    {
        $this->validate(['messageInput' => 'required|string']);
        
        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->activeChatId,
            'message' => $this->messageInput,
            'is_read' => false
        ]);

        $this->messageInput = '';
    }

    public function render()
    {
        $chats = [];
        $messages = [];
        $activeUser = null;

        if (Auth::check()) {
            $userId = Auth::id();

            if ($this->view === 'list') {
                // Ambil recent chats
                $recentMessages = Message::where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $processed = [];
                foreach ($recentMessages as $msg) {
                    $otherId = $msg->sender_id == $userId ? $msg->receiver_id : $msg->sender_id;
                    if (!in_array($otherId, $processed)) {
                        $otherUser = User::find($otherId);
                        if ($otherUser) {
                            $unread = Message::where('sender_id', $otherId)
                                ->where('receiver_id', $userId)
                                ->where('is_read', false)
                                ->count();
                            
                            $chats[] = [
                                'user' => $otherUser,
                                'last_message' => $msg->message,
                                'time' => $msg->created_at->diffForHumans(null, true, true),
                                'unread' => $unread
                            ];
                        }
                        $processed[] = $otherId;
                    }
                }
            } elseif ($this->view === 'chat' && $this->activeChatId) {
                $activeUser = User::find($this->activeChatId);
                
                // Mark as read
                Message::where('sender_id', $this->activeChatId)
                    ->where('receiver_id', $userId)
                    ->update(['is_read' => true]);

                $messages = Message::where(function($q) use ($userId) {
                    $q->where('sender_id', $userId)->where('receiver_id', $this->activeChatId);
                })->orWhere(function($q) use ($userId) {
                    $q->where('sender_id', $this->activeChatId)->where('receiver_id', $userId);
                })->orderBy('created_at', 'asc')->get();
            }
        }

        return view('livewire.social.chat-popup', [
            'chats' => collect($chats),
            'messages' => collect($messages),
            'activeUser' => $activeUser
        ]);
    }
}
