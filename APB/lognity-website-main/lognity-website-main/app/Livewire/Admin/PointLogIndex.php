<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class PointLogIndex extends Component
{
    use WithPagination;

    public $pointsAmount, $reason;
    
    // Fitur Search User
    public $searchUserQuery = ''; 
    public $selectedUserId = null;
    public $selectedUserName = null;

    public function mount()
    {
        if (Auth::user()->role !== 'Superadmin') abort(403);
    }

    // Reset pencarian saat user mengetik ulang
    public function updatedSearchUserQuery()
    {
        $this->selectedUserId = null;
    }

    // Pilih user dari hasil pencarian
    public function selectUser($id, $name)
    {
        $this->selectedUserId = $id;
        $this->selectedUserName = $name;
        $this->searchUserQuery = ''; // Bersihkan search bar
    }

    public function updatePoints()
    {
        $this->validate([
            'selectedUserId' => 'required|exists:users,user_id',
            'pointsAmount' => 'required|integer|min:-5000|max:5000',
            'reason' => 'required|string|min:5'
        ]);

        $user = User::find($this->selectedUserId);
        
        // 1. Update Poin
        $user->increment('points', $this->pointsAmount);

        // 2. LOGIKA UPDATE LEVEL (Copy logic ini)
        $p = $user->points; // Ambil poin terbaru
        $newLevel = 'Maba';
        if ($p >= 8000) $newLevel = 'Artefak';
        elseif ($p >= 2500) $newLevel = 'Calon';
        elseif ($p >= 500) $newLevel = 'Aktif';

        // Hanya update jika level berubah
        if ($user->current_level !== $newLevel) {
            $user->update(['current_level' => $newLevel]);
        }

        // 3. Catat Log
        DB::table('point_logs')->insert([
            'user_id' => $user->user_id,
            'activity' => "Manual Admin: {$this->reason} (Level: $newLevel)",
            'points_change' => $this->pointsAmount,
            'timestamp' => now()
        ]);

        $this->reset(['pointsAmount', 'reason', 'selectedUserId', 'selectedUserName']);
        session()->flash('message', "Poin $user->username berhasil diupdate.");
    }

    public function render()
    {
        // Query untuk pencarian user (autocomplete)
        $searchResults = [];
        if (strlen($this->searchUserQuery) >= 2) {
            $searchResults = User::where('username', 'like', '%' . $this->searchUserQuery . '%')
                ->take(5)->get();
        }

        $logs = DB::table('point_logs')
            ->join('users', 'point_logs.user_id', '=', 'users.user_id')
            ->select('point_logs.*', 'users.username')
            ->orderBy('timestamp', 'desc')
            ->paginate(10);

        return view('livewire.admin.point-log-index', [
            'logs' => $logs,
            'searchResults' => $searchResults
        ]);
    }
}