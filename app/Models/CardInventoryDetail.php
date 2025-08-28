<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardInventoryDetail extends Model
{
        protected $fillable = [
        'HEADERID',
        'CARDNAME',
        'QRCODE',
        'NO_OF_DAYS',
        'DISCOUNT',
        'BALANCE',
        'PRICE',
        'AMOUNT',
        'CANCELLED',
        'CANCELLEDBY',
        'CANCELLEDDATETIME',
        'CREATEDBY',
     ];
}
