<?php

namespace App\Http\Controllers;

use App\Models\ShiftLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftLogController extends Controller
{
    public function startShift(Request $request)
    {
       $user = auth()->user();

    // ✅ Check if user already has an active shift
    $activeShift = ShiftLog::where('user_id', $user->id)
        ->whereNull('ended_at')
        ->first();

    if ($activeShift) {
        return back()->with('error', 'You already have an active shift.');
    }

    // ✅ Check if they already finished a shift today
    $todayShift = ShiftLog::where('user_id', $user->id)
        ->whereDate('started_at', now()->toDateString())
        ->whereNotNull('ended_at')
        ->exists();

    if ($todayShift) {
        return back()->with('error', 'You have already completed a shift today.');
    }

    // ✅ Create new shift
    ShiftLog::create([
        'user_id' => $user->id,
        'started_at' => now(),
        //'shift_name' => $this->getShiftName(now()), // optional helper
    ]);

    return back()->with('success', 'Shift started successfully!');
    }

    public function endShift(Request $request)
    {
        $user = Auth::user();

        // ✅ 3. Only end if an active shift exists
        $activeShift = ShiftLog::where('user_id', $user->id)
            ->whereNull('ended_at')
            ->latest()
            ->first();

        if (!$activeShift) {
            return back()->with('error', 'No active shift found to end.');
        }

        $activeShift->update([
            'ended_at' => now(),
        ]);

        return back()->with('success', 'Shift ended successfully!');
    }

    /**
     * 🔄 Optional: detect shift name based on time
     */
    private function detectShiftName()
    {
        $now = now()->format('H:i:s');

        if ($now >= '06:00:00' && $now <= '14:00:00') {
            return 'MORNING';
        } elseif ($now > '14:00:00' && $now <= '22:00:00') {
            return 'AFTERNOON';
        } else {
            return 'NIGHT';
        }
    }
}
