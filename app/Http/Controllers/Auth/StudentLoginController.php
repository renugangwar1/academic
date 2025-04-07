<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class StudentLoginController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest:student')->except('logout');
    }

    public function showLoginForm()
    {
        if(Auth::user()){
            return redirect()->route('welcome');
        }else{
            return view('auth.studentlogin');
        }
    }

    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'rollnumber' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to log the user in
        if (Auth::guard('student')->attempt(['rollnumber' => $request->rollnumber, 'password' => $request->password], $request->remember)) {
            
            $student = Auth::guard('student')->user();
            
            if (!$student->hasVerifiedEmail()) {
                // Auth::guard('student')->logout();

                return redirect()->route('student.verification.notice')->withErrors(['email' => 'You need to verify your email address.']);
            }
            // If successful, then redirect to their intended location
            return redirect()->intended(route('student.dashboard'));
        }

        // If unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($request->only('rollnumber', 'remember'))->withErrors([
            'rollnumber' => 'These credentials do not match our records.',
        ]);;
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
