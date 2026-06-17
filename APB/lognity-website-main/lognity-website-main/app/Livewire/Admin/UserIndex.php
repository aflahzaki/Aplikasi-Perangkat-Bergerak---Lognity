<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\BannedIp;

#[Layout('layouts.app')]
class UserIndex extends Component
{
    use WithPagination;

    public $search = '';

    // Form Create Admin
    public $newUsername, $newEmail, $newPassword;

    // Variabel Modal Ban
    public $showBanModal = false;
    public $targetBanUserId = null;
    public $targetBanUsername = '';
    public $banDuration = '1'; // Default 1 hari

    // Function Create Admin (Diambil dari kode lama)
    public function createAdmin()
    {
        if (Auth::user()->role !== 'Superadmin') abort(403);

        $this->validate([
            'newUsername' => 'required|unique:users,username',
            'newEmail'    => 'required|email|unique:users,email',
            'newPassword' => 'required|min:8',
        ]);

        User::create([
            'username'          => $this->newUsername,
            'email'             => $this->newEmail,
            'password'          => Hash::make($this->newPassword),
            'role'              => 'Admin', // Pastikan string 'Admin' sesuai Enum di DB
            'points'            => 0,
            'current_level'     => 'Maba',
            'email_verified_at' => now(),
        ]);

        $this->reset(['newUsername', 'newEmail', 'newPassword']);
        session()->flash('message', 'Akun Admin berhasil dibuat!');
    }

    // 1. Buka Modal Ban
    public function confirmBan($userId, $username)
    {
        $user = User::find($userId);

        // Proteksi: Admin tidak bisa ban sesama Admin/Superadmin
        if (in_array($user->role, ['Admin', 'Superadmin'])) {
            session()->flash('error', 'Anda tidak bisa mem-banned sesama Admin.');
            return;
        }

        $this->targetBanUserId = $userId;
        $this->targetBanUsername = $username;
        $this->banDuration = '1'; // Reset ke default
        $this->showBanModal = true;
    }

    // 2. Eksekusi Ban
    public function applyBan()
    {
        if (!$this->targetBanUserId) return;

        $user = User::find($this->targetBanUserId);

        if ($this->banDuration === 'permanent') {
            $expiration = null;
            $msgDuration = "Permanen";
        } else {
            $days = (int) $this->banDuration;
            $expiration = now()->addDays($days);
            $msgDuration = "$days Hari";
        }

        // 1. Ban Akun User
        $user->update([
            'is_banned' => true,
            'ban_expiration' => $expiration
        ]);

        // 2. Ban IP User (FITUR BARU)
        // Cek apakah user punya record IP terakhir
        if ($user->last_ip_address) {
            // Cek supaya tidak duplikat
            $existingIpBan = BannedIp::where('ip_address', $user->last_ip_address)->first();
            
            if (!$existingIpBan) {
                BannedIp::create([
                    'ip_address' => $user->last_ip_address,
                    'reason' => 'Banned otomatis mengikuti akun: ' . $user->username,
                    'expiration' => $expiration // Durasi IP ban sama dengan durasi Akun ban
                ]);
            }
        }

        $this->showBanModal = false;
        session()->flash('message', "User {$user->username} & IP-nya berhasil dibanned ($msgDuration). User akan terlogout paksa.");
    }

    // 3. Unban User
    public function unbanUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Unban User
        $user->update(['is_banned' => false, 'ban_expiration' => null]);

        // Unban IP (Opsional: Jika ingin IP-nya dibuka juga)
        if ($user->last_ip_address) {
            BannedIp::where('ip_address', $user->last_ip_address)->delete();
        }

        session()->flash('message', 'User dan IP berhasil di-unbanned.');
    }

    public function closeBanModal()
    {
        $this->showBanModal = false;
    }

    public function render()
    {
        $users = User::where('username', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.user-index', ['users' => $users]);
    }

    public function deleteUser($userId)
    {
        // 1. Proteksi: Hanya Superadmin yang boleh akses fungsi ini
        if (Auth::user()->role !== 'Superadmin') {
            abort(403, 'Hanya Superadmin yang bisa menghapus user.');
        }

        $user = User::findOrFail($userId);

        // 2. Proteksi: Tidak boleh menghapus diri sendiri
        if ($user->user_id === Auth::id()) {
            session()->flash('error', 'Anda tidak bisa menghapus akun sendiri.');
            return;
        }

        // 3. Eksekusi Hapus
        $username = $user->username;
        $user->delete();

        session()->flash('message', "Akun $username berhasil dihapus permanen.");
    }

}