<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'login_time',
        'logout_time'
    ];

    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calculate duration in minutes
    public function getDurationAttribute()
    {
        if ($this->login_time && $this->logout_time) {
            return $this->login_time->diffInMinutes($this->logout_time);
        }
        return 0;
    }

    // Get active sessions (logged in but not logged out in last 5 minutes)
    public static function getActiveSessions()
    {
        return self::whereNull('logout_time')
            ->where('login_time', '>=', now()->subMinutes(5))
            ->count();
    }

    // Get total unique users for a specific date
    public static function getUniqueUsersByDate($date)
    {
        return self::whereDate('login_time', $date)
            ->distinct('user_id')
            ->count('user_id');
    }
}