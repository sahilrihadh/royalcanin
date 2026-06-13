<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'question', 'is_active', 'expires_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime'
    ];

    public function webinarSession()
    {
        return $this->belongsTo(WebinarSession::class);
    }

    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    // Get vote count for each option with percentage
    public function getResultsAttribute()
    {
        $totalVotes = $this->votes()->count();
        
        return $this->options->map(function($option) use ($totalVotes) {
            $percentage = $totalVotes > 0 ? round(($option->vote_count / $totalVotes) * 100, 1) : 0;
            return [
                'id' => $option->id,
                'text' => $option->option_text,
                'votes' => $option->vote_count,
                'percentage' => $percentage
            ];
        });
    }
}