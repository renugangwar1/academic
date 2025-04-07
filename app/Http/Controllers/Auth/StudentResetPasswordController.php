<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset-student')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Here we will attempt to reset the student's password.
        $status = Password::broker('students')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($student, $password) {
                $student->password = Hash::make($password);
                $student->setRememberToken(Str::random(60));
                $student->save();
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('student.login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}

