<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class UserPublicProfile extends Component
{
    use WithPagination;

    public $userId;
    public $activeTab = 'overview'; // overview, requests, interactions, badges
    public $isFollowing = false;

    // Variabel Modal Report Akun
    public $showReportModal = false;
    public $reportReason = '';

    public function mount($id)
    {
        $this->userId = $id;
        if (Auth::check()) {
            $this->isFollowing = Auth::user()->following()->where('following_id', $id)->exists();
        }
    }

    public function toggleFollow()
    {
        if (!Auth::check()) return redirect()->route('login');
        if (Auth::id() == $this->userId) return;

        if ($this->isFollowing) {
            Auth::user()->following()->detach($this->userId);
            $this->isFollowing = false;
        } else {
            Auth::user()->following()->syncWithoutDetaching([$this->userId]);
            $this->isFollowing = true;
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    // --- LOGIKA LAPOR AKUN ---
    public function openReportModal()
    {
        if (!Auth::check()) return redirect()->route('login');
        if (Auth::id() == $this->userId) return; // Gak bisa lapor diri sendiri

        $this->reportReason = '';
        $this->showReportModal = true;
    }

    public function closeReportModal()
    {
        $this->showReportModal = false;
    }

    public function submitReport()
    {
        $this->validate(['reportReason' => 'required|min:5|max:255']);

        Report::create([
            'reporter_id' => Auth::id(),
            'target_user_id' => $this->userId, // Targetnya adalah User ID ini
            'reason' => $this->reportReason,
            'status' => 'Pending'
        ]);

        $this->showReportModal = false;
        session()->flash('message', 'Laporan akun berhasil dikirim.');
    }

    public function render()
    {
        $user = User::findOrFail($this->userId);

        // Statistik
        $stats = [
            'requests' => $user->requests()->count(),
            'answers' => $user->interactions()->where('type', 'Answer')->count(),
            'materials' => $user->materials()->count(),
        ];

        // Data Tab
        $dataRequests = [];
        $dataInteractions = [];

        if ($this->activeTab === 'requests') {
            $dataRequests = $user->requests()->latest()->paginate(10);
        }

        if ($this->activeTab === 'interactions') {
            $dataInteractions = $user->interactions()
                ->whereIn('type', ['Answer', 'Comment'])
                ->with('request')
                ->latest()
                ->paginate(10);
        }

        return view('livewire.user-public-profile', [
            'user' => $user,
            'stats' => $stats,
            'badges' => $user->badge_progress_list, // Pakai helper badge 20 item yg sudah dibuat
            'dataRequests' => $dataRequests,
            'dataInteractions' => $dataInteractions
        ]);
    }
}