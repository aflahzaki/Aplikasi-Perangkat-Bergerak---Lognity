<?php

namespace App\Livewire\Library;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Ebook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class EbookIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCategory = '';

    // Hapus Buku (Khusus Admin)
    public function deleteEbook($id)
    {
        if (!in_array(Auth::user()->role, ['Admin', 'Superadmin'])) abort(403);

        $ebook = Ebook::findOrFail($id);
        
        // Hapus file fisik
        if ($ebook->file_path) Storage::disk('public')->delete($ebook->file_path);
        if ($ebook->cover_path) Storage::disk('public')->delete($ebook->cover_path);
        
        $ebook->delete();
        session()->flash('message', 'Buku berhasil dihapus.');
    }

    public function render()
    {
        $query = Ebook::query();

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('author', 'like', '%' . $this->search . '%');
        }

        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        return view('livewire.library.ebook-index', [
            'ebooks' => $query->latest()->paginate(8)
        ]);
    }
}