<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    //
       protected $casts = [
        'PARKOUTDATETIME' => 'datetime:Y-m-d H:i:s',
        'park_datetime' => 'datetime:Y-m-d H:i:s',
         'park_fee' => 'float'
    ];
        
        use SoftDeletes;


        protected $fillable = [
        'ticket_no',
        'uuid',
        'qr_code',
        'mode_of_payment',
        'days_parked',
        'hours_parked',
        'plate_no',
        'park_year',
        'park_month',
        'park_day',
        'park_hour',
        'park_minute',
        'park_second',
        'park_fee',
        'park_out_year',
        'park_out_month',
        'park_out_day',
        'park_out_hour',
        'park_out_minute',
        'park_out_second',
        'is_park_out',
        'total_minutes',
        'park_datetime',
        'park_out_datetime',
        'remarks',
        'cancelled',
        'cancelled_by',
        'cancelled_datetime',
        'slip_no',
        'park_in_by',
        'park_out_by',
        'created_by',
    ];
    
public function parkOutUser()
    {
        return $this->belongsTo(User::class, 'park_out_by');
    }
}
