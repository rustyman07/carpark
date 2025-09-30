<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
        public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:255|unique:'.User::class,
            'password' => 'required |min:3',
            'role'    =>  'required|integer',
          'contact'  => 'nullable|string',
           //'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
            'contact'  => $request->contact,

        ]);

        //  Auth::login($user);
return redirect()->route('users.index')->with('success', 'User has been created successfully.');


    }

    public function index(){

        $users = User::whereNull('deleted_at')->get();

        return Inertia('Users/Index',[
            'users' => $users,
          
        ]);


    }
    

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
public function update(User $user, Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => [
            'required',
            'string',
            'min:3',
            'max:255',
            Rule::unique(User::class)->ignore($user->id), // important for updates
        ],
        'role' => 'required|integer',
        'contact' => 'nullable|integer',
    ]);

    $user->update([
        'name' => $request->name,
        'username' => $request->username,
        'role' => $request->role,
        'contact' => $request->contact,
        'password' => Hash::make($request->password),
    ]);

    return redirect()->route('users.index')->with('success', 'User updated successfully.');
}


public function destroy(User $user): RedirectResponse
{
    $user->delete();
    return redirect()->route('users.index')->with('success', 'User deleted successfully.');
}


}
