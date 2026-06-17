<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\Request as RequestModel;
use App\Models\Interaction;
use App\Models\User;
use App\Services\SmartSearch;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class RequestIndex extends Component
{
    use WithPagination;
    use WithFileUploads;

    // --- FORM CREATE VARIABLE ---
    public $description, $attachment;
    // Variable baru untuk Create
    public $faculty, $course_name, $category = 'Lain-Lain';

    // --- FILTER VARIABLE ---
    public $search = '';
    public $filterCategory = '';
    public $filterFaculty = '';
    public $sort = 'latest'; // latest, popular, oldest

    // Reset pagination jika user melakukan search/filter
    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterCategory() { $this->resetPage(); }
    public function updatedSort() { $this->resetPage(); }

    protected $rules = [
        'description' => 'required|min:10|max:500',
        'category'    => 'required',
        'faculty'     => 'nullable|string|max:50',
        'course_name' => 'nullable|string|max:50',
        'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:5120',
    ];

    // --- FUNGSI CREATE REQUEST ---
    public function createRequest()
    {
        if (Auth::user()->hasReachedLimit('request')) {
            $this->addError('description', 'Batas request harian Anda sudah habis. Naikkan level untuk kuota lebih!');
            return;
        }
        $this->validate();

        $filePath = $this->attachment ? $this->attachment->store('requests_attachments', 'public') : null;
        $check = \App\Services\ContentModerator::check($this->description);

        if ($check['is_toxic']) {
            // EKSEKUSI HUKUMAN
            $user = Auth::user();
            $user->decrement('points', 25); // Kurangi 25 poin langsung

            // Tampilkan Pesan Error Spesifik
            // $check['category'] akan berisi 'sexual', 'hate', dll sesuai deteksi AI
            $this->addError('description', 'Konten DITOLAK! Terdeteksi unsur: ' . $check['category'] . '. Poin Anda dikurangi 25 sebagai sanksi.');
            
            return; // STOP: Jangan simpan ke database
        }

        RequestModel::create([
            'user_id' => Auth::id(),
            'description' => $this->description,
            'attachment_file' => $filePath,
            'status' => 'Open',
            // Data Baru
            'faculty' => $this->faculty,
            'course_name' => $this->course_name,
            'category' => $this->category,
        ]);

        // Reset Form
        $this->reset(['description', 'attachment', 'faculty', 'course_name', 'category']);
        session()->flash('message', 'Request berhasil dibuat!');
    }

    // --- FUNGSI UPVOTE ---
    public function toggleUpvote($requestId)
    {
        $user = Auth::user();
        if (!$user->canUpvote()) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Level Mahasiswa Baru belum bisa Upvote!']);
            return;
        }
        
        // 1. Cek Permission (Mahasiswa Aktif / Poin >= 500)
        if ($user->points < 500) {
            // Kita bisa kirim notifikasi error / alert
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Minimal 500 Poin (Mahasiswa Aktif) untuk Upvote!']);
            return;
        }

        $request = RequestModel::find($requestId);
        if (!$request) return;

        // 2. Cek apakah sudah pernah upvote (Mencegah spam like)
        $existingUpvote = Interaction::where('user_id', $user->user_id)
            ->where('request_id', $requestId)
            ->where('type', 'Upvote')
            ->first();

        if ($existingUpvote) {
            // UN-VOTE (Hapus like)
            $existingUpvote->delete();
            $request->decrement('upvotes_count');
        } else {
            // UPVOTE BARU
            Interaction::create([
                'user_id' => $user->user_id,
                'request_id' => $requestId,
                'type' => 'Upvote'
            ]);
            $request->increment('upvotes_count');

            // Opsional: Beri poin ke pemilik request (+5 Poin)
            $owner = User::find($request->user_id);
            if($owner && $owner->user_id !== $user->user_id) {
                $owner->increment('points', 5);
            }
        }
    }

    public function render()
    {
        // Query Dasar (Relasi)
        $query = RequestModel::with('user')->withCount('answers');

        // Filter Kategori (SQL Biasa)
        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }
        if ($this->filterFaculty) {
            $query->where('faculty', $this->filterFaculty);
        }

        // Sorting (SQL Biasa)
        if ($this->sort === 'popular') {
            $query->orderBy('upvotes_count', 'desc');
        } elseif ($this->sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            // Default latest, tapi nanti SmartSearch akan mengurutkan ulang berdasarkan relevansi jika ada search
            $query->orderBy('created_at', 'desc');
        }

        // --- PENCARIAN PINTAR (SMART SEARCH) ---
        if ($this->search) {
            // Jika user mengetik sesuatu, gunakan SmartSearch
            // Cari di kolom 'description', 'course_name', dan 'category'
            $requests = SmartSearch::search($query, ['description', 'course_name', 'category'], $this->search);
        } else {
            // Jika tidak mencari, gunakan pagination biasa
            $requests = $query->paginate(10);
        }

        return view('livewire.forum.request-index', [
            'requests' => $requests
        ]);
    }

    public function deleteRequest($requestId)
    {
        $request = RequestModel::findOrFail($requestId);
        $user = Auth::user();

        // IZIN: Pemilik Konten ATAU Admin/Superadmin
        if ($request->user_id === $user->user_id || in_array($user->role, ['Admin', 'Superadmin'])) {
            
            // Hapus file lampiran jika ada
            if ($request->attachment_file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($request->attachment_file);
            }
            
            $request->delete();
            session()->flash('message', 'Request berhasil dihapus.');
        } else {
            abort(403, 'Anda tidak memiliki izin menghapus request ini.');
        }
    }
}