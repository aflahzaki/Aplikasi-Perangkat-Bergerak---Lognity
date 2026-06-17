<?php

namespace App\Livewire\Material;

use Livewire\Component;
use Livewire\WithFileUploads; // Wajib untuk handling file
use Livewire\Attributes\Layout;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class MaterialCreate extends Component
{
    use WithFileUploads;

    public $title;
    public $description;
    public $file;
    public $tags;

    // Rules Validasi
    protected $rules = [
        'title' => 'required|min:5|max:100',
        'description' => 'required|min:10',
        'tags' => 'required',
        'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,jpg,jpeg,png,webp|max:10240', 
    ];

    public function upload()
    {
        if (Auth::user()->hasReachedLimit('upload')) {
            session()->flash('error', 'Kuota upload harian habis. Kembali besok!');
            return;
        }
        $this->validate();

        // 1. Simpan File ke Storage (Folder: public/materials)
        // Pastikan jalankan: php artisan storage:link
        $filePath = $this->file->store('materials', 'public');

        // 2. Simpan ke Database
        Material::create([
            'uploader_id' => Auth::id(),
            'title' => $this->title,
            'description' => $this->description,
            'file_path' => $filePath,
            'tags' => $this->tags,
            'download_count' => 0,
        ]);

        // 3. GAMIFIKASI: Tambah Poin (+20)
        $user = Auth::user();
        $user->increment('points', 20);

        // Cek Level Up (Logic sama seperti sebelumnya, bisa dipisah ke Service nanti)
        if ($user->points >= 8000) $user->update(['current_level' => 'Artefak']);
        elseif ($user->points >= 2500) $user->update(['current_level' => 'Calon']);
        elseif ($user->points >= 500) $user->update(['current_level' => 'Aktif']);

        // 4. Redirect / Reset
        session()->flash('message', 'Materi berhasil diupload! Kamu mendapatkan +20 Poin.');
        return redirect()->route('material.index');
    }

    public function render()
    {
        return view('livewire.material.material-create');
    }
}