<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'full_name',
        'mobile_number',
        'email_id',
        'clinic_name',
        'registration_number',
        'city',
        'state',
        'city_pincode',
        'registered_at',
        'last_seen_at',
        'is_online',
        'is_admin',
        'terms_accepted',
        'sale_consent',
        'research_consent',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'is_online' => 'boolean',
        'is_admin' => 'boolean',
        'terms_accepted' => 'boolean',
        'sale_consent' => 'boolean',
        'research_consent' => 'boolean',
    ];

    // Authentication using email_id
    public function getAuthIdentifierName()
    {
        return 'email_id';
    }

    public function getAuthPassword()
    {
        return null;
    }

    // Relationships
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function pollVotes()
    {
        return $this->hasMany(PollVote::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function userSessions()
    {
        return $this->hasMany(UserSession::class);
    }

    // Update last seen timestamp
    public function updateLastSeen()
    {
        $this->update([
            'last_seen_at' => now(),
            'is_online' => true
        ]);
    }

    // Mark user as offline
    public function markOffline()
    {
        $this->update([
            'is_online' => false
        ]);
    }

    // Check if user is online (last seen within last 5 minutes)
    public function isOnline()
    {
        return $this->is_online && $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }

    // Get online users count
    public static function getOnlineCount()
    {
        return static::where('is_online', true)
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->count();
    }

    // Relationship with reactions
    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

}
