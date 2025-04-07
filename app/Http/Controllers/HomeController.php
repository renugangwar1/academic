<?php

namespace App\Http\Controllers;

use App\Models\Bsc;
use App\Models\BscSemFirst;
use App\Models\Course;
use App\Models\Institute;
use App\Models\Msc;
use App\Models\MscSemFirst;
use App\Models\ResultData;
use App\Models\DiplomaResult;
use App\Models\Subject;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use conmmandatacall;
use SubjectCompailer;
use ResultGenerate;
use PrintDetails;
use PrintDiplomaResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\DriverManager;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::guard('student')->check()) {
            if (!Auth::guard('student')->user()->hasVerifiedEmail()) {
                return redirect()->route('student.verification.notice')->withErrors(['email' => 'You need to verify your email address.']);
            }
            return redirect()->route('student.dashboard');
        } elseif (Auth::guard('institute')->check()) {
            if (!Auth::guard('institute')->user()->hasVerifiedEmail()) {
                return redirect()->route('institute.verification.notice')->withErrors(['email' => 'You need to verify your email address.']);
            }
            return redirect()->route('institute.dashboard');
        }


        if(Auth::check()){
            $subjectinfo = new conmmandatacall();
            $institutes = $subjectinfo->institutes;
            $corse = $subjectinfo->course;
            $corsename = $subjectinfo->coursename;
            $semester = $subjectinfo->semester;
            return view('home',compact('institutes','corsename','corse','semester'));
        }else{
            return view('welcome');
        }
    }

    // $subjectinfo = new conmmandatacall();
    // $institutes = $subjectinfo->institutes;
    // $corse = $subjectinfo->course;
    // $corsename = $subjectinfo->coursename;
    // $semester = $subjectinfo->semester;
    // return view('compileresult',compact('corse','corsename','semester','institutes'));

    public function compileResult(){
        $subjectinfo = new conmmandatacall();
        $institutes = $subjectinfo->institutes;
        $corse = $subjectinfo->course;
        $corsename = $subjectinfo->coursename;
        $semester = $subjectinfo->semester;
        return view('compileresult',compact('corse','corsename','semester','institutes'));
    }

    public function compilingResult(Request $request)
    {
        try {
            set_time_limit(0);
            
            // $subject = new conmmandatacall();
            // $currentcourse = Course::where('Course_name', $request->course)->first();
            
            $dataQuery = ResultData::select('id','course_id', 'Stud_batch', 'Optional_subject', 'Stud_semester', 'institute_id', 'Mid_Result', 'Mid_marks', 'End_marks', 'Mid_Reappear_subject', 'End_Reappear_subject')->where([
                'course_id' => $request->course,
                'Stud_batch' => $request->batch,
                'Stud_semester' => $request->semester,
            ]);

            if ($request->institute != null) {
                $dataQuery->where('institute_id', $request->institute);
            }
            
            $totalData = $dataQuery->count();
            
            if ($totalData === 0) {
                return response()->json(['success' => false, 'message' => 'Data does not exist.'], 404);
            }
            
            if ($request->term == 'Mid') {
                $dataQuery->whereNull('Mid_Result');
            } elseif ($request->term == 'End') {
                $dataQuery->whereNull('End_Reappear_subject');
            }

            $uncompiledDataCount = $dataQuery->count();

            if ($uncompiledDataCount === 0) {
                return response()->json(['success' => false, 'message' => 'Data Already Compiled!'], 400);
            }

            $subject = Subject::select('id', 'Optional_subject', 'Subject_code', 'Mid_pass_mark', 'End_pass_mark', 'Semester', 'course_id', 'Mid_max_mark', 'End_max_mark', 'Credit', 'It_status')->where(['course_id'=>$request->course,'Semester'=>$request->semester])
            ->orderBy('Subject_code')
            ->get()->toArray();

            $existingArray = [];

            $dataQuery->chunk(2000, function ($dataChunk) use ($request,$subject, &$existingArray) {
                $dataChunk = $dataChunk->toArray();
                $newArray = SubjectCompailer::BscOrMsccourse($dataChunk, $subject, $request->term);
                $existingArray = array_merge($existingArray, $newArray);
            });

            $currentcompiled = ['totallist'=>$uncompiledDataCount,'currentlist'=>count($existingArray)];

            if (empty($existingArray)) {
                return response()->json(['success' => false, 'message' => 'No data to compile.'], 400);
            }

            $compileresult = ResultData::with('student')->whereIn('id', $existingArray)
                ->select('id','student_id','Stud_academic_year', 'Mid_Reappear_subject', 'Mid_Appear_subject', 'Total_Reappear_subject', 'Reappear_subject_count')
                ->get();
            
            if ($request->term == 'Mid') {
                $view = view('components.midcompile', compact('compileresult','currentcompiled'));
            } elseif ($request->term == 'End') {
                $view = view('components.endcompile', compact('compileresult','currentcompiled'));
            }

            return $view;

        } catch (CustomeMessageDisplay $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function compiledview(Request $request){
        $validate = $request->validate([
            "course" => 'required',
            "batch" => 'required',
            "semester" => 'required',
            "term" => 'required'
        ],[
            "course.required" => 'You did not select a course.',
            "batch.required" => 'You did not select a Batch.',
            "semester.required" => 'You did not select a Semester.',
            "term.required" => 'You did not select a Tearm.'
        ]);
        
        if($validate){
            $compileresult = ResultData::with('student')->where(['course_id'=>$request->course,'Stud_batch'=>$request->batch,'Stud_semester'=>$request->semester])
            ->select('id','student_id','Stud_academic_year', 'Mid_Reappear_subject', 'Mid_Appear_subject', 'Total_Reappear_subject', 'Reappear_subject_count','institute_id');
            
            if($request->institute){
                $compileresult = $compileresult->where('institute_id',$request->institute);
            }
    
            $compileresult = $compileresult->get();

            $currentcompiled = ['totallist'=>$compileresult->count(),'currentlist'=>$compileresult->count()];
    
            if ($request->term == 'Mid') {
                $view = view('components.midcompile', compact('compileresult','currentcompiled'));
            } elseif ($request->term == 'End') {
                $view = view('components.endcompile', compact('compileresult','currentcompiled'));
            }else{
                return response()->json(['error' => false, 'message' => 'Please select Tearm to view compile result'], 500);
            }
    
            return $view;
        }else{
            return response()->json(['error' => false, 'message' => $validate], 500);
        }
    }


    public function compiledPrint(Request $request){
        $validate = $request->validate([
            'course'=>'required',
            'batch'=>'required',
            'semester'=>'required',
            'institute'=>'required',
        ],[
            'course.required'=>'Please select Course!',
            'batch.required'=>'Please select Batch!',
            'semester.required'=>'Please select Semester!',
            'institute.required'=>'Please select Institute!',
        ]);

        set_time_limit(0);

        $print = new PrintDetails($request->course,$request->semester,$request->batch,$request->institute,$request->rollno);
        
        return $print->PrintAdmitCard();
    }
    

    // result generate controllers /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function generateResult(){
        $subjectinfo = new conmmandatacall();
        $institutes = $subjectinfo->institutes;
        $corse = $subjectinfo->course;
        $corsename = $subjectinfo->coursename;
        $semester = $subjectinfo->semester;
        return view('generateresult',compact('corse','corsename','semester','institutes'));
    }

    public function generatingResult(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'course' => 'required',
            'batch' => 'required',
            'semester' => 'required'
        ], [
            'course.required' => 'The course field is required.',
            'batch.required' => 'The batch field is required.',
            'semester.required' => 'The semester field is required.'
        ]);

        try {
            set_time_limit(0);

            $checkcourse = Course::where('id',$request->course)->first();

            $subject = Subject::select('id','Mid_max_mark','Mid_pass_mark','End_max_mark','End_pass_mark','Subject_code')->where(['course_id'=>$request->course,'Semester'=>$request->semester])
                ->orderBy('Subject_code')
                ->get()->toArray();
    
            // if($subject && (int) $request->semester === 4){
            //     return response()->json(['success' => false, 'message' => 'Current selected data is for IT Students.'], 500);
            // }
            if($checkcourse->Course_type == 1){
                // Build the initial query
                $query = ResultData::select('id','student_id','course_id', 'Stud_batch', 'Stud_semester', 'institute_id', 'Mid_Result', 'End_marks', 'Marks_total', 'Marks_credit_point', 'Marks_credit', 'Mid_Reappear_subject', 'Mid_Appear_subject', 'Total_Reappear_subject')
                ->where([
                    'course_id' => $request->course,
                    'Stud_batch' => $request->batch,
                    'Stud_semester' => $request->semester,
                ]);
            }else{
                $query = DiplomaResult::select('id','student_id','course_id', 'Stud_batch', 'Stud_semester', 'institute_id', 'Mid_marks', 'End_marks',)
                ->where([
                    'course_id' => $request->course,
                    'Stud_batch' => $request->batch,
                    'Stud_semester' => $request->semester,
                ]);
            }
            
            // Filter by institute if provided
            if ($request->institute) {
                $query->where('institute_id', $request->institute);
            }
            
            // Check if data exists
            if ($query->count() === 0) {
                return response()->json(['success' => false, 'message' => 'Data not available for current selection!'], 500);
            }

            if($checkcourse->Course_type == 1){
                $missinginsmarks = clone $query;
    
                $missing = $missinginsmarks->where('Mid_Result', '!=', 'Fail')->wherenull('End_marks');
                
                if ($missing->exists()) {
                    $missingInstitutes = $missing->pluck('institute_id')->unique();
                    
                    $errorMessages = $missingInstitutes->map(function ($instid) {
                        return "End term marks are missing for Institute {$instid}!";
                    })->toArray();
    
                    return response()->json(['success' => false, 'message' => implode("\n", $errorMessages)], 500);
                }

                $query->wherenotnull('Mid_Result')->wherenull('End_Result');
            }else{
                $missinginsmarks = clone $query;
    
                $missing = $missinginsmarks->wherenull('End_marks');
                
                if ($missing->exists()) {
                    $missingInstitutes = $missing->pluck('institute_id')->unique();
                    
                    $errorMessages = $missingInstitutes->map(function ($instid) {
                        return "End term marks are missing for Institute {$instid}!";
                    })->toArray();
    
                    return response()->json(['success' => false, 'message' => implode("\n", $errorMessages)], 500);
                }

                $query->wherenull('Result');
            }

            // Filter for non-null Mid_Result and null End_Result
            
            $totallist = $query->count();

            // Process data in chunks
            $existingArray = [];

            if($checkcourse->Course_type == 1){
                $query->chunk(2000, function($dataChunk) use (&$existingArray, $request,$subject) {
                    $dataChunk = $dataChunk->toArray();
                    $newArray = ResultGenerate::BscOrMsccourse($dataChunk, $subject, (int)$request->semester);
                    $existingArray = array_merge($existingArray, $newArray);
                });
            }else{
                $query->chunk(2000, function($dataChunk) use (&$existingArray, $request,$subject) {
                    $dataChunk = $dataChunk->toArray();
                    $newArray = ResultGenerate::DeeplomaResult($dataChunk, $subject, (int)$request->semester);
                    $existingArray = array_merge($existingArray, $newArray);
                });
            }

            $currentcompiled = ['totallist'=>$totallist,'currentlist'=>count($existingArray)];
            $sem = $request->semester;
            
            if($checkcourse->Course_type == 1){
                // Fetch the generated results
                $generatedResults = ResultData::with('student')->whereIn('id', $existingArray)->get();
            }else{
                $generatedResults = DiplomaResult::with('student')->whereIn('id', $existingArray)->get();
            }

            // Check if any results exist
            if ($generatedResults->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Result already generated.'], 500);
            }

            if($checkcourse->Course_type == 1){
                // Render the results view
                return view('components.resultcompile', compact('generatedResults','currentcompiled','sem'));
            }else{
                return view('components.deeplomacompile', compact('generatedResults','currentcompiled','sem'));
            }

        } catch (CustomeMessageDisplay $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function generatingCGPA(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'course' => 'required',
            'batch' => 'required',
            'semester' => 'required'
        ], [
            'course.required' => 'The course field is required.',
            'batch.required' => 'The batch field is required.',
            'semester.required' => 'The semester field is required.'
        ]);

        try {
            set_time_limit(0);

            $subject = Subject::select('id','Mid_max_mark','End_max_mark','Subject_code')->where(['course_id'=>$request->course,'Semester'=>$request->semester])
                ->orderBy('Subject_code')
                ->get()->toArray();
    
            // if($subject && (int) $request->semester === 4){
            //     return response()->json(['success' => false, 'message' => 'Current selected data is for IT Students.'], 500);
            // }

            // Build the initial query
            $query = ResultData::select('id','student_id','course_id', 'Stud_batch', 'Stud_semester', 'institute_id', 'Mid_Result', 'End_marks', 'Marks_total', 'Marks_credit_point', 'Marks_credit', 'Mid_Reappear_subject', 'Mid_Appear_subject', 'Total_Reappear_subject')
            ->where([
                'course_id' => $request->course,
                'Stud_batch' => $request->batch,
                'Stud_semester' => $request->semester,
            ]);

            // Filter by institute if provided
            if ($request->institute) {
                $query->where('institute_id', $request->institute);
            }
            
            // Check if data exists
            if ($query->count() === 0) {
                return response()->json(['success' => false, 'message' => 'Data not available for current selection!'], 500);
            }

            $missinginsmarks = clone $query;

            $missing = $missinginsmarks->where('Mid_Result', '!=', 'Fail')->wherenull('End_marks');
            
            if ($missing->exists()) {
                $missingInstitutes = $missing->pluck('institute_id')->unique();
                
                $errorMessages = $missingInstitutes->map(function ($instid) {
                    return "End term marks are missing for Institute {$instid}!";
                })->toArray();

                return response()->json(['success' => false, 'message' => implode("\n", $errorMessages)], 500);
            }

            // Filter for non-null Mid_Result and null End_Result
            $query->wherenotnull('Mid_Result')->wherenull('End_Result');
            
            $totallist = $query->count();

            // Process data in chunks
            $existingArray = [];

            $query->chunk(2000, function($dataChunk) use (&$existingArray, $request,$subject) {
                $dataChunk = $dataChunk->toArray();
                $newArray = ResultGenerate::CGPCallculation($dataChunk, $subject, (int)$request->semester);
                $existingArray = array_merge($existingArray, $newArray);
            });

            $currentcompiled = ['totallist'=>$totallist,'currentlist'=>count($existingArray)];
            $sem = $request->semester;
            
            // Fetch the generated results
            $generatedResults = ResultData::with('student')->whereIn('id', $existingArray)->get();
            
            // Check if any results exist
            if ($generatedResults->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Result already generated.'], 500);
            }

            // Render the results view
            return view('components.resultcompile', compact('generatedResults','currentcompiled','sem'));

        } catch (CustomeMessageDisplay $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function viewgeneratingResult(Request $request){

        $vaidate = $request->validate([
            'course'=>'required',
            'batch'=>'required',
            'semester'=>'required',
        ],[
            'course.required'=>'You did not select a course.',
            'batch.required'=>'You did not select a Batch.',
            'semester.required'=>'You did not select a Semester.',
        ]);

        if($vaidate){
            $course = Course::where('id',$request->course)->first();
            
            if($course->Course_type == 1){
                // Fetch the generated results
                $generatedResults = ResultData::with('student')->where(['course_id'=>$request->course, 'Stud_batch'=>$request->batch, 'Stud_semester'=>$request->semester]);
            }else{
                $generatedResults = DiplomaResult::with('student')->where(['course_id'=>$request->course, 'Stud_batch'=>$request->batch, 'Stud_semester'=>$request->semester]);
            }

            if($request->institute){
                $generatedResults = $generatedResults->where('institute_id',$request->institute);
            }
    
            $generatedResults = $generatedResults->get();

            $currentcompiled = ['totallist'=>$generatedResults->count(),'currentlist'=>$generatedResults->count()];

            $sem = $request->semester;
    
            // Check if any results exist
            if ($generatedResults->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Result not generated of current selection.'], 500);
            }else{
                if($course->Course_type == 1){
                    // Render the results view
                    return view('components.resultcompile', compact('generatedResults','currentcompiled','sem'));
                }else{
                    return view('components.deeplomacompile', compact('generatedResults','currentcompiled','sem'));
                }
            }
        }else{
            return response()->json(['success' => false, 'message' => $vaidate], 500);
        }

    }

    public function generatedPrint(Request $request){
        $validate = $request->validate([
            'course'=>'required',
            'batch'=>'required',
            'semester'=>'required',
            'institute'=>'required',
        ],[
            'course.required'=>'Please select Course!',
            'batch.required'=>'Please select Batch!',
            'semester.required'=>'Please select Semester!',
            'institute.required'=>'Please select Institute!',
        ]);

        $course = Course::where('id',$request->course)->first();
        
        set_time_limit(0);
        
        $print = new PrintDetails($request->course,$request->semester,$request->batch,$request->institute);
        
        if($course->Course_type == 1){
            return $print->PrintResult();
        }else{
            return $print->PrintDiplomaResult();
        }
        
    }

    public function jnuresult(){
        $subjectinfo = new conmmandatacall();
        $institutes = $subjectinfo->institutes;
        $corse = $subjectinfo->course;
        $corsename = $subjectinfo->coursename;
        $semester = $subjectinfo->semester;
        return view('jnuresult',compact('corse','corsename','semester','institutes'));
    }

    public function showjnuresult(Request $request){
        $request->validate([
            'course'=>'required',
            'batch'=>'required',
            'semester'=>'required',
            'institute'=>'required',
        ],[
            'course.required'=>'Please Select Course',
            'batch.required'=>'Please Select Batch',
            'semester.required'=>'Please Select Semester',
            'institute.required'=>'Please Select Institute',
        ]);
        
        if($request->institute){
            $instinfo = Institute::select('id','InstituteName','InstituteCode')->where('id',$request->institute)->first();
        }
        
        $db = Subject::select(
            'id',
            'Subject_code',
            'Subject_name',
            'Credit',
            'Optional_subject',
            'course_id',
            'Semester',
        )->where(['course_id'=>$request->course,'Semester'=>$request->semester])->orderBy('Subject_code')->get();;
        
        // $totalcredit = ($subget->where('Optional_subject',null)->sum('Credit')) + (Subject::where(['Course_code'=>$request->course,'Semester'=>$request->semester])->whereNotnull('Optional_subject')->sum('Credit') / 2);
        $totalcredit = 0;
        
        $generateresult = ResultData::with('student')->select(
            'id',
            'student_id',
            'Marks_grade',
            'Marks_grade_point',
            'Grand_Credit_Point',
            'End_Result_CGPA',
            'course_id',
            'Stud_batch',
            'Stud_semester',
            'institute_id',
            'End_Result_SGPA',
        )->where([
            'course_id'=>$request->course,
            'Stud_batch'=>$request->batch,
            'Stud_semester'=>$request->semester,
        ])->orderBy(Student::select('JNU_Rollnumber')
        ->whereColumn('students.id', 'result_data.student_id'));

        if($request->institute){
            $generateresult = $generateresult->where('institute_id',$request->institute);
        }
        
        $results = $generateresult->where('End_Result_SGPA','!=',null)->where('End_Result_CGPA','!=',null);

        if($results->count() > 0){
            $results = $results->get();

            $pdf = FacadePdf::loadView('pdf.viewjnuresult', compact('instinfo','results','db','totalcredit'));
            
            return $pdf->stream();
        }else{
            return back()->withErrors('Result not generated yet!');
        }
    }

    public function bankResponse(Request $request){
        return $request;
    }
}
