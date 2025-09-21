<?php

namespace App\Models;
use App\Models\CardInventoryDetail;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class CardInventory extends Model
{
    use SoftDeletes;

     protected $fillable = [
    'card_template_id',
    'card_name',
    'no_of_days',
    'discount',
    'price',
    'amount',
    'cancelled',
    'cancelled_by',
    'cancelled_datetime',
    'created_by',
];


public function details(){

    return $this->hasMany(CardInventoryDetail::class);
}




}
