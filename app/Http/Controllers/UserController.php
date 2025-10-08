<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * 📌 Show all users
     */
    public function index()
    {
        $users = User::with('shift')->whereNull('deleted_at')->get();
        $shifts = Shift::select('id', 'name')->where('is_active', true)->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'shifts' => $shifts, // ✅ so the frontend can show a dropdown
        ]);
    }

    /**
     * ➕ Store new user (with optional shift assignment)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:255|unique:users,username',
            'password' => 'required|min:3',
            'role' => 'required|integer',
            'contact' => 'nullable|string',
            'shift_id' => 'nullable|exists:shifts,id', // ✅ allow assigning shift on creation
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'contact' => $request->contact,
            'shift_id' => $request->shift_id, // ✅ assign shift if provided
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * 🔁 Update user (and re-assign shift if needed)
     */
    public function update(User $user, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'role' => 'required|integer',
            'contact' => 'nullable|string',
            'shift_id' => 'nullable|exists:shifts,id', // ✅ shift reassignment
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
            'contact' => $request->contact,
            'shift_id' => $request->shift_id, // ✅ update shift
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * 🔁 Reset password
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:3|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('users.index')->with('success', "Password for {$user->username} has been updated.");
    }

    public function showResetForm(User $user)
    {
        return inertia('Users/ResetPassword', [
            'user' => $user,
        ]);
    }

    /**
     * 🗑️ Soft delete a user
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
