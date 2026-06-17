<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Request extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';
    
    // Pastikan semua kolom baru dan lama ada disini
    protected $fillable = [
        'user_id', 
        'description', 
        'attachment_file', 
        'status',
        'faculty', 
        'course_name', 
        'semester', 
        'academic_year', 
        'category', 
        'upvotes_count'
    ];

    protected $appends = ['attachment_url'];

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_file ? asset('storage/' . $this->attachment_file) : null;
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke SEMUA Interaksi (Upvote, Comment, Answer)
    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'request_id', 'request_id');
    }

    // Relasi KHUSUS untuk Jawaban saja (Filter type = Answer)
    public function answers()
    {
        return $this->hasMany(Interaction::class, 'request_id', 'request_id')
                    ->where('type', 'Answer');
    }

    // Helper: Cek apakah user yang login sudah like request ini?
    public function getIsUpvotedAttribute()
    {
        if (!Auth::check()) return false;
        
        return $this->interactions()
                    ->where('user_id', Auth::id())
                    ->where('type', 'Upvote')
                    ->exists();
    }
}