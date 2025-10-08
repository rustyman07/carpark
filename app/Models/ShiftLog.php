<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_id',
        'started_at',
        'ended_at',
        'is_closed',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'is_closed'  => 'boolean',
    ];

    /**
     * ✅ User who worked this shift
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ Planned shift this log belongs to
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
