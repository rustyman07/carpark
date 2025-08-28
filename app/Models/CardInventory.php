<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardInventory extends Model
{
      protected $fillable = [
        'CARD_TEMPLATEID',
        'CARDNAME',
        'NO_OF_DAYS',
        'DISCOUNT',
        'PRICE',
        'AMOUNT',
        'CANCELLED',
        'CANCELLEDBY',
        'CANCELLEDDATETIME',
        'CREATEDBY',
     ];
}
