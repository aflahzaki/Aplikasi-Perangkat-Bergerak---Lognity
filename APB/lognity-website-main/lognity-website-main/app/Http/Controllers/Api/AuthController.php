<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // Jika user dibanned
        if ($user->is_banned) {
            return response()->json([
                'message' => 'Akun Anda sedang dibanned.'
            ], 403);
        }

        // Hapus token lama agar tidak menumpuk (opsional)
        $user->tokens()->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'email' => $user->email,
                'profil_url' => $user->profil_url,
                'role' => $user->role,
                'points' => $user->points,
                'current_level' => $user->current_level,
            ]
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'User',
            'points' => 0,
            'current_level' => 'Maba',
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'token' => $token,
            'user' => [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'email' => $user->email,
                'profil_url' => $user->profil_url,
                'role' => $user->role,
                'points' => $user->points,
                'current_level' => $user->current_level,
            ]
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}
