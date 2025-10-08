<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * 📜 Show all shifts with assigned users
     */
    public function index()
    {
        $shifts = Shift::with('users')->orderBy('start_time')->get();
        $users = User::select('id', 'name')->orderBy('name')->get();

        return inertia('Shifts/Index', [
            'shifts' => $shifts,
            'availableUsers' => $users,
        ]);
    }

    /**
     * 🧱 Store a new shift
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'is_active' => 'boolean',
            'user_ids' => 'array', // optional user assignments
            'user_ids.*' => 'exists:users,id',
        ]);

        // create the shift
        $shift = Shift::create($validated);

        // ✅ Assign users to this shift (update their shift_id)
        if (!empty($validated['user_ids'])) {
            User::whereIn('id', $validated['user_ids'])->update(['shift_id' => $shift->id]);
        }

        return back()->with('success', 'Shift created successfully.');
    }

    /**
     * 🔁 Update an existing shift
     */
    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name,' . $shift->id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'is_active' => 'boolean',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // update shift details
        $shift->update($validated);

        // ✅ Unassign all current users from this shift first
        User::where('shift_id', $shift->id)->update(['shift_id' => null]);

        // ✅ Then assign new users (if any)
        if (!empty($validated['user_ids'])) {
            User::whereIn('id', $validated['user_ids'])->update(['shift_id' => $shift->id]);
        }

        return back()->with('success', 'Shift updated successfully.');
    }

    /**
     * 🗑️ Delete a shift
     */
    public function destroy(Shift $shift)
    {
        // ✅ Optional: unassign users before deleting shift
        User::where('shift_id', $shift->id)->update(['shift_id' => null]);

        $shift->delete();

        return back()->with('success', 'Shift deleted successfully.');
    }
}
