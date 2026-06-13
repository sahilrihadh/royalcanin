<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcements';

    protected $fillable = [
        'title',
        'description',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope for active announcements
    public function scopeActive($query)
    {
        return $query->where('status', 'show');
    }

    // Check if announcement is visible
    public function isVisible()
    {
        return $this->status === 'show';
    }
}