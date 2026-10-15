<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory;

    protected $primaryKey = 'reply_id';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'sender_name',
        'sender_role',
        'message',
        'attachment',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'ticket_id');
    }
}