<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CardTemplate extends Model
{
    //
   use SoftDeletes;

    protected $fillable = [
    'card_name',
    'no_of_days',
    'status',
    'discount',
    'price',
    'amount',
    'total_amount',
    'change',
    'cancelled',
    'cancelled_by',
    'cancelled_datetime',
    'created_by',
];



}
