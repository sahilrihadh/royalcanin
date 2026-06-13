<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id', 'option_text', 'vote_count', 'is_correct'
    ];

    protected $casts = [
        'vote_count' => 'integer',
        'is_correct' => 'boolean'
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }
}