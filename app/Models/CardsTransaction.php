<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardsTransaction extends Model
{
   protected $fillable = [
        'ticket_id',
        'ticket_no',
        'card_id',
        'card_name',
        'card_number',
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
