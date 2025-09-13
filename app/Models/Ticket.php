<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    //
       protected $casts = [
        'PARKOUTDATETIME' => 'datetime:Y-m-d H:i:s',
        'PARKDATETIME' => 'datetime:Y-m-d H:i:s',
         'PARKFEE' => 'float'
    ];
        
        use SoftDeletes;


        protected $fillable = [
        'TICKETNO',
        'uuid',
        'QRCODE',
        'mode_of_payment',
        'days_parked',
        'hours_parked',
        'PLATENO',
        'PARKYEAR',
        'PARKMONTH',
        'PARKDAY',
        'PARKHOUR',
        'PARKMINUTE',
        'PARKSECOND',
        'PARKFEE',
        'PARKOUTYEAR',
        'PARKOUTMONTH',
        'PARKOUTDAY',
        'PARKOUTHOUR',
        'PARKOUTMINUTE',
        'PARKOUTSECOND',
        'ISPARKOUT',
        'TOTALMINUTES',
        'PARKDATETIME',
        'PARKOUTDATETIME',
        'REMARKS',
        'CANCELLED',
        'CANCELLEDBY',
        'CANCELLEDDATETIME',
        'SLIPNO',
        'PARKINBY',
        'PARKOUTBY',
        'CREATEDBY',
    ];
}
