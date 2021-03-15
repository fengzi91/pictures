<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterUserController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:32',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        $token = $user->createToken('spa');
        return [
            'token' => $token->plainTextToken,
            'data' => $user
        ];
    }
}
