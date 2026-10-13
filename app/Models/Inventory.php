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
        'iteam_category',
        'item_model',
        'item_status',
        'item_location',
        'user_id',
    ];
}
