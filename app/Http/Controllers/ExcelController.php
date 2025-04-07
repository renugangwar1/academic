<?php

namespace App\Http\Controllers;

use App\Exports\JNUExport;
use App\Exports\PrintExport;
use App\Exports\ResultExport;
use App\Exports\StudentExport;
use App\Exports\TemExport;
use App\Imports\DataImport;
use App\Imports\DiplomaDataImport;
use App\Imports\StudentImport;
use App\Imports\StudentUpdate;
use App\Models\Course;
use App\Models\ExcelLog;
use App\Models\Institute;
use App\Models\ResultData;
use App\Models\DiplomaResult;
use App\Models\ResultDataBackup;
use App\Models\Student;
use App\Models\Subject;
use App\Models\StudentHistory;
use basic;
use History;
use DataView;
use conmmandatacall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Str;
use LDAP\Result;
use RowCountImport;
use App\Exceptions\InstituteCodeNotFoundException;
use App\Exceptions\CustomeMessageDisplay;
use Illuminate\Support\Facades\Cache;

use function Laravel\Prompts\error;

class ExcelController extends Controller
{
    public function template(Request $request){
        return Excel::download(new TemExport($request), 'ImportTemp.xlsx');
    }

    public function export(){
        $subjectinfo = new conmmandatacall();
        $institutes = $subjectinfo->institutes;
        $course = $subjectinfo->course;
        $corsename = $subjectinfo->coursename;
        $semester = $subjectinfo->semester;

        return view('export',compact('course','corsename','semester','institutes'));
    }


    // ucwords($tb->Table_name).'('.$request->exportbatch.')
    public function exportdata(Request $request){
        try{
            return Excel::download(new ResultExport($request->exportcourse,$request->exportbatch,$request->exportsemester,$request->exportinstitute), 'Export.xlsx');
        }catch (\Exception $e) {
            return back()->withErrors('Please Provide Valid File and Details');
        }
    }

    public function Import(){
        $incomingdata = session('data') ? session('data') : null;
        $studCourse = session('Stud_course') ? session('Stud_course') : null;
        // $studBatch = session('Stud_batch') ? session('Stud_batch') : null;
        $studSemester = session('Stud_semester') ? session('Stud_semester') : null;
        $term = session('term') ? session('term') : null;
        // $Stud_institute = session('Stud_institute') ? session('Stud_institute') : null;
        
        $subjectinfo = new conmmandatacall();
        $institutes = $subjectinfo->institutes;
        $course = $subjectinfo->course;
        $corsename = $subjectinfo->coursename;
        $semester = $subjectinfo->semester;

        
        if(isset($incomingdata)){
            
            $tb = Subject::where(['course_id'=>$studCourse,'Semester'=>$studSemester])->orderBy('Subject_code')->get();
        
            foreach($tb as $single){
                $termsub[] = ucfirst($term).'_'.strtoupper($single->Subject_code);
            }

            $primary = [
                'name',
                'NCHMCT_Rollnumber',
                'JNU_Rollnumber',
                'Stud_academic_year',
            ];
    
            if($term == 'mid'){
                $primary[] = 'Mid_marks';
                $incomingdata = $incomingdata->select($primary);
                foreach($incomingdata as $key=>$single){
                    $newaray = json_decode($single['Mid_marks']);
                    foreach($newaray as $title=>$data){
                        $single[$title] = $data;
                    }
                    $incomingdata[$key]=$single;
                }
            }else if($term == 'end'){
                $primary[] = 'End_marks';
                $incomingdata = $incomingdata->select($primary);
                foreach($incomingdata as $key=>$single){
                    $newaray = json_decode($single['End_marks']);
                    foreach($newaray as $title=>$data){
                        $single[$title] = $data;
                    }
                    $incomingdata[$key]=$single;
                }
            }
    
            $insdata = $incomingdata;

            return view('Import',compact('course','corsename','semester','institutes','insdata','termsub'));
        }else{
            return view('Import',compact('course','corsename','semester','institutes'));
        }
    }

    public function importdata(Request $request){
        if($request->file('importfile') != null && $request->course != null && $request->batch != null && $request->semester != null){
            flushcache();
            try {
                $course = Course::where('id',$request->course)->first();
                // dd($course);
                
                // Count total rows excluding the header
                $rowCountImport = new RowCountImport($course,$request->semester,$request->termmarks); // checked

                Excel::import($rowCountImport, $request->file('importfile'));
                
                $rowCountImport->checkexcelwithdb();

                // Retrieve the cached value
                $excelsubjects = Cache::get('subjects_list');
                $existchecklist = Cache::get('check_list');
                $existprevlist = Cache::get('previuse_list');
                $studentList = Cache::get('student_list');


                if (!$studentList) {
                    // If the cache is empty, store the new value and retrieve it
                    $studentList = Cache::remember('student_list', 1800, function() use ($request) {
                        return Student::where('batch',$request->batch)
                        ->select('id','optionalSubject','name','NCHMCT_Rollnumber','JNU_Rollnumber','course_id')
                        ->orderBy('NCHMCT_Rollnumber')
                        ->get()
                        ->mapWithKeys(function ($item) {
                            return [$item['NCHMCT_Rollnumber'] => $item];
                        })
                        ->toArray();
                    });
                }

                if (!$excelsubjects) {
                    // If the cache is empty, store the new value and retrieve it
                    $excelsubjects = Cache::remember('subjects_list', 1800, function() use ($request) {
                        return Subject::where(['course_id' => $request->course, 'Semester' => $request->semester])
                            ->select('id', 'Optional_subject', 'Subject_code', 'Mid_max_mark', 'End_max_mark', 'Semester', 'It_status')
                            ->orderBy('Subject_code')
                            ->get()->toArray();
                    });
                }

                $itcheck = collect($excelsubjects)->pluck('It_status')->toArray();
                
                $oldsem = in_array(0,$itcheck) ? ($request->semester - 1) : ($request->semester - 2);
                
                if (!$existchecklist) {
                    // If the cache is empty, store the new value and retrieve it with('student:id,NCHMCT_Rollnumber')->
                    $existchecklist = Cache::remember('check_list', 1800, function() use ($request,$course) {
                        if($course->Course_type == 1){
                            $checklist = ResultData::where([
                                'Stud_batch'=>$request->batch,
                                'Stud_semester'=>$request->semester,
                            ]);
                
                        }else{
                            $checklist = DiplomaResult::where([
                                'Stud_batch'=>$request->batch,
                                'Stud_semester'=>$request->semester,
                            ]);
                        }
                        if($request->institute){
                            $checklist = $checklist->where('institute_id',$request->institute);
                        }
                        
                        return $checklist->get()->toArray();
                        // ->mapWithKeys(function ($item) {
                        //     return [$item['NCHMCT_Rollnumber'].$item['Stud_semester'].$item['institute_id'] => $item];
                        // })
                    });
                }

                if(!$existprevlist && $course->Course_type == 1){
                    $existprevlist = Cache::remember('previuse_list', 1800, function() use ($request,$oldsem,$course) {
                        
                        $prevlist = ResultData::where([
                            'Stud_batch'=>$request->batch,
                            'Stud_semester'=>$oldsem
                        ])->select('id','student_id','institute_id','Mid_Reappear_subject','Mid_Appear_subject','Total_Reappear_subject','End_Result_CGPA','Stud_semester');
                        
                        if($request->institute){
                            $prevlist = $prevlist->where('institute_id',$request->institute);
                        }
                        
                        return $prevlist->get()->toArray();
                    });
                }
                
                // dd($excelsubjects, $existchecklist, $existprevlist, $studentList);
                // importing excel data into database
                
                if($course->Course_type == 1){
                    $dataImport = new DataImport($request->batch, $request->semester, $request->institute, $course, $request->termmarks, $rowCountImport, $excelsubjects, $existchecklist, $existprevlist, $studentList); 
                }else{
                    $dataImport = new DiplomaDataImport($request->batch, $request->semester, $request->institute, $course, $request->termmarks, $rowCountImport, $excelsubjects, $existchecklist, $studentList);
                }

                
                Excel::import($dataImport, $request->file('importfile'));
                
                
                // Clear the cache for these keys
                flushcache();
               
                
                $errors = [];
                
                if (!empty($dataImport->studentnotexist)) {
                    $errors = array_merge($errors, $dataImport->studentnotexist);
                }
                
                if($dataImport->studentcount === 0){
                    \abort(500,"Student is not exist in selected batch {$request->batch}");
                }

                $data = $dataImport;

                $totalentries = [];
                
                // Start a database transaction
                DB::transaction(function () use ($data,$course) {
                    $newEntries = $data->newinsert;
                    $updateEntries = $data->updateinsert;

                    // Batch insert new entries
                    if(!empty($newEntries)) {
                        $chunks = array_chunk($newEntries, 1000); // Adjust the chunk size as needed
                        foreach ($chunks as $chunk) {
                            if($course->Course_type == 1){
                                ResultData::insert($chunk);
                                $totalentries = $chunk;
                            }else{
                                DiplomaResult::insert($chunk);
                                $totalentries = $chunk;
                            }
                            
                        }
                    }

                    if(!empty($updateEntries)){
                        // Batch update existing entries
                        foreach ($updateEntries as $entry) {
                            if($entry){
                                if($course->Course_type == 1){
                                    ResultData::where($entry['conditions'])->update($entry['data']);
                                }else{
                                    DiplomaResult::where($entry['conditions'])->update($entry['data']);
                                }
                            }
                        }
                    }
                });

                if(!empty($data->newinsert)){
                    array_push($totalentries, ...$data->newinsert);
                }else if(!empty($data->updateinsert)){
                    foreach ($data->updateinsert as $entry) {
                        if($entry){
                            $totalentries[] = $entry['data'];
                        }
                    }
                }

                $datacount = count($totalentries);

                $totalentries = collect($totalentries);
                
                if( $datacount > 0){ // store upload excel file for log

                    $file = uploadFile($request->file('importfile'),date('Y/m'),Str::slug($course->Course_name).'_'.date('m_Y_h_i_s'));
                    try{
                        $upload = ExcelLog::create([
                            'user_id'=>Auth::guard('institute')->user()->id ?? Auth::user()->id,
                            'excel_title'=>Str::slug($course->Course_name).'_'.date('m_Y_h_i_s'),
                            'excel_link'=>$file,
                            'UserName'=>Auth::guard('institute')->user() ? Auth::guard('institute')->user()->InstituteName : Auth::user()->name,
                            'Tearm'=>$request->termmarks,
                            'Batch'=>$request->batch,
                            'system'=>$request->ip(),
                        ]);
                    }catch (\Exception $e) {
                        // fixpgSequence('excel_logs');
                        return back()->withErrors('There is a technical issue, please re-upload your Excel file!');
                    }
                    
                    $basic = new basic();
    
                    $basic->storeuploadexcel($upload->excel_link,$data->rowentry,Str::slug($course->Course_name),'exceluploads',$file,$request->semester);
                }

                $route = Auth::guard('institute')->user() ? 'institute.excel.Import':'excel.Import'; // redirect route according to current user
                
                $dpdata = collect($data->dpdata);


                if($datacount > 0){ // if data is upload successfully
                    return redirect()->route($route)->with('data',$dpdata)->with('Stud_course', $request->course)
                    ->with('Stud_batch', $request->batch)
                    ->with('Stud_institute',$request->institute)
                    ->with('Stud_semester', $request->semester)->with('term',$request->termmarks)->withSuccess('Excel Upload Successfully!')->withErrors($errors);
                }else if(count($dataImport->message) > 0){ // if there is any error regarding to data
                    return redirect()->route($route)->with('data',$dpdata)->with('Stud_course', $request->course)
                    ->with('Stud_batch', $request->batch)
                    ->with('Stud_institute',$request->institute)
                    ->with('Stud_semester', $request->semester)->with('term',$request->termmarks)->withErrors($dataImport->message);
                }else{// if data is already uploaded into database
                    return redirect()->route($route)->with('data',$dpdata)->with('Stud_course', $request->course)
                    ->with('Stud_batch', $request->batch)
                    ->with('Stud_institute',$request->institute)
                    ->with('Stud_semester', $request->semester)->with('term',$request->termmarks)->withErrors('Data Is already Existed!');
                }
                
            } catch (\Exception $e) { // in case of try catch faild 
                return back()->withErrors('Error importing data: ' . $e->getMessage());
            }
        }else{
            return back()->withErrors('Please Provide Valid File and Details');
        }
    }

    public function exportjnu(Request $request){
        $tb = Subject::with('course')->where(['course_id'=>$request->course,'Semester'=>$request->semester])->first();
        
        if(isset($tb->course->Course_name)){ 
            $filename = ucwords($tb->course->Course_name).'('.$request->batch.')ExportJNUResult.xlsx';
            
            return Excel::download(new JNUExport($request->course,$request->batch,$request->semester,$request->institute),$filename);
        }else{
            return back()->withErrors('Please Provide Valid File and Details');
        }
    }

    public function studenttemplate(Request $request){
        return Excel::download(new StudentExport('new',$request->course,$request->batch), 'StudentImportTemp.xlsx');
    }

    public function importstudentlist(Request $request){
        
        $request->validate([
            'excel_file'=>'required|mimes:xlsx,xls',
        ], [
            'excel_file.required' => 'Excel File Rrequired',
            'excel_file.mimes' => 'Please provide Excel File only',
        ]);

        try {
            $studentlist = Student::get()->toArray();
            $institutelist = Institute::get()->toArray();
            $courselist = Course::select('id','Course_name')->get()->toArray();
            
            $dataImport = new StudentImport($studentlist,$courselist,$institutelist);
        
            Excel::import($dataImport, $request->file('excel_file'));
            
            $importedData = Student::whereIn('id', $dataImport->importedIds)->get();
            
            if(count($dataImport->importedIds) > 0){
                return redirect()->route(isset(Auth::guard('institute')->user()->id) ? 'institute.uploadstudentlist' : 'admin.uploadstudentlist')->with('data',$importedData)->withSuccess('Excel Upload Successfully!');
            }else{
                return redirect()->route(isset(Auth::guard('institute')->user()->id) ? 'institute.uploadstudentlist' : 'admin.uploadstudentlist')->with('data',$importedData)->withErrors('Student list is already Existed!');
            }

        } catch (CustomeMessageDisplay $e) {
            
            return redirect()->back()->with('error', $e->getMessage());
        
        }
    }

    public function tempoptionalstudentlist(Request $request){
        return Excel::download(new StudentExport('optional',$request->op_course,$request->op_batch), 'optionalsubjectlistTemp.xlsx');
    }


    public function updatestudentlist(Request $request){
        $request->validate([
            'update_excel_file'=>'required|mimes:xlsx,xls',
        ], [
            'update_excel_file.required' => 'Excel File Rrequired',
            'update_excel_file.mimes' => 'Please provide Excel File only',
        ]);

        try {
            $studentlist = Student::select('id','name','NCHMCT_Rollnumber','course_id','optionalSubject')->get()->toArray();
            $dataImport = new StudentUpdate($studentlist);

            Excel::import($dataImport, $request->file('update_excel_file'));

            $importedData = Student::whereIn('id', $dataImport->importedIds)->get();

            if(count($dataImport->importedIds) > 0){
                return redirect()->route(isset(Auth::guard('institute')->user()->id) ? 'institute.uploadstudentlist' : 'admin.uploadstudentlist')->with('updatedata',$importedData)->withSuccess('Excel Upload Successfully!');
            }else{
                return redirect()->route(isset(Auth::guard('institute')->user()->id) ? 'institute.uploadstudentlist' : 'admin.uploadstudentlist')->with('updatedata',$importedData)->withErrors('Student list is already updated!');
            }
        } catch (CustomeMessageDisplay $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function exportshow(){
        $subjectinfo = new conmmandatacall();
        $institutes = $subjectinfo->institutes;
        $course = $subjectinfo->course;
        $corsename = $subjectinfo->coursename;
        $semester = $subjectinfo->semester;
        return view('search.excel.datasearchform',compact('course','corsename','semester','institutes'));
    }

    public function viewexcel(Request $request){
        $findcourse = Course::where('id',$request->exportcourse)->first();
        if($findcourse->Course_type == 1){
            return DataView::degreeview($request);
        }else{
            return DataView::diplomaview($request);
        }
    }

    public function viewhistory(){
        $subjectinfo = new conmmandatacall();
        $institutes = $subjectinfo->institutes;
        $course = $subjectinfo->course;
        $corsename = $subjectinfo->coursename;
        $semester = $subjectinfo->semester;
        return view('search.excel.history',compact('course','corsename','semester','institutes'));
    }

    public function searchexcel(Request $request){
        $studentcheck = Student::where(['NCHMCT_Rollnumber'=>$request->rollno,'batch'=>$request->historybatch]);
        $historystudentcheck = StudentHistory::where(['NCHMCT_Rollnumber'=>$request->rollno,'batch'=>$request->historybatch]);
        $tb = Subject::where(['course_id'=>$request->historycourse,'Semester'=>$request->historysemester])->orderBy('Subject_code')->get();
        
        $data = new History($request->historybatch,$request->historysemester,$request->historycourse,$request->rollno);
        
        if($studentcheck->exists()){
            $current = $data->Current($studentcheck->first()->toArray());
            $previus = $data->Previus('student',$studentcheck->first()->toArray());
            $newdata = array_merge($current,$previus);
        }else if($historystudentcheck->exists()){
            $newdata = $data->SHistory('student_history',$historystudentcheck->first()->toArray());
        }else{
            $newdata = [];
        }

        $heading = [
            'updated_at'=>'Entry Date & Time',
            'name'=>'Student Name',
            'NCHMCT_Rollnumber'=>'NCHM Roll No',
            'JNU_Rollnumber'=>'JNU Roll No',
            'InstituteName'=>'Institute Code'
        ];

        $heading['Mid_marks'] = 'Mid_marks';
        $heading['End_marks'] = 'End_marks';
        $heading['Marks_total'] = 'Marks_total';
        $heading['Marks_grade_point'] = 'Marks_grade_point';
        $heading['Marks_credit_point'] = 'Marks_credit_point';
        $heading['Marks_grade'] = 'Marks_grade';
        $heading['Grand_Total'] = 'Total';
        $heading['Grand_Credit_Point'] = 'Total Credit Point';
        $heading['Total_Percentage'] = 'Total Percentage';
        $heading['End_Result'] = 'Result';
        $heading['End_Result_SGPA'] = 'Result SGPA';
        $heading['End_Result_CGPA'] = checkEven($request->historysemester) == true ? 'Result CGPA' : 'Result SGPA';
        $heading['Optional_subject'] = 'Optional Subject';
        $heading['Total_Reappear_subject'] = 'Reappear Subject';
        
        if(count($newdata) > 0){
            $view = view('components.historyview',compact('heading','newdata'));
            return $view;
        }else{
            $errorMessage = 'No History Found For The Given Data.';
            return response()->json(['message' => $errorMessage], 500);
        }
    }

    public function instituteview(){
        $subjectinfo = new conmmandatacall();
        $institutes = $subjectinfo->institutes;
        $course = $subjectinfo->course;
        $corsename = $subjectinfo->coursename;
        $semester = $subjectinfo->semester;
        return view('search.institute.datasearchform',compact('course','corsename','semester','institutes'));
    }

    public function coursewiseview(Request $request){
        $table = Subject::where(['course_id'=>$request->exportcourse,'Semester'=>$request->exportsemester])->first();
        $institute = Institute::get();
        
        // diffining empty variables
        $arange = [];
        $totalpass = $totalfail = $totalreappear = $grandtotal = 0;
        
        foreach($institute as $key=>$singleins){
            $data = ResultData::with('course')->where(['institute_id'=>$singleins->InstituteCode,'Stud_batch'=>$request->exportbatch,'Stud_semester'=>$request->exportsemester])->get();
            // store in saprate evaluation variable
            $pass = $data;
            $fail = $data;
            $reappear = $data;
            $total = $data;
            
            $course = Course::where('id',$request->exportcourse)->first();

            // exicuting condition on it
            $passResults = $pass->where('End_Result', 'Pass')->count();
            $failResults = $fail->where('End_Result', 'Fail')->count();
            $reappearResults = $reappear->where('End_Result', 'Reappear')->count();
            $totalResults = $total->count();

            
            // storing data into array
            $arange[$key][] = $singleins->InstituteName;
            $arange[$key][] = $singleins->InstituteCode;
            $arange[$key][] = $course->Course_name;
            $arange[$key][] = $request->exportsemester;
            $arange[$key][] = $totalResults ?? 0;
            $arange[$key][] = $passResults ?? 0;
            $arange[$key][] = $passResults != 0 && $totalResults != 0 ? number_format(($passResults/$totalResults)*100,2) : 0;
            $arange[$key][] = $failResults ?? 0;
            $arange[$key][] = $failResults != 0 && $totalResults != 0 ? number_format(($failResults/$totalResults)*100,2) : 0;
            $arange[$key][] = $reappearResults ?? 0;
            $arange[$key][] = $reappearResults != 0 && $totalResults != 0 ? number_format(($reappearResults/$totalResults)*100,2) : 0;

            // totaling diffrent evaluation variable
            $grandtotal += $totalResults ?? 0;
            $totalpass += $passResults ?? 0;
            $totalfail += $failResults ?? 0;
            $totalreappear += $reappearResults ?? 0;  
        }

        // finding percentage of totaling values
        $totalpasspercent = $totalpass != 0 && $grandtotal != 0 ? number_format($totalpass/$grandtotal*100,2) : 0;
        $totalfailpercent = $totalfail != 0 && $grandtotal != 0 ? number_format($totalfail/$grandtotal*100,2) : 0;
        $totalreappearpercent = $totalreappear != 0 && $grandtotal != 0 ? number_format($totalreappear/$grandtotal*100,2) : 0;

        return view('search.institute.coursewisedata',compact('arange','grandtotal','totalpass','totalpasspercent','totalfail','totalfailpercent','totalreappear','totalreappearpercent'));
    }

    public function subjectwiseview(Request $request){
        $table = Subject::where(['course_id'=>$request->exportcourse,'Semester'=>$request->exportsemester])->select('Subject_code','Mid_pass_mark','End_pass_mark')->orderBy('Subject_code')->get();
        $course = Course::where('id',$request->exportcourse)->first();
        $institute = Institute::get();
        
        // difining empty variables
        $arange = [];
        $heading = [];
        
        // storing tabler header names
        foreach($table as $key2=>$sub){
            $heading[] = $sub->Subject_code;
        }


        foreach($institute as $inskey=>$insdata){
            $data = ResultData::with('course')->where(['course_id'=>$request->exportcourse,'institute_id'=>$insdata->id,'Stud_batch'=>$request->exportbatch,'Stud_semester'=>$request->exportsemester])
            ->where('End_Result','!=',null)
            ->select('Marks_grade')->get();
            
            $total = $data;
            
            $arange = [];
            
            // storing comman data into array 
            $arange['info'][] = $insdata->id;
            $arange['info'][] = $insdata->InstituteName;
            $arange['info'][] = $course->Course_name;
            $arange['info'][] = ordinalget($request->exportsemester).' semester';
            $arange['info'][] = $total->count();
            
            $pass = [];
            $fail = [];
            $total = [];
            $passpercentage = [];
            $failpercentage = [];
            foreach($table as $key2=>$sub){
                $pass[$sub->Subject_code] = 0;
                $fail[$sub->Subject_code] = 0;
                $total[$sub->Subject_code] = 0;
            }
            foreach($data as $key=>$single){
                $marks = collect(json_decode($single->Marks_grade));
                foreach($table as $key2=>$sub){
                    if(isset($marks[$sub->Subject_code])){
                        $total[$sub->Subject_code]++;
                        if($marks[$sub->Subject_code] != 'F'){
                            $pass[$sub->Subject_code]++;
                        }else{
                            $fail[$sub->Subject_code]++;
                        }
                    } 
                }
            }

            foreach($table as $key2=>$sub){
                $passpercentage[$sub->Subject_code] = $total[$sub->Subject_code] != 0 ? number_format(($pass[$sub->Subject_code]/$total[$sub->Subject_code])*100,2) : 0;
                $failpercentage[$sub->Subject_code] = $total[$sub->Subject_code] != 0 ? number_format(($fail[$sub->Subject_code]/$total[$sub->Subject_code])*100,2) : 0;
            }
            
            $formatedmarks = [];

            foreach($table as $key2=>$sub){
                $formatedmarks[$sub->Subject_code]['pass'] = $pass[$sub->Subject_code];
                $formatedmarks[$sub->Subject_code]['passpercent'] = $passpercentage[$sub->Subject_code];
                $formatedmarks[$sub->Subject_code]['fail'] = $fail[$sub->Subject_code];
                $formatedmarks[$sub->Subject_code]['failpercent'] = $failpercentage[$sub->Subject_code];
            }
            
            $arange['result'] = $formatedmarks; 
            $institute[$inskey] = $arange;
        }
        return view('search.institute.subjectwisedata',compact('heading','institute'));
    }

    public function failsubjects(Request $request){

        $select=['id','student_id',
        'institute_id',
        'Grand_Total',
        'Marks_credit_point',
        'Total_Percentage',
        'End_Result',
        'End_Result_SGPA',
        'End_Result_CGPA',
        'Optional_subject',
        'Total_Reappear_subject',
        'Mid_marks',
        'End_marks',
        'Marks_total',
        'Marks_grade_point',
        'Marks_grade',
        'Grand_Credit_Point',
        ];

        
        
        $current = ResultData::with('student')->where(['course_id' => $request->exportcourse, 'Stud_semester' => $request->exportsemester,'Stud_batch'=>$request->exportbatch]);
        
        if(isset($request->exportinstitute)){
            $current = $current->where('institute_id',$request->exportinstitute);
        }
        
        $current  = $current->select($select)->get()->toArray();
        // return $current;
        $newdata = [];
        $every = [];

        if(isset($request->subject_codes)){
            $optionalsub = Subject::where(['course_id' => $request->exportcourse, 'Semester' => $request->exportsemester,'Optional_subject'=>true])->select('Subject_code')->get()->toArray();
            
            $updatedSubjects = array_map(function($subject) use ($request) {
                $subject['Subject_code'] = $request->exporttearm.'_' . $subject['Subject_code'];
                return $subject;
            }, $optionalsub);


            
            $totalsearch = count($request->subject_codes);
            
            foreach($current as $key=>$single){
                // return $single;
                $checkcount = 0;
                $removecount = isset($single['Optional_subject']) && in_array('End_'.$single['Optional_subject'],$request->subject_codes) == true ? 1 : 0;
                $midmakr = \jsondecodetoarray($single['Mid_marks']);
                $endmakr = \jsondecodetoarray($single['End_marks']);
                $subject = [];
                $selectarray = $request->exporttearm == 'Mid' ? $midmakr : $endmakr;
                
                foreach($selectarray as $key2=>$check){
                    $currentsub = explode('_',$key2);
                    if(isset($request->subject_codes[$key2]) && ((int) $request->subject_codes[$key2] != $check)){
                        $gkey = $key;
                        break;
                    }else if(isset($request->subject_codes[$key2])){
                        $subject[$currentsub[1]]['mid_marks']= $request->exporttearm == 'Mid' ? $check : $midmakr['Mid_'.$currentsub[1]];
                        if($request->exporttearm === 'End'){
                            $subject[$currentsub[1]]['end_marks']= $check;
                        }
                        $checkcount++;
                    }
                }
                
                if($checkcount == ($totalsearch-$removecount)){
                    $credit = json_decode($single['Marks_credit_point'], true);
                    $info = [
                        'name'=> $single['student']['name'],
                        'NCHMCT_Rollnumber'=> $single['student']['NCHMCT_Rollnumber'],
                        'JNU_Rollnumber'=> $single['student']['JNU_Rollnumber'],
                        'institute_id'=> $single['institute_id'],
                        'Grand_Total'=> $single['Grand_Total'],
                        'Marks_credit_point'=> array_sum($credit),
                        'Total_Percentage'=> $single['Total_Percentage'],
                        'End_Result_SGPA'=> $single['End_Result_SGPA'],
                        'End_Result_CGPA'=> $single['End_Result_CGPA'],
                        'End_Result'=> $single['End_Result'],
                        'Optional_subject'=> $single['Optional_subject'],
                        'Total_Reappear_subject'=> $single['Total_Reappear_subject'],
                    ];
                    // $current[$key] = $single;

                    $newdata[$key]['info'] = $info;
                    $newdata[$key]['data'] = $subject;
                    // return $newdata; 
                }
            }

            $heading['head'] = ['Student Name','NCHM Roll No','JNU Roll No','Institute Code'];
    
            $heading['head'][] = 'Total';
            $heading['head'][] = 'Total Credit Point';
            $heading['head'][] = 'Total Percentage';
            $heading['head'][] = 'Result SGPA';
            $heading['head'][] = 'Result CGPA';
            $heading['head'][] = 'Result';
            $heading['head'][] = 'Optional Subject';
            $heading['head'][] = 'Reappear Subject';
    
            foreach($request->subject_codes as $key=>$singlesubhead){
                $heading['data'][explode('_',$key)[1]] = explode('_',$key)[1];
            }
            
            if(count($newdata) > 0){
                $view = view('search.excel.subjectfail',compact('heading','newdata'));
                return $view;
            }else{
                $errorMessage = 'No Data Found For The Given Data.';
                return response()->json(['message' => $errorMessage], 500);
            }
        }else{
            $errorMessage = 'Please Select atleast one subject.';
            return response()->json(['message' => $errorMessage], 500);
        }
    }


    public function printerData(){
        $subjectinfo = new conmmandatacall();
        $institutes = $subjectinfo->institutes;
        $course = $subjectinfo->course;
        $corsename = $subjectinfo->coursename;
        $semester = $subjectinfo->semester;
        return view('search.excel.printerdata',compact('course','corsename','semester','institutes'));
    }

    public function getprinterData(Request $request){
        $generateresult = ResultData::with(['course','student'])->where([
            'course_id'=>$request->historycourse,
            'Stud_batch'=>$request->historybatch,
            'Stud_semester'=>$request->historysemester,
        ])->get()->toArray();

        $subjects = Subject::where([
            'course_id'=>$request->historycourse,
            'Semester'=>$request->historysemester,
        ])->orderby('Subject_code')->get();
            
        if($request->exportinstitute){
            $generateresult = array_filter($generateresult,function($data) use ($request){
                return $data['institute_id'] == $request->exportinstitute;
            });
            
            if(count($generateresult) === 0){
                $errorMessage = 'Institute Not Found!';
                return response()->json(['message' => $errorMessage], 500);
            }
        }
        
        if(count($generateresult) > 0){
            $results = $generateresult = array_filter($generateresult,function($data){
                return 'End_Result_SGPA' != null && 'End_Result_CGPA' != null;
            });
            
            if(count($results) > 0){
                
                $results = resultfiltaring($results);

                $subject = [];

                $cumulative = checkEven($request->historysemester) === true ? 'CUMULATIVE RECORD (CUMULATIVE GRADE POINT AVERAGE (C.G.P.A))' : 'CUMULATIVE RECORD (SEMESTER GRADE POINT AVERAGE (S.G.P.A))';
                
                $headingstart = [
                    'Student Name'=>2,
                    'Student NCHM Rollnumber'=>2,
                    'Student JNU Rollnumber'=>2,
                    'Programme of Study'=>2,
                    'Academic Chapter Code'=>2,
                    'Semester'=>2,
                    'Academic Session'=>2,
                    'CURRENT SEMESTER RECORD (SEMESTER GRADE POINT AVERAGE (S.G.P.A))'=>2,
                    $cumulative=>2,
                    'Total Valid Credit Earned'=>2,
                ];
                
                foreach($subjects as $main){
                    $subject[$main->Subject_code] = 4;
                }
                
                $headingend = [
                'Current Semester Record'=>2,
                'Cumulative Record'=>2];
                
                $subjectinner = [
                    'Course Code','Course Title','Credit','Grade'
                ];

                $headingendinner = [
                    'Total Credits','Total Points'
                ];

                $view = view('components.printerdata',compact('results','headingstart','subject','headingend','subjectinner','headingendinner'));

                return $view;
            }else{
                $errorMessage = 'Result not generated yet!';
                return response()->json(['message' => $errorMessage], 500);
            }

        }else{
            $errorMessage = 'Result not Found!';
            return response()->json(['message' => $errorMessage], 500);
        }
    }

    public function exportprinterData(Request $request){
        try{
            $tb = Subject::with('Course')->where([
                'course_id'=>$request->historycourse,
                'Semester'=>$request->historysemester,
            ])->orderby('Subject_code')->first();
                
            $filename = ucwords($tb->Course->Course_name).'_'.$request->historysemester.'('.$request->historybatch.')ExportPrintResult.xlsx';    
            return Excel::download(new PrintExport($request->historycourse,$request->historybatch,$request->historysemester,$request->exportinstitute),$filename);
        }catch(\Exception $e){
            return back()->withErrors('Please Provide Valid File and Details');
        }
    }
    
}