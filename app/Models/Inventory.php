<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $primaryKey = 'item_id';

    const UPDATED_AT = null;

    protected $fillable = [
        'item_name',
        'item_category', // Pembetulan ejaan typo di sini
        'item_model',
        'item_status',
        'item_location',
        'user_id',
    ];

    /**
     * Setiap item inventori dimiliki oleh seorang User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}