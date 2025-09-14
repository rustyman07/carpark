<?php

namespace App\Models;
use App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    protected $fillable = [ 
     'payment_id',
    'card_id',
    'card_name',
    'card_number',
    'no_of_days',
    'discount',
    'balance',
    'amount',

    ];

    public function payment()
{
    return $this->belongsTo(Payment::class,'payment_id');
}
}

