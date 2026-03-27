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
    'additional_hour_block',
    'additional_rate_per_block',
    'grace_minutes',
    'motor_rate',
    'motor_rate_perhour',
    'motor_rate_perday',
    'motor_hourly_billing_limit',
    'motor_grace_minutes',
    'motor_additional_hour_block',
    'motor_additional_rate_per_block',
    ];
}
