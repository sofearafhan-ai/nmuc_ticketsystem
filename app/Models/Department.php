<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'department_id';

    protected $fillable = ['department_name', 'code'];

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'department_id', 'department_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'department_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'department_id', 'department_id');
    }
}