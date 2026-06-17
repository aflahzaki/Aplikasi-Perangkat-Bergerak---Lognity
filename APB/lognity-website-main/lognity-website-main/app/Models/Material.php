<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $primaryKey = 'material_id';
    protected $guarded = [];

    // Relasi ke Pengupload
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id', 'user_id');
    }

    // Relasi ke Request (Jika materi ini adalah balasan request)
    public function relatedRequest()
    {
        return $this->belongsTo(Request::class, 'related_request_id', 'request_id');
    }

    // Relasi ke Interaksi (Komentar/Upvote pada materi ini)
    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'material_id', 'material_id');
    }
    
    // Helper format ukuran file (Opsional)
    public function getFileSizeAttribute()
    {
        // Logika konversi bytes ke KB/MB bisa ditaruh di sini nanti
    }
}