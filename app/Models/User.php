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
        'email',
        'password',
        'role',
        'department',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    // ==========================================
    // HUBUNGAN (RELATIONSHIPS)
    // ==========================================

    /**
     * Seorang User boleh mempunyai banyak Ticket.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id', 'user_id');
    }

    /**
     * Seorang User boleh mempunyai banyak Inventory.
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'user_id', 'user_id');
    }
}