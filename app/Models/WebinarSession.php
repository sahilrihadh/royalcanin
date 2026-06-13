<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebinarSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
