<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;
use App\Models\PaymentDetail;

class Payment extends Model
{
    protected $fillable = [
        'ticket_id',
        'amount',
        'days_deducted',
        'payment_type',
        'payment_method',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function details()
    {
        return $this->hasMany(PaymentDetail::class,'payment_id');
    }


}


