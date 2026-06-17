<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use Livewire\WithFileUploads; // Wajib untuk upload file
use Livewire\Attributes\Layout;
use App\Models\Request as RequestModel;
use App\Models\Interaction;
use App\Models\Material;
use App\Models\Report; // <--- WAJIB: Jangan lupa import model Report
use App\Models\User;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class RequestShow extends Component
{
    use WithFileUploads;

    public $requestId;
    
    // Variabel Form Jawaban
    public $content;
    public $file; 

    // Variabel Modal Report
    public $showReportModal = false; // Mengatur muncul/hilang modal
    public $reportReason = '';
    public $reportTargetType = ''; 
    public $reportTargetId = null;

    // Variabel Edit Request
    public $showEditRequestModal = false;
    public $editReqDescription = '';
    public $editReqCategory = '';
    public $editReqFaculty = '';
    public $editReqCourseName = '';

    public function mount($id)
    {
        $this->requestId = $id;
    }

    public function openEditRequestModal()
    {
        $request = RequestModel::findOrFail($this->requestId);
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }
        $this->editReqDescription = $request->description;
        $this->editReqCategory = $request->category;
        $this->editReqFaculty = $request->faculty;
        $this->editReqCourseName = $request->course_name;
        $this->showEditRequestModal = true;
    }

    public function closeEditRequestModal()
    {
        $this->showEditRequestModal = false;
    }

    public function updateRequest()
    {
        $request = RequestModel::findOrFail($this->requestId);
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }

        $this->validate([
            'editReqDescription' => 'required|min:10|max:500',
            'editReqCategory'    => 'required',
        ]);

        $check = \App\Services\ContentModerator::check($this->editReqDescription);
        if ($check['is_toxic']) {
            $this->addError('editReqDescription', 'Deskripsi DITOLAK! Terdeteksi unsur: ' . $check['category']);
            return;
        }

        $request->update([
            'description' => $this->editReqDescription,
            'category'    => $this->editReqCategory,
            'faculty'     => $this->editReqFaculty,
            'course_name' => $this->editReqCourseName,
        ]);

        $this->showEditRequestModal = false;
        session()->flash('message', 'Request berhasil diupdate.');
    }

    // --- FUNGSI JAWAB & UPLOAD ---
    public function submitAnswer()
    {
        // CEK BATASAN HARIAN
        if (Auth::user()->hasReachedLimit('interaction')) {
            $this->addError('content', 'Kuota interaksi/jawab harian Anda habis.');
            return;
        }

        $this->validate([
            'content' => 'required|min:5|max:1000',
            'file'    => 'nullable|file|max:10240', // Max 10MB
        ]);

        $check = \App\Services\ContentModerator::check($this->content);

        if ($check['is_toxic']) {
            // EKSEKUSI HUKUMAN
            Auth::user()->decrement('points', 25);

            // Pesan Error
            $this->addError('content', 'Komentar DITOLAK! Terdeteksi unsur: ' . $check['category'] . '. Anda terkena penalti -25 Poin.');
            
            return; // STOP
        }

        $materialId = null;

        // 1. Jika ada file, Buat Material Baru
        if ($this->file) {
            $filePath = $this->file->store('materials', 'public');
            
            $material = Material::create([
                'uploader_id' => Auth::id(),
                'related_request_id' => $this->requestId,
                'title' => 'Jawaban untuk Request #' . $this->requestId,
                'description' => $this->content,
                'file_path' => $filePath,
                'tags' => 'jawaban',
            ]);
            
            $materialId = $material->material_id;
            
            // Tambah Poin Upload (+20)
            $this->addPoints(Auth::user(), 20);
        }

        // 2. Buat Interaksi
        Interaction::create([
            'user_id' => Auth::id(),
            'request_id' => $this->requestId,
            'material_id' => $materialId, // Link ke material
            'type' => 'Answer',
            'content' => $this->content,
        ]);

        $this->reset(['content', 'file']);
        session()->flash('message', 'Jawaban terkirim!');
    }

    // --- FUNGSI MENERIMA JAWABAN (ACCEPT) ---
    public function markAsAccepted($interactionId)
    {
        $request = RequestModel::find($this->requestId);
        $interaction = Interaction::find($interactionId);

        if (Auth::id() !== $request->user_id) abort(403);
        if ($interaction->user_id === Auth::id()) return; // Cegah self-accept

        $interaction->update(['is_accepted_answer' => true]);
        $request->update(['status' => 'Resolved']);

        // Beri poin ke penjawab
        $this->addPoints(User::find($interaction->user_id), 50);

        session()->flash('message', 'Jawaban diterima! Penjawab dapat +50 Poin.');
    }

    // --- FUNGSI REPORT (Agar Tombol Bekerja) ---
    public function openReportModal($type, $id)
    {
        // Fungsi ini dipanggil saat tombol report diklik
        $this->reportTargetType = $type;
        $this->reportTargetId = $id;
        $this->reportReason = '';
        $this->showReportModal = true; // Ini yang memunculkan Popup
    }

    public function closeReportModal()
    {
        $this->showReportModal = false;
    }

    public function submitReport()
    {
        $this->validate([
            'reportReason' => 'required|string|min:3|max:255',
        ]);

        $data = [
            'reporter_id' => Auth::id(),
            'reason' => $this->reportReason,
            'status' => 'Pending',
        ];

        // Tentukan target laporan
        if ($this->reportTargetType === 'request') {
            $data['target_request_id'] = $this->reportTargetId;
        } elseif ($this->reportTargetType === 'interaction') {
            $data['target_interaction_id'] = $this->reportTargetId;
        }

        Report::create($data);

        $this->showReportModal = false;
        session()->flash('message', 'Laporan berhasil dikirim ke Admin.');
    }

    // --- HELPER POIN & LEVEL ---
    private function addPoints($user, $amount)
    {
        if(!$user) return;
        $user->increment('points', $amount);
        
        $p = $user->points;
        $newLevel = 'Maba';
        if ($p >= 8000) $newLevel = 'Artefak';
        elseif ($p >= 2500) $newLevel = 'Calon';
        elseif ($p >= 500) $newLevel = 'Aktif';

        if ($user->current_level !== $newLevel) {
            $user->update(['current_level' => $newLevel]);
        }
    }

    public function render()
    {
        $request = RequestModel::with(['user', 'answers.material', 'answers.user'])->findOrFail($this->requestId);
        return view('livewire.forum.request-show', ['request' => $request]);
    }

    public function deleteCurrentRequest()
    {
        $request = RequestModel::findOrFail($this->requestId);
        $user = Auth::user();

        if ($request->user_id === $user->user_id || $user->isAdmin()) {
            if ($request->attachment_file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($request->attachment_file);
            }
            $request->delete();
            return redirect()->route('forum.index')->with('message', 'Konten berhasil dihapus.');
        }
        abort(403);
    }

    public function deleteInteraction($interactionId)
    {
        $interaction = Interaction::findOrFail($interactionId);
        $user = Auth::user();

        if ($interaction->user_id === $user->user_id || $user->isAdmin()) {
            
            // Jika jawaban ini adalah yang 'Accepted', kembalikan status request ke Open
            if ($interaction->is_accepted_answer) {
                $interaction->request->update(['status' => 'Open']);
            }

            if ($interaction->material) {
                if ($interaction->material->file_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($interaction->material->file_path);
                }
                $interaction->material->delete();
            }

            $interaction->delete();
            session()->flash('message', 'Jawaban berhasil dihapus.');
        } else {
            abort(403);
        }
    }

    public $editingInteractionId = null;
    public $editingContent = '';

    public function editInteraction($id)
    {
        $interaction = Interaction::findOrFail($id);
        $this->editingInteractionId = $interaction->interaction_id;
        $this->editingContent = $interaction->content;
    }

    public function cancelEdit()
    {
        $this->editingInteractionId = null;
        $this->editingContent = '';
    }

    public function updateInteraction()
    {
        $interaction = Interaction::findOrFail($this->editingInteractionId);
        $user = Auth::user();
        if ($interaction->user_id === $user->user_id) {
            $this->validate(['editingContent' => 'required|min:5|max:1000']);
            $check = \App\Services\ContentModerator::check($this->editingContent);
            if ($check['is_toxic']) {
                $this->addError('editingContent', 'Komentar DITOLAK! Terdeteksi unsur: ' . $check['category']);
                return;
            }
            $interaction->update(['content' => $this->editingContent]);
            $this->cancelEdit();
            session()->flash('message', 'Jawaban berhasil diupdate.');
        } else {
            abort(403);
        }
    }
}