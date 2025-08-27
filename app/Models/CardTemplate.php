<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardTemplate extends Model
{
    //
     protected $fillable = [
        'CARDNAME',
        'NOOFDAYS',
        'DISCOUNT',
        'PRICE',
        'AMOUNT',
        'CANCELLED',
        'CANCELLEDBY',
        'CANCELLEDDATETIME',
        'CREATEDBY',
     ];
}
