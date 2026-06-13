<?php
// app/Models/Reaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reaction_type',
        'session_id',
        'ip_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for today's reactions
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Scope for specific reaction type
    public function scopeOfType($query, $type)
    {
        return $query->where('reaction_type', $type);
    }
}