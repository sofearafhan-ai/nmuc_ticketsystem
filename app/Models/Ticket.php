<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $primaryKey = 'ticket_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'department_id',
        'title',
        'description',
        'type',
        'category',
        'priority',
        'location',
        'status',
        'pic',
        'date',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function picAdmin()
    {
        return $this->belongsTo(User::class, 'pic', 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id', 'ticket_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }
}