<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'question_text', 
        'asked_at', 
        'is_answered', 
        'answer_text'
    ];

    protected $casts = [
        'asked_at' => 'datetime',
        'is_answered' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}