<?php

namespace App\Livewire\Material;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Material;
use App\Models\DownloadLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('layouts.app')]
class MaterialIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function download($id)
    {
        $user = Auth::user();
        
        // 1. CEK BATASAN HARIAN
        if ($user->hasReachedLimit('download')) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Kuota download harian Anda habis!']);
            return;
        }

        $material = Material::findOrFail($id);
        
        // 2. CATAT LOG DOWNLOAD
        // Cek apakah user sudah download file yang sama hari ini? (Opsional: Agar tidak rugi kuota jika download ulang)
        $alreadyDownloaded = DownloadLog::where('user_id', $user->user_id)
            ->where('material_id', $id)
            ->whereDate('downloaded_at', Carbon::today())
            ->exists();

        if (!$alreadyDownloaded) {
            DownloadLog::create([
                'user_id' => $user->user_id,
                'material_id' => $id,
                'downloaded_at' => now()
            ]);
            
            // Increment counter total di tabel material
            $material->increment('download_count');
        }

        // 3. PROSES DOWNLOAD
        return response()->download(storage_path('app/public/' . $material->file_path));
    }

    public function render()
    {
        $materials = Material::with('uploader')
            ->where('title', 'like', '%' . $this->search . '%')
            ->orWhere('tags', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(9); // Grid 3x3

        return view('livewire.material.material-index', [
            'materials' => $materials
        ]);
    }
}