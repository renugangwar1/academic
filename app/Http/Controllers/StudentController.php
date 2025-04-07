<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ResultData;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\InstituteMaster;
use App\Models\Notification;
use App\Models\ReappearSetting;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\Course;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use basic;
use conmmandatacall;
use Eazypay;
use Illuminate\Support\Facades\Route;

class StudentController extends Controller
{
    protected $student;

    public function __construct()
    {
        $this->middleware(['auth:student', 'verified']); // Ensure user is authenticated and email is verified
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notification = Notification::where('Nfor','student')->where('Nto_date','>=',date('Y-m-d'))->get();
        $data = ResultData::with('course','institute')->where('Stud_nchm_roll_number',Auth::guard('student')->user()->rollnumber)->get();
        
        return view('student.dashboard',compact('notification','data'));
    }


    public function reappearform(){
        $subjectinfo = new conmmandatacall();
        $corse = $subjectinfo->course;
        $semester = $subjectinfo->semester;
        return view('student.reappearform',compact('corse','semester'));
    }


    public function searchReappear(Request $request){// this will searchreaper of student
        $subjectinfo = Course::where('Course_name',$request->course)->first();
        $findtable = Subject::where(['course_id'=>$subjectinfo->id,'Semester'=>$request->semester])->get();
        $setting = ReappearSetting::where(['course_id'=>$subjectinfo->id,'semester'=>$request->semester,'batch'=>$request->batch])->where('Reappear_late_fee_date','>=',date('Y-m-d'))->where('Reappear_from_date','<=',date('Y-m-d'))->orderby('Reappear_late_fee_date')->first();
        if(!isset($setting)){
            return back()->withErrors('Reappear form for '.strtoupper($request->course).' '.ordinalget($request->semester).' Semester for the Batch '.$request->batch.' is not open yet. Please check notifications for further information.');
        }
        
        $Reappear = null;
        
        $studentsubinfo = ResultData::with('course')->where(['Stud_nchm_roll_number'=>Auth::guard('student')->user()->rollnumber,'Stud_semester'=>$request->semester])->first();
        
        if($studentsubinfo->End_Result === 'Pass'){
            return back()->withSuccess('You clear your current semester examse.');
        }
        
        if(isset($studentsubinfo->End_Reappear_subject) && strlen($studentsubinfo->End_Reappear_subject) > 0){
            $Reappearexp = explode(',',($studentsubinfo->End_Reappear_subject ?? $studentsubinfo->Mid_Reappear_subject));
            foreach($Reappearexp as $sub){
                $searchsub = Subject::where('Subject_code',trim($sub))->first();
                $Reappear[] = ['code'=>trim($sub),'name'=>isset($searchsub->Subject_name) ? $searchsub->Subject_name : 'N/A','fee'=>isset($searchsub->Reappear_fee) ? $searchsub->Reappear_fee : 0];
            }
        }else{
            return back()->withErrors('You did not clear any of your mid-term exams.');
        }
        
        $subjectinfo = new conmmandatacall();
        $corse = $subjectinfo->course;
        $semester = $subjectinfo->semester;
        
        return view('student.reappearform',compact('corse','semester','Reappear','studentsubinfo'));
    }

    public function feepayment(Request $request){
        // return $request;
        $getmarksheet = ResultData::where(['Stud_nchm_roll_number'=>Auth::guard('student')->user()->rollnumber,'Stud_semester'=>$request->sendsemester])->first();
        $refranceid = $getmarksheet->Stud_nchm_roll_number;
        
        $nonmendatvalue = $refranceid.'|'.Auth::guard('student')->user()->Stud_name.'|xy|xy|xy|xy|xy|xy|xy|xy|xy|xy';
        
        $feetotal = 0;
        foreach($request->fee as $sub=>$singlefee){
            $checkfee = Subject::where('Subject_code',$sub)->first();
            if(!isset($checkfee)){
                return back()->withErrors('Sorry there is some issue.Please try after some time!');   
            }
            $feetotal += $checkfee->Reappear_fee;
        }
        
        $feetotal = (double)$feetotal;
        
        $base=new Eazypay();

        $url=$base->getPaymentUrl($feetotal, $refranceid,$getmarksheet->Stud_nchm_roll_number , $nonmendatvalue);

        return $url;
        return redirect()->to($url);
    }



    public function search($course,$semester,$rollno){
        $searchfor = Route::currentRouteName();

        $user = Auth::guard('student')->user();
        
        if($user->rollnumber != $rollno){
            abort(404);
        }
        
        if($searchfor  === 'student.reportcard'){
            $generateresult = ResultData::where(['Stud_nchm_roll_number'=>$rollno,'Stud_semester'=>$semester])->orderBy('Stud_nchm_roll_number')->where('End_Result_SGPA','!=',null)->where('End_Result_CGPA','!=',null);
            
            $results = $generateresult;
            
            if($results->count() > 0){
                
                $results = $results->get();

                $results = resultfiltaring($results);

                $html = view('pdf.pdfresult', compact('results'))->render();
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            
                return $pdf->stream();
            }else{
                return back()->withErrors('Result not Found. Please check Notice for feather details!');
            }
        }else{
            $getacdmitcard = ResultData::with('institute')->where(['Stud_nchm_roll_number'=>$rollno,'Stud_semester'=>$semester]);
            
            if($getacdmitcard->count() == 0){
                return back()->withErrors('Roll Number Not Existed!');
            }

            if((int)$request->semester < 3){
                $getacdmitcard = $getacdmitcard->get();
            }else{
                $getacdmitcard = $getacdmitcard->where('Mid_Result','Pass');
                
                if($getacdmitcard->count() == 0){
                    return back()->withErrors('Your Resutl of Mid-tearm is not clear!');
                }

                $getacdmitcard = $getacdmitcard->get();
            }
        }

        $getacdmitcard = $getacdmitcard->map(function ($card) {
            if ($card->Stud_semester == '3rd' || $card->Stud_semester == 3) {
                $card->Stud_semester = '4th'; // Change semester only if it's 3rd
            }
         

            return $card; //  Ensure the updated object is returned
        });
    
    
        if ($getacdmitcard->count() > 0) {
            $pdf = FacadePdf::loadView('pdf.pdfadmitcard', compact('getacdmitcard'));
            return $pdf->stream();
        } else {
            return back()->withErrors('Sorry, your Admit Card is not generated yet. Please check the Notice for further details!');
        }
    }}