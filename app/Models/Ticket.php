<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $primaryKey = 'ticket_id';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_name',
        'user_email',
        'title',
        'description',
        'image',
        'type',
        'category',
        'date',
        'status',
        'priority',
        'user_id',
        'location',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
