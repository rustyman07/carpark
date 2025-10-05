<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Company extends Model
{
      use HasFactory;

     protected $fillable = [
    'name',
    'address',
    'contact',
    'rate',
    'rate_perhour',
    'rate_perday',
    'hourly_billing_limit',
    'grace_minutes'
    ];
}
