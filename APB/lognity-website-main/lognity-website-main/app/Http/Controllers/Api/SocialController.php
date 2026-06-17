<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class SocialController extends Controller
{
    public function followStatus($id)
    {
        $user = Auth::user();
        $isFollowing = $user->following()->where('following_id', $id)->exists();
        return response()->json(['is_following' => $isFollowing]);
    }

    public function follow($id)
    {
        $user = Auth::user();
        if ($user->user_id == $id) {
            return response()->json(['message' => 'Cannot follow yourself'], 400);
        }
        $user->following()->syncWithoutDetaching([$id]);
        return response()->json(['message' => 'Followed successfully']);
    }

    public function unfollow($id)
    {
        $user = Auth::user();
        $user->following()->detach($id);
        return response()->json(['message' => 'Unfollowed successfully']);
    }

    public function getChats()
    {
        $userId = Auth::id();

        // Ambil semua pesan di mana user adalah pengirim atau penerima
        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $chats = [];
        $processedUserIds = [];

        foreach ($messages as $message) {
            $otherUserId = $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
            
            if (!in_array($otherUserId, $processedUserIds)) {
                $otherUser = User::find($otherUserId);
                if ($otherUser) {
                    $unreadCount = Message::where('sender_id', $otherUserId)
                        ->where('receiver_id', $userId)
                        ->where('is_read', false)
                        ->count();

                    $chats[] = [
                        'chat_id' => $otherUserId,
                        'user' => [
                            'id' => $otherUser->user_id,
                            'username' => $otherUser->username,
                            'profil_url' => $otherUser->profil_url
                        ],
                        'last_message' => $message->message,
                        'time' => $message->created_at->format('H:i'),
                        'unread_count' => $unreadCount
                    ];
                    $processedUserIds[] = $otherUserId;
                }
            }
        }

        return response()->json($chats);
    }

    public function getMessages($id)
    {
        $userId = Auth::id();

        // Tandai sebagai sudah dibaca
        Message::where('sender_id', $id)
            ->where('receiver_id', $userId)
            ->update(['is_read' => true]);

        $messages = Message::where(function($q) use ($userId, $id) {
            $q->where('sender_id', $userId)->where('receiver_id', $id);
        })->orWhere(function($q) use ($userId, $id) {
            $q->where('sender_id', $id)->where('receiver_id', $userId);
        })->orderBy('created_at', 'asc')->get();

        $formattedMessages = $messages->map(function($msg) use ($userId) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'message' => $msg->message,
                'time' => $msg->created_at->format('H:i'),
                'is_me' => $msg->sender_id == $userId
            ];
        });

        return response()->json($formattedMessages);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);
        
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $id,
            'message' => $request->message,
            'is_read' => false
        ]);

        return response()->json([
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'message' => $message->message,
            'time' => $message->created_at->format('H:i'),
            'is_me' => true
        ]);
    }

    public function deleteChat($id)
    {
        $userId = Auth::id();
        
        Message::where(function($q) use ($userId, $id) {
            $q->where('sender_id', $userId)->where('receiver_id', $id);
        })->orWhere(function($q) use ($userId, $id) {
            $q->where('sender_id', $id)->where('receiver_id', $userId);
        })->delete();

        return response()->json(['message' => 'Chat deleted successfully']);
    }
}
