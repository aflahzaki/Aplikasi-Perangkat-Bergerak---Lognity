<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'user_id');
    }
}