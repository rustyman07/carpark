<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardInventoryDetail extends Model
{
       protected $fillable = [
    'header_id',
    'card_name',
    'qr_code',
    'no_of_days',
    'discount',
    'balance',
    'price',
    'amount',
    'cancelled',
    'cancelled_by',
    'cancelled_datetime',
    'created_by',
];

}
