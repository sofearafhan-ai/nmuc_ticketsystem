<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'item_id';
    
    protected $fillable = [
        'user_id',
        'department_id',
        'item_name',
        'item_category',
        'item_model',
        'item_location',
        'item_status',
        'item_attachment',
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