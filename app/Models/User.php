<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';

    const UPDATED_AT = null;

    protected $fillable = [
        'full_name',
        'nmuc_id',
        'email',
        'password',
        'role',
        'department_id',
        'user_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id', 'user_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'user_id', 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }
}