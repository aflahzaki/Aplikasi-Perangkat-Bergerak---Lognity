<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_id';
    protected $guarded = [];

    // Relasi-relasi
    public function reporter() { return $this->belongsTo(User::class, 'reporter_id', 'user_id'); }
    public function targetUser() { return $this->belongsTo(User::class, 'target_user_id', 'user_id'); }
    public function targetRequest() { return $this->belongsTo(Request::class, 'target_request_id', 'request_id'); }
    public function targetInteraction() { return $this->belongsTo(Interaction::class, 'target_interaction_id', 'interaction_id'); }
    public function targetMaterial() { return $this->belongsTo(Material::class, 'target_material_id', 'material_id'); }

    // --- HELPER BARU: Mengambil Konten Secara Dinamis ---
    public function getTargetContentAttribute()
    {
        if ($this->target_request_id && $this->targetRequest) {
            return $this->targetRequest->description;
        }
        if ($this->target_interaction_id && $this->targetInteraction) {
            return $this->targetInteraction->content;
        }
        if ($this->target_material_id && $this->targetMaterial) {
            return "Judul Materi: " . $this->targetMaterial->title . " (Deskripsi: " . $this->targetMaterial->description . ")";
        }
        return "Konten tidak ditemukan / dihapus.";
    }

    // --- HELPER BARU: Mengambil Tipe Target ---
    public function getTargetTypeLabelAttribute()
    {
        if ($this->target_request_id) return 'Request Forum';
        if ($this->target_interaction_id) return 'Komentar/Jawaban';
        if ($this->target_material_id) return 'File Materi';
        return 'Unknown';
    }
}