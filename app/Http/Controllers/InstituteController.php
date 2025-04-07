<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Institute;
use App\Models\Notification;
use App\Models\Subject;
use App\Models\Course;
use App\Models\ExcelLog;
use Illuminate\Support\Facades\DB;

class InstituteController extends Controller
{
    protected $institute;

    public function __construct()
    {
        $this->middleware(['auth:institute', 'verified']); // Ensure user is authenticated and email is verified
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notification = Notification::where('Nfor','institute')->where('Nto_date','>=',date('Y-m-d'))->get();
        $exellog = ExcelLog::where('UserName',Auth::guard('institute')->user()->InstituteName)->take(5)->get();
        return view('institute.dashboard',compact('notification','exellog'));
    }
}
