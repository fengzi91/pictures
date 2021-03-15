<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->authenticate();
        $user = Auth::user();
        $token = $user->createToken('spa');
        return [
            'token' => $token->plainTextToken,
            'data' => $user
        ];
    }
}
