<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Coba Login Standar
        if (! Auth::attempt($this->only('email', 'password'), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // 2. CEK STATUS BANNED (LOGIKA BARU)
        $user = Auth::user();

        if ($user->is_banned) {
            
            // Cek apakah masa ban sudah habis (Auto-Unban)
            if ($user->ban_expiration && now()->greaterThan($user->ban_expiration)) {
                $user->update(['is_banned' => false, 'ban_expiration' => null]);
                
                // Bersihkan limiter dan biarkan login lanjut
                RateLimiter::clear($this->throttleKey());
                return;
            }

            // Siapkan Pesan untuk Popup
            $pesan = "Akun Anda telah dibanned.";
            if ($user->ban_expiration) {
                $pesan .= "\nHukuman berakhir pada: " . \Carbon\Carbon::parse($user->ban_expiration)->translatedFormat('d F Y H:i');
            } else {
                $pesan .= "\n(Status: Permanen).";
            }

            // FORCE LOGOUT (Tendang Keluar)
            Auth::guard('web')->logout();
            session()->invalidate();
            session()->regenerateToken();

            // Kunci: Kirim session 'banned' agar Popup muncul
            session()->flash('banned', $pesan);

            // Lempar error agar script berhenti (redirect otomatis ke login page oleh Livewire)
            throw ValidationException::withMessages([
                'email' => 'Akses ditolak karena akun dibanned.',
            ]);
        }

        // Jika lolos semua cek
        RateLimiter::clear($this->throttleKey());
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 4)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower($this->email).'|'.request()->ip());
    }
}