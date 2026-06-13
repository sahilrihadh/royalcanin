<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'webinar_session_id', 'certificate_code', 'issued_at', 'file_path'
    ];

    protected $casts = [
        'issued_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function webinarSession()
    {
        return $this->belongsTo(WebinarSession::class);
    }

    // Generate unique certificate code
    public static function generateCode($userId, $sessionId)
    {
        return 'CERT-' . strtoupper(uniqid()) . '-' . $userId . '-' . $sessionId;
    }
}