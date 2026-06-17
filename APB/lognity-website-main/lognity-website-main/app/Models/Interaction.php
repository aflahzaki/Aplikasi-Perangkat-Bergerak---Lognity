<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'interaction_id';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id', 'request_id');
    }
    
    // app/Models/Interaction.php

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }
    // Helper untuk hitung upvote pada interaksi ini (jika interaksi ini adalah sebuah jawaban)
    // *Catatan: Karena skema Anda menggabung tabel, biasanya upvote itu melekat pada objectnya.
    // Jika user meng-upvote sebuah JAWABAN, kita butuh kolom 'parent_interaction_id' di tabel Anda 
    // atau logic terpisah.
    // TAPI, untuk MVP sesuai tabel Anda, kita asumsikan 'interactions' type 'Upvote'
    // akan me-refer ke request_id (upvote pertanyaan) atau material_id.
}