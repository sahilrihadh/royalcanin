<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'user_role',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Check if admin has specific role
    public function hasRole($role)
    {
        return $this->user_role === $role;
    }

    // Check if is super admin
    public function isSuperAdmin()
    {
        return $this->user_role === 'admin';
    }
}
