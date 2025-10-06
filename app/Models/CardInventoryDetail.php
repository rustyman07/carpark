<?php

namespace App\Models;
use App\Models\CardInventory;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class CardInventoryDetail extends Model
{

     use SoftDeletes;

       protected $fillable = [
    'card_inventory_id',
    'card_template_id',
    'card_name',
    'card_number',
    'qr_code_hash',
    'no_of_days',
    'discount',
    'balance',
    'price',
    'status',
    'amount',
    'cancelled',
    'cancelled_by',
    'cancelled_datetime',
    'created_by',
];


public function cardInventory(){

    return $this->belongsTo(CardInventory::class);
}



}
