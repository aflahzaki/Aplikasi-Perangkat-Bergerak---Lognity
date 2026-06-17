<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class LeaderboardIndex extends Component
{
    use WithPagination;

    public function render()
    {
        // Get top users ordered by points
        $topUsers = User::orderBy('points', 'desc')
                        ->paginate(15);

        return view('livewire.leaderboard-index', [
            'users' => $topUsers,
        ])->layout('layouts.app', ['header' => 'Leaderboard Peringkat Mahasiswa']);
    }
}
