<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UserProfile extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $username;
    public $photo; // Untuk upload baru
    public $email;
    public $currentPhoto; // Untuk preview foto lama

    // Statistik
    public $totalRequests;
    public $totalAnswers;
    public $totalMaterials;

    public $activeTab = 'overview';

    public function mount()
    {
        $user = Auth::user();
        $this->username = $user->username;
        $this->email = $user->email;
        $this->currentPhoto = $user->profil;

        // Hitung Statistik
        $this->totalRequests = $user->requests()->count();
        $this->totalAnswers = $user->interactions()->where('type', 'Answer')->count();
        // Asumsi relasi materials ada di model User
        $this->totalMaterials = \App\Models\Material::where('uploader_id', $user->user_id)->count(); 
    }

    // Fungsi Ganti Tab
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updateProfile()
    {
        $user = Auth::user();

        $this->validate([
            'username' => 'required|string|min:3|max:20|unique:users,username,' . $user->user_id . ',user_id',
            'email'    => 'required|email|unique:users,email,' . $user->user_id . ',user_id', // <--- VALIDASI EMAIL
            'photo'    => 'nullable|image|max:2048',
        ]);

        $data = [
            'username' => $this->username,
            'email'    => $this->email,
        ];

        if ($this->photo) {
            if ($user->profil && $user->profil !== 'ppdefault.png') {
                Storage::disk('public')->delete($user->profil);
            }
            $data['profil'] = $this->photo->store('profiles', 'public');
        }

        $user->update($data);
        $user->refresh();

        if(isset($data['profil'])) $this->currentPhoto = $data['profil'];
        $this->reset('photo'); 

        session()->flash('message', 'Profil & Email berhasil diperbarui!');
    }

    public function deleteProfilePhoto()
    {
        $user = User::find(Auth::id());

        // Cek jika user memang punya foto custom
        if ($user->profil && $user->profil !== 'ppdefault.png') {
            Storage::disk('public')->delete($user->profil);
            
            $user->update(['profil' => null]);
            $user->refresh();
            
            $this->currentPhoto = null;
            $this->reset('photo');

            session()->flash('message', 'Foto profil dihapus dan kembali ke default.');
        } elseif ($user->profil === 'ppdefault.png') {
            $user->update(['profil' => null]);
            $user->refresh();
            $this->currentPhoto = null;
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Data Dinamis berdasarkan Tab Aktif
        $myRequests = [];
        $myInteractions = [];

        if ($this->activeTab === 'requests') {
            $myRequests = $user->requests()
                ->withCount('answers')
                ->latest()
                ->paginate(10);
        }

        if ($this->activeTab === 'interactions') {
            $myInteractions = $user->interactions()
                ->with(['request', 'material']) // Eager load
                ->whereIn('type', ['Answer', 'Comment']) // Ambil jawaban & komentar
                ->latest()
                ->paginate(10);
        }

        return view('livewire.user-profile', [
            'user' => $user,
            'badges' => $user->badge_progress_list, // Panggil list 20 badge tadi
            'myRequests' => $myRequests,
            'myInteractions' => $myInteractions
        ]);
    }
}