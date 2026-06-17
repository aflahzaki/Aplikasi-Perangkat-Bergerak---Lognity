<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Forum;

class UserController extends Controller
{
    // Melihat profil pengguna lain beserta forum yang mereka buat
    public function showProfile($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $requests = \App\Models\Request::where('user_id', $user->user_id)
            ->latest()
            ->get();

        $interactions = \App\Models\Interaction::with('request')
            ->where('user_id', $user->user_id)
            ->latest()
            ->get();

        return response()->json([
            'user' => [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'email' => $user->email,
                'profil_url' => $user->profil_url,
                'current_level' => $user->current_level,
                'points' => $user->points,
                'role' => $user->role,
                'badges' => array_values($user->unlocked_badges),
            ],
            'recent_requests' => $requests,
            'recent_interactions' => $interactions,
        ]);
    }

    // Mengganti password pengguna saat ini
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // Pastikan request memiliki new_password_confirmation
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama salah.'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah.'
        ]);
    }

    // Mengupdate profil pengguna (username, email, foto profil)
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'username' => 'nullable|string|min:3|max:20|unique:users,username,' . $user->user_id . ',user_id',
            'email'    => 'nullable|email|unique:users,email,' . $user->user_id . ',user_id',
            'photo'    => 'nullable|image|max:2048',
        ]);

        if ($request->has('username')) {
            $user->username = $request->username;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->hasFile('photo')) {
            if ($user->profil && $user->profil !== 'ppdefault.png') {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profil);
            }
            $user->profil = $request->file('photo')->store('profiles', 'public');
        } elseif ($request->has('remove_photo') && $request->remove_photo) {
            if ($user->profil && $user->profil !== 'ppdefault.png') {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profil);
            }
            $user->profil = null;
        }

        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user' => [
                'username' => $user->username,
                'email' => $user->email,
                'profil_url' => $user->profil_url,
            ]
        ]);
    }

    public function leaderboard(Request $request)
    {
        $limit = $request->query('limit', 20);
        $users = User::select('user_id', 'username', 'points', 'current_level', 'profil')
            ->orderBy('points', 'desc')
            ->take($limit)
            ->get();
            
        $users->transform(function ($user) {
            $user->profil_url = $user->profil_url; // Use accessor
            return $user;
        });

        return response()->json(['leaderboard' => $users]);
    }
}
