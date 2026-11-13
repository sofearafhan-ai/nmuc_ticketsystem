<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications'; 
    protected $primaryKey = 'notification_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'notification_type',
        'notification_title',
        'notification_message',
        'notification_link',
        'is_read',
        'created_at',
    ];
}