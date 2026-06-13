<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'webinar_session_id', 'started_watching_at', 'completed_watching_at', 'certificate_sent'
    ];

    protected $casts = [
        'started_watching_at' => 'datetime',
        'completed_watching_at' => 'datetime',
        'certificate_sent' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function webinarSession()
    {
        return $this->belongsTo(WebinarSession::class);
    }

    // Mark video as started
    public function markStarted()
    {
        $this->update(['started_watching_at' => now()]);
    }

    // Mark video as completed and trigger certificate
    public function markCompleted()
    {
        $this->update(['completed_watching_at' => now()]);
        return $this;
    }
}