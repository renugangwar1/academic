<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstituteLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:institute')->except('logout');
    }

    public function showloginForm(){
        if(Auth::user()){
            return redirect()->route('welcome');
        }else{
            return view('auth.institutelogin');
        }  
    }

    public function login(Request $request){
        // Validate the form data
        $this->validate($request, [
            'Iemail' => 'required|string',
            'password' => 'required|string',
        ]);
        
        // Attempt to log the user in
        if (Auth::guard('institute')->attempt(['email' => $request->Iemail, 'password' => $request->password], $request->remember)) {
            
            $institute = Auth::guard('institute')->user();

            if (!$institute->hasVerifiedEmail()) {
                // Auth::guard('institute')->logout();

                return redirect()->route('institute.verification.notice')->withErrors(['email' => 'You need to verify your email address.']);
            }
            // If successful, then redirect to their intended location
            return redirect()->intended(route('institute.dashboard'));
        }

        // If unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors([
            'email' => 'These credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('institute')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
