<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username', 'email', 'password', 'role', 
        'points', 'current_level', 'profil', 
        'is_banned', 'ban_expiration','last_ip_address'
    ];

    protected $appends = ['profil_url'];

    public function getProfilUrlAttribute()
    {
        if ($this->profil && $this->profil !== 'ppdefault.png') {
            return asset('storage/' . $this->profil);
        }
        
        $name = urlencode($this->username ?? 'User');
        return "https://ui-avatars.com/api/?name={$name}&background=random&color=fff";
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin()
    {
        return in_array($this->role, ['Admin', 'Superadmin']);
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'user_id', 'user_id');
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'user_id', 'user_id');
    }

    public function hasPermissionToUpvote()
    {
        // Upvote butuh level Mahasiswa Aktif (500-2499) atau lebih
        return $this->points >= 500;
    }
    
    public function materials()
    {
        return $this->hasMany(Material::class, 'uploader_id', 'user_id');
    }

    public function downloads()
    {
        return $this->hasMany(DownloadLog::class, 'user_id', 'user_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id', 'user_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id', 'user_id');
    }

    const LIMITS = [
        'Maba'    => ['request' => 3, 'interaction' => 10, 'upload' => 3,    'download' => 5,    'upvote' => true],
        'Aktif'   => ['request' => 10, 'interaction' => 30, 'upload' => 10,  'download' => 15,   'upvote' => true],
        'Calon'   => ['request' => 25, 'interaction' => 50, 'upload' => 25,  'download' => 30,   'upvote' => true],
        'Artefak' => ['request' => 9999, 'interaction' => 9999, 'upload' => 9999, 'download' => 9999, 'upvote' => true],
    ];

    public function hasReachedLimit($type)
    {
        // 1. Ambil config level user saat ini
        $config = self::LIMITS[$this->current_level] ?? self::LIMITS['Maba'];
        $limit = $config[$type];

        // Jika Artefak (Unlimited)
        if ($limit >= 9999) return false;

        // 2. Hitung jumlah aktivitas hari ini
        $todayCount = 0;

        switch ($type) {
            case 'request':
                $todayCount = $this->requests()->whereDate('created_at', Carbon::today())->count();
                break;
            case 'interaction':
                $todayCount = $this->interactions()->whereDate('created_at', Carbon::today())->count();
                break;
            case 'upload':
                // Asumsi relasi materials ada di User model
                $todayCount = $this->materials()->whereDate('created_at', Carbon::today())->count();
                break;
            case 'download':
                $todayCount = $this->downloads()->whereDate('downloaded_at', Carbon::today())->count();
                break;
        }

        // 3. Bandingkan
        return $todayCount >= $limit;
    }

    public function canUpvote()
    {
        $config = self::LIMITS[$this->current_level] ?? self::LIMITS['Maba'];
        return $config['upvote'];
    }

    // Helper untuk ambil sisa kuota (Opsional, buat ditampilkan di UI)
    public function getRemainingQuota($type)
    {
        $config = self::LIMITS[$this->current_level] ?? self::LIMITS['Maba'];
        $limit = $config[$type];
        if ($limit >= 9999) return '∞';

        $todayCount = 0;
    }

    public function getLevelProgressAttribute()
    {
        $points = $this->points;
        
        // Target poin berdasarkan level saat ini
        if ($points < 500) { // Maba -> Aktif (Target 500)
            return ($points / 500) * 100;
        } elseif ($points < 2500) { // Aktif -> Calon (Target 2500)
            return (($points - 500) / (2500 - 500)) * 100;
        } elseif ($points < 8000) { // Calon -> Artefak (Target 8000)
            return (($points - 2500) / (8000 - 2500)) * 100;
        } else {
            return 100; // Artefak (Max)
        }
    }

    // HELPER 3: Poin Menuju Level Berikutnya
    public function getPointsToNextLevelAttribute()
    {
        $points = $this->points;
        if ($points < 500) return 500 - $points;
        if ($points < 2500) return 2500 - $points;
        if ($points < 8000) return 8000 - $points;
        return 0; // Max level
    }

    public function receivedUpvotes()
    {
        // Menghitung upvote dari Request milik user ini
        // Note: Ini agak kompleks query-nya, untuk MVP kita hitung manual via Requests saja
        return $this->requests()->sum('upvotes_count');
    }

    public function getBadgeProgressListAttribute()
    {
        // 1. Ambil Data Statistik User Sekali Saja (Optimasi Query)
        $xp = $this->points;
        $reqCount = $this->requests()->count();
        $ansCount = $this->interactions()->where('type', 'Answer')->count();
        $matCount = $this->materials()->count();
        
        // Hitung Accepted Answer
        $acceptedCount = $this->interactions()
            ->where('type', 'Answer')
            ->where('is_accepted_answer', true)
            ->count();

        // Hitung Popularitas (Total Download dari semua materi user)
        $totalDownloads = $this->materials()->sum('download_count');

        // DAFTAR BADGE (150+) diambil dari config lognity
        $definitions = config('lognity.badges', []);

        // Proses Kalkulasi Status
        $result = [];
        foreach ($definitions as $def) {
            $current = 0;
            switch ($def['metric']) {
                case 'xp': $current = $xp; break;
                case 'request': $current = $reqCount; break;
                case 'answer': $current = $ansCount; break;
                case 'accepted': $current = $acceptedCount; break;
                case 'upload': $current = $matCount; break;
                case 'download': $current = $totalDownloads; break;
            }

            $isUnlocked = $current >= $def['target'];
            
            // Hitung persentase (max 100%)
            $percent = 0;
            if ($def['target'] > 0) {
                $percent = ($current / $def['target']) * 100;
            } else {
                $percent = 100; // Target 0 langsung 100%
            }
            
            $result[] = [
                'name' => $def['name'],
                'icon' => $def['icon'],
                'desc' => $def['desc'],
                'target' => $def['target'],
                'current' => $current,
                'percent' => min($percent, 100),
                'unlocked' => $isUnlocked,
                'color' => $isUnlocked ? $def['color'] : 'bg-gray-100 text-gray-400 grayscale opacity-50',
                'raw_color' => $isUnlocked ? ($def['raw_color'] ?? 'blue') : 'gray',
            ];
        }

        return $result;
    }

    // Helper sederhana untuk menampilkan badge yang SUDAH didapat saja (Versi ringkas)
    public function getUnlockedBadgesAttribute()
    {
        return array_filter($this->badge_progress_list, function($badge) {
            return $badge['unlocked'];
        });
    }
}
