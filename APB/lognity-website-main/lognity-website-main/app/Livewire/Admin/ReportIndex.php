<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Report;
use App\Models\User;

#[Layout('layouts.app')]
class ReportIndex extends Component
{
    use WithPagination;

    // --- Variabel Detail Laporan ---
    public $selectedReport = null;
    public $showDetailModal = false;

    // --- Variabel Modal Ban ---
    public $showBanModal = false;
    public $targetBanUserId = null;
    public $targetBanUsername = '';
    public $banDuration = '1';

    // 1. Aksi: Buka Modal Detail
    public function openDetailModal($reportId)
    {
        $this->selectedReport = Report::with(['reporter', 'targetRequest', 'targetInteraction', 'targetMaterial'])
            ->find($reportId);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedReport = null;
    }

    // 2. Aksi: Tandai Selesai
    public function markResolved($reportId)
    {
        Report::find($reportId)->update(['status' => 'Resolved']);
        if ($this->showDetailModal) $this->closeDetailModal();
        session()->flash('message', 'Laporan ditandai selesai.');
    }

    // 3. Aksi: Abaikan
    public function dismissReport($reportId)
    {
        Report::find($reportId)->update(['status' => 'Dismissed']);
        if ($this->showDetailModal) $this->closeDetailModal();
        session()->flash('message', 'Laporan diabaikan.');
    }

    // 4. Aksi: Hapus Konten
    public function deleteContent($reportId)
    {
        $report = Report::find($reportId);

        if ($report->target_request_id) {
            $report->targetRequest()->delete();
        } elseif ($report->target_interaction_id) {
            $report->targetInteraction()->delete();
        } elseif ($report->target_material_id) {
            $report->targetMaterial()->delete();
        }

        $report->update(['status' => 'Resolved']);
        $this->closeDetailModal();
        session()->flash('message', 'KONTEN BERHASIL DIHAPUS DARI SISTEM.');
    }

    // 5. Aksi: Buka Modal Ban
    public function openBanModalFromReport()
    {
        $offender = null;
        if ($this->selectedReport->targetRequest) {
            $offender = $this->selectedReport->targetRequest->user;
        } elseif ($this->selectedReport->targetInteraction) {
            $offender = $this->selectedReport->targetInteraction->user;
        } elseif ($this->selectedReport->targetMaterial) {
            $offender = $this->selectedReport->targetMaterial->uploader;
        }

        if (!$offender) {
            session()->flash('message', 'User tidak ditemukan (mungkin sudah dihapus).');
            return;
        }

        if (in_array($offender->role, ['Admin', 'Superadmin'])) {
            session()->flash('message', 'Tidak bisa mem-ban Admin.');
            return;
        }

        $this->targetBanUserId = $offender->user_id;
        $this->targetBanUsername = $offender->username;
        $this->banDuration = '1'; // Default

        $this->showBanModal = true;
    }

    public function closeBanModal()
    {
        $this->showBanModal = false;
    }

    // 6. Aksi: Eksekusi Ban
    public function applyBan()
    {
        if (!$this->targetBanUserId) return;
        
        $user = User::find($this->targetBanUserId);

        if ($this->banDuration === 'permanent') {
            $expiration = null;
            $msg = "Permanen";
        } else {
            $days = (int) $this->banDuration;
            $expiration = now()->addDays($days);
            $msg = "$days Hari";
        }

        $user->update([
            'is_banned' => true, 
            'ban_expiration' => $expiration
        ]);

        if ($this->selectedReport) {
            $this->selectedReport->update(['status' => 'Resolved']);
        }

        $this->showBanModal = false;
        $this->showDetailModal = false; // Tutup semua
        
        session()->flash('message', "User {$user->username} BERHASIL DIBANNED ($msg). Laporan ditandai selesai.");
    }

    public function render()
    {
        $reports = Report::with(['reporter', 'targetRequest', 'targetInteraction', 'targetMaterial'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.report-index', [
            'reports' => $reports
        ]);
    }
}