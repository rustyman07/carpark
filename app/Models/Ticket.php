<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    //

        protected $fillable = [
        'TICKETNO',
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
