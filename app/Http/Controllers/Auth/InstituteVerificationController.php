<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InstituteVerificationController extends Controller
{
    public function notice()
    {
        return view('auth.verifyinstitute'); // Ensure you have this view
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('institute.dashboard');
    }

    public function resend(Request $request)
    {
        $request->user('institute')->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    }
}
