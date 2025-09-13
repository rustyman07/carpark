<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    protected $fillable = [ 
     'header_id',
    'card_id',
    'card_name',
    'card_number',
    'no_of_days',
    'discount',
    'balance',
    'amount',

    ];
}
