<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardInventory extends Model
{
     protected $fillable = [
    'card_template_id',
    'card_name',
    'no_of_days',
    'discount',
    'price',
    'amount',
    'cancelled',
    'cancelled_by',
    'cancelled_datetime',
    'created_by',
];

}
