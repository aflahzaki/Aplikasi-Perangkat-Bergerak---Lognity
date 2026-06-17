<?php

namespace App\Livewire\Library;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\Ebook;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class EbookCreate extends Component
{
    use WithFileUploads;

    public $title, $author, $description, $category = 'Umum';
    public $file, $cover;

    public function mount()
    {
        // Proteksi: Hanya Admin/Superadmin
        if (!in_array(Auth::user()->role, ['Admin', 'Superadmin'])) {
            abort(403, 'Hanya Pustakawan (Admin) yang boleh masuk sini.');
        }
    }

    protected $rules = [
        'title' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'category' => 'required',
        'file' => 'required|file|mimes:pdf,epub,zip|max:51200', // Max 50MB
        'cover' => 'nullable|image|max:2048', // Max 2MB
    ];

    public function store()
    {
        $this->validate();

        $filePath = $this->file->store('ebooks/files', 'public');
        $coverPath = $this->cover ? $this->cover->store('ebooks/covers', 'public') : null;

        Ebook::create([
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
            'category' => $this->category,
            'file_path' => $filePath,
            'cover_path' => $coverPath,
            'uploaded_by' => Auth::id(),
        ]);

        session()->flash('message', 'E-book berhasil ditambahkan ke perpustakaan.');
        return redirect()->route('library.index');
    }

    public function render()
    {
        return view('livewire.library.ebook-create');
    }
}