<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\BannedIp;

class CheckBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $currentIp = $request->ip();

        // 1. CEK BANNED IP (Blokir total akses bahkan untuk tamu)
        $isIpBanned = BannedIp::where('ip_address', $currentIp)->first();
        
        if ($isIpBanned) {
            // Cek apakah durasi ban IP sudah habis?
            if ($isIpBanned->expiration && now()->greaterThan($isIpBanned->expiration)) {
                $isIpBanned->delete(); // Hapus dari daftar ban
            } else {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['message' => 'Akses dari IP Anda (' . $currentIp . ') telah diblokir.'], 403);
                }
                abort(403, 'Akses dari IP Anda (' . $currentIp . ') telah diblokir.');
            }
        }

        // 2. CEK USER BANNED (Force Logout / Token Revocation)
        if ($request->expectsJson() || $request->is('api/*')) {
            $user = $request->user('sanctum');
            if ($user) {
                // Update IP (Biarkan tetap ada)
                if ($user->last_ip_address !== $request->ip()) {
                    $user->update(['last_ip_address' => $request->ip()]);
                }

                if ($user->is_banned) {
                    // Cek Auto-Unban
                    if ($user->ban_expiration && now()->greaterThan($user->ban_expiration)) {
                        $user->update(['is_banned' => false, 'ban_expiration' => null]);
                    } else {
                        // Revoke current token
                        if ($user->currentAccessToken()) {
                            $user->currentAccessToken()->delete();
                        }

                        $pesan = "Akun Anda telah dibanned.";
                        if ($user->ban_expiration) {
                            $pesan .= " Hukuman berakhir pada: " . \Carbon\Carbon::parse($user->ban_expiration)->translatedFormat('d F Y H:i');
                        } else {
                            $pesan .= " (Status: Permanen).";
                        }
                        return response()->json(['message' => $pesan], 403);
                    }
                }
            }
        } else {
            if (Auth::check()) {
                $user = Auth::user();

                // Update IP (Biarkan tetap ada)
                if ($user->last_ip_address !== $request->ip()) {
                    $user->update(['last_ip_address' => $request->ip()]);
                }

                if ($user->is_banned) {
                    
                    // Cek Auto-Unban
                    if ($user->ban_expiration && now()->greaterThan($user->ban_expiration)) {
                        $user->update(['is_banned' => false, 'ban_expiration' => null]);
                        return $next($request);
                    }

                    // Pesan Hukuman
                    $pesan = "Akun Anda telah dibanned.";
                    if ($user->ban_expiration) {
                        $pesan .= "\nHukuman berakhir pada: " . \Carbon\Carbon::parse($user->ban_expiration)->translatedFormat('d F Y H:i');
                    } else {
                        $pesan .= "\n(Status: Permanen).";
                    }

                    // FORCE LOGOUT
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    // 👇 UBAH BAGIAN INI: Pakai 'with' key 'banned'
                    return redirect()->route('login')->with('banned', $pesan);
                }
            }
        }

        return $next($request);
    }
}