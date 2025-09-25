<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
        public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:255|unique:'.User::class,
            'password' => ['required', Rules\Password::defaults()],
            'role'    =>  'required|integer',
            'contact'  => 'integer',
           //'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
            'contact'  => $request->contact,

        ]);



        // Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    public function index(){

        $users = User::whereNull('deleted_at')->get();

        return Inertia('Users/Index',[
            'users' => $users
        ]);


    }

}
