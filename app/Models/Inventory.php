<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'item_id';
    const UPDATED_AT = null; 

    protected $fillable = [
        'item_name',
        'serial_number',
        'item_manufacturer',
        'item_type',
        'item_model',
        'item_status',
        'item_location',
        'item_attachment',
        'department_id',
        'user_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}