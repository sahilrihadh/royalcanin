<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreviousSession extends Model
{
    protected $table = 'previous_sessions';

    protected $fillable = [
        'name',
        'email_id',
        'session_name',
        'watched_on',
        'certificate_status',
        'certificate_path',
        'count'
    ];

    protected $casts = [
        'certificate_status' => 'boolean',
        'watched_on' => 'datetime'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'email_id', 'email_id');
    }
}
