<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'ticket_id',
        'card_id',
        'qr_code',
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

    public function card()
    {
        return $this->belongsTo(CardInventoryDetail::class, 'card_id');
    }
}


