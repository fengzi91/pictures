<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;

class UpdatePasswordController extends Controller
{

    public function update(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [], [
            'old_password' => '旧密码',
            'password' => '新密码'
        ]);
        if (!$this->checkOldPassword($request->input('old_password'))) {
            throw ValidationException::withMessages([
                'old_password' => '旧密码不正确'
            ]);
        }

        $this->saveNewPassword($request->password);
        return response('', 204);
    }

    protected function checkOldPassword($password)
    {
        if (Hash::check($password, Auth::user()->makeVisible('password')->password)) {
            return true;
        }
        return false;

    }

    protected function saveNewPassword($password)
    {
        Auth::user()->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ])->save();
    }
}
