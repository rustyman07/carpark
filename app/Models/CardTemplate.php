<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardTemplate extends Model
{
    //
    protected $fillable = [
    'card_name',
    'no_of_days',
    'status',
    'discount',
    'price',
    'amount',
    'cancelled',
    'cancelled_by',
    'cancelled_datetime',
    'created_by',
];

}
