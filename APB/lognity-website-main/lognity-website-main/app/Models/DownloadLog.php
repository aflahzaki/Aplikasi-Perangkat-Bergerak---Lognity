<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadLog extends Model {
    public $timestamps = false; // Kita atur manual pakai user current ler
    protected $fillable = ['user_id', 'material_id', 'downloaded_at'];
}
