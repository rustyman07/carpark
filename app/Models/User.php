<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'contact',
        'shift_id', // ✅ add this so it's mass assignable
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * ✅ Relationship: User belongs to a shift
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * ✅ Relationship: User has many shift logs
     */
    public function shiftLogs()
    {
        return $this->hasMany(ShiftLog::class);
    }
}
