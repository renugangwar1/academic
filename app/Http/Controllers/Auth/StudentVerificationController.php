<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StudentVerificationController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function notice()
    {
        return view('auth.verify-student'); // Ensure you have this view
    }
        
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('student.dashboard');
    }

    public function resend(Request $request)
    {
        $check = Student::where('rollnumber',Auth::guard('student')->user()->rollnumber)->first();
        
        if($check->email_verified_at === null){
            $validator = Validator::make($request->all(), [
                'verify_email' => ['required', 'string', 'email', 'max:255', 'unique:students,email'],
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            Student::where('rollnumber',Auth::guard('student')->user()->rollnumber)->update(['email'=>$request->verify_email]);
        }else{
           return redirect()->route('student.dashboard');
        }

        $user = Student::where('rollnumber',Auth::guard('student')->user()->rollnumber)->first();
        
        $user->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    }
}

