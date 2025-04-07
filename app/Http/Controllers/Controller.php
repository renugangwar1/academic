<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ExcelLog;
use App\Models\Institute;
use App\Models\Notification;
use App\Models\ReappearSetting;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\RefrenceSubjectMaster;
use App\Models\Student;
use App\Models\ResultData;
use App\Models\ResultDataBackup;
use App\Models\User;
use App\Models\TestData;
use GrahamCampbell\ResultType\Success;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use SchemaCheck;
use conmmandatacall;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function coursemaster(){
        $courses = Course::all();
        return view('admin.coursemaster',compact('courses'));
    }

    public function addcours(Request $request){
        $request->validate([
            'course_name'=>'required|string|max:255',
            'min_duration'=>['required','numeric'], //'lt:max_duration'
            'max_duration'=>['required','numeric'], //'gte:min_duration'
            'type'=>['required','in:1,2'],
            'credit'=>['required','numeric'],
        ],[
            'type.required'=>'Course type required',
            'min_duration.numeric'=>'Min Duration should be Number',
            'max_duration.numeric'=>'Max Duration should be Number'
        ]);

        try{
            $entry = Course::updateorcreate([
                'id'=>$request->id ?? null,
             ],[
                'Course_name'=>strtolower($request->course_name),
                'Min_duration'=>$request->min_duration,
                'Max_duration'=>$request->max_duration,
                'Course_type'=>$request->type,
                'Course_credit'=>$request->credit,
                'system'=>$request->ip(),
             ]);
        }catch (\Exception $e) {
            // fixpgSequence('courses');
            return back()->withError('There Is some issue please try again!');
        }
        
        if($entry->wasRecentlyCreated){
            return back()->withSuccess('Course add successfully!');
        }else{
            return back()->withSuccess('Course updated successfully!');
        }
    }

    public function deletecourse(Request $request){
        Course::where('id',$request->id)->delete();
        return back()->withsuccess('Course Delete Successfully!');
    }
    
    public function subjectmaster(){
        $subjects = Subject::with('Course')->get();
        $courses = Course::all();
        return view('admin.subjectmaster',compact('courses','subjects'));
    }

    public function addsubject(Request $request){
        $request->merge([
            'Subject_code' => strtoupper(conmmandatacall::SubjectCodeCheck($request->input('Subject_code'))),
        ]);

        $request->validate([
            'cours_code' => 'required|exists:courses,id',
            'semester' => 'required|string|max:255',
            'subject_name' => 'required|string|max:255',
            'Subject_code' => ['required','string','max:255',Rule::unique('subjects', 'Subject_code')->ignore($request->id ?? null)],
            'mid_max_mark' => 'required|numeric',
            'mid_pass_mark' => 'required|numeric',
            'end_max_mark' => 'required|numeric',
            'end_pass_mark' => 'required|numeric',
            'type_name' => 'required|string|in:practical,theory',
            'credit' => 'required|numeric',
            'Reappear_fee' => 'nullable|numeric',
            'it_status'=>'required',
        ], [
            'cours_code.required' => 'The course code field is required.',
            'cours_code.exists' => 'The selected course code is invalid.',
            'semester.required' => 'The semester field is required.',
            'semester.string' => 'The semester field must be a string.',
            'semester.max' => 'The semester field may not be greater than 255 characters.',
            'subject_name.required' => 'The subject name field is required.',
            'subject_name.string' => 'The subject name field must be a string.',
            'subject_name.max' => 'The subject name field may not be greater than 255 characters.',
            'Subject_code.required' => 'The subject code field is required.',
            'Subject_code.string' => 'The subject code field must be a string.',
            'Subject_code.max' => 'The subject code field may not be greater than 255 characters.',
            'mid_max_mark.required' => 'The mid max mark field is required.',
            'mid_max_mark.numeric' => 'The mid max mark field must be a number.',
            'mid_pass_mark.required' => 'The mid pass mark field is required.',
            'mid_pass_mark.numeric' => 'The mid pass mark field must be a number.',
            'end_max_mark.required' => 'The end max mark field is required.',
            'end_max_mark.numeric' => 'The end max mark field must be a number.',
            'end_pass_mark.required' => 'The end pass mark field is required.',
            'end_pass_mark.numeric' => 'The end pass mark field must be a number.',
            'it_status.required' => 'It Status must be selected',
            'credit.numeric' => 'The credit field must be a number.',
            'type_name.string' => 'Subject Type must be a selected.',
            'type_name.in' => 'The selected type is invalid. It must be practical or theory.',
            'Reappear_fee.numeric' => 'The reappear fee field must be a number.',
        ]);

        try{
            $entry = Subject::updateorcreate([
                'id'=>$request->id ?? null,
            ],[
                'course_id'=>$request->cours_code,
                'Subject_code'=>implode('_',preg_split('/[-=\/.:]/',$request->Subject_code)),
                'Subject_name'=>strtolower($request->subject_name),
                'Subject_type'=>$request->type_name ?? null,
                'Semester'=>$request->semester,
                'Reappear_fee'=>$request->Reappear_fee ?? null,
                'Optional_subject'=>$request->optional_subject ?? 0,
                'Credit'=>$request->credit,
                'Mid_max_mark'=>$request->mid_max_mark,
                'Mid_pass_mark'=>$request->mid_pass_mark,
                'End_max_mark'=>$request->end_max_mark,
                'End_pass_mark'=>$request->end_pass_mark,
                'It_status'=>$request->it_status ?? 0,
                'system'=>$request->ip(), 
            ]);
        }catch (\Exception $e) {
            fixpgSequence('subjects');
            return back()->withError('There Is some issue please try again!');
        }


        if($entry->wasRecentlyCreated){
            return back()->withSuccess('Subject add successfully!');
        }else{
            return back()->withSuccess('Subject updated successfully!');
        }
    }

    public function deletesubject(Request $request){
        Subject::where('id',$request->id)->delete();
        return back()->withSuccess('Subject Deleted SuccessFully!');
    }





    // insitutemaster///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function institutemaster(){
        $institutes = Institute::all();
        return view('admin.institutemaster',compact('institutes'));
    }

    public function updateorcreateinstitute(Request $request){
        $request->merge([
            'email' => $request->input('email'),
        ]);

        $validate = $request->validate([
            'institute_code'=>'required',
            'institute_name'=>'required',
            'email'=>['required', 'string', 'email', 'max:255' ,Rule::unique('institutes', 'email')->ignore($request->id ?? null)]
        ], [
            'institute_code.required' => 'The institute code field is required.',
            'institute_name.required' => 'The institute name field is required.',
        ]);

        if($request->id == null){
            $istcheck = Institute::where('InstituteCode',$request->institute_code)->count();
            if($istcheck != 0){
                return back()->withErrors('Institute Already Existed!');
            }
        }

        if($validate){
            try{
                $entry = Institute::updateOrCreate([
                    'id'=>$request->id ?? null,
                ],[
                    'id'=>$request->institute_code,
                    'InstituteName'=>strtolower($request->institute_name),
                    'InstituteCode'=>$request->institute_code,
                    'email'=>$request->email,
                    'password'=>Hash::make('123456inst'),
                    'system'=>$request->ip(),
                ]);
            }catch (\Exception $e) {
                // fixpgSequence('institutes');
                return back()->withError('There Is some issue please try again!');
            }
            
        }
        
        if($entry->wasRecentlyCreated){
            return back()->withSuccess('Institute add successfully!');
        }else{
            return back()->withSuccess('Institute updated successfully!');
        }
    }

    // try{
    //     $entry = Institute::updateOrCreate([
    //         'id'=>$insid ?? null,
    //     ],[
    //         'id'=>$request->institute_code,
    //         'InstituteName'=>strtolower($request->institute_name),
    //         'InstituteCode'=>$request->institute_code,
    //         'email'=>$request->email,
    //         'password'=>Hash::make('123456inst'),
    //         'system'=>$request->ip(),
    //     ]);
    // }catch (\Exception $e) {
    //     // fixpgSequence('institutes');
    //     return back()->withError('There Is some issue please try again!');
    // }

    // 'id'=>$request->institute_code,

    public function deleteinstitute(Request $request){
        Institute::where('id',$request->id)->delete();
        return back()->withSuccess('Institute remove successfully!');
    }


    public function bgcolorchange(Request $request){
        Setting::updateOrCreate([
            'id'=>1,
        ],[
            'bg_color'=>$request->bgchangecolor,
        ]);
        return ('Background Color Changed Successfully!');
    }

    public function users(){
        $users = User::with('institute')->where('role','!=',3)->get();
        return view('admin.users',compact('users'));
    }

    public function updateorcreateuser(Request $request){

        if(isset($request->ins_code)){
            $find = Institute::where('Institute_code',$request->ins_code)->first();
            if(!isset($find)){
                return back()->withErrors("Institute that doesn't exist");
            }
        }

        if($request->accessmenu){
            $list = '';
            foreach($request->accessmenu as $key=>$ind){
                $list = $list.($key !== 0 ? ',' : '').$ind;
            }
        }
        
        $request->validate([
            'user_name'=>'required',
            'user_email'=>'required',
            'user_pass'=>'required',
        ], [
            'user_name.required' => 'User Name is required.',
            'user_email.required' => 'Email is required.',
            'user_pass.required' => 'Password is required.',
        ]);

        try{
            $entry = User::updateorcreate([
                'id'=>isset($request->id) ? $request->id : null,
            ],[
                'name'=>$request->user_name,
                'email'=>$request->user_email,
                'role'=>isset($request->user_role) ? $request->user_role : 0,
                'menu_access'=>isset($list) ? $list : null,
                'password'=>Hash::make($request->user_pass),
            ]);
        }catch (\Exception $e) {
            // fixpgSequence('users');
            return back()->withError('There Is some issue please try again!');
        }
        
        if(isset($request->ins_code)){
            Institute::where('Institute_code',$request->ins_code)->update([
                'user_id'=>$entry->id
            ]);
        }

        if($entry->wasRecentlyCreated){
            return back()->withSuccess('New User Created Successfully!');
        }else{
            return back()->withSuccess('User updated Successfully!');
        }
    }

    public function userdelete(Request $request){
        User::where('id',$request->id)->delete();
        return back()->withSuccess('User Remove Successfully!');
    }

    public function excelLog(){
        $inscode = Auth::guard('institute')->user()->id ?? null;
        $excels = ExcelLog::with('user')->orderby('created_at','desc')->get();
        if($inscode){
            $excels = $excels->where('uploader_id',$inscode);
        }
        return view('admin.excellogs',compact('excels'));
    }

    public function settings(){
        $setting = Setting::where('id',2)->first();
        
        $subjectinfo = new conmmandatacall();
        $institutes = $subjectinfo->institutes;
        $course = $subjectinfo->course;
        $corsename = $subjectinfo->coursename;
        $semester = $subjectinfo->semester;

        $Reappeardata = ReappearSetting::with('Course')->get();
        $notification = Notification::all();
        return view('admin.setting',compact('setting','course','corsename','semester','Reappeardata','notification'));
    }

    public function updatesettings(Request $request){
        $validator = $request->validate([
            'poD'=>'required',
            'pcD'=>'required',
        ], [
            'poD.required' => 'Opening Date Required.',
            'pcD.required' => 'Closing Date Required.',
        ]);

        Setting::updateorcreate([
            'id'=>2,
        ],[
            'opening_date'=>$request->poD,
            'closing_date'=>$request->pcD,
        ]);

        return ('Setting Update Successfully.');
    }

    public function Reappearsetting(Request $request){
        // dd($request);
        $request->validate([
            'course'=>'required',
            'batch'=>'required',
            'semester'=>'required',
            'Reappear_from_date'=>['required','before:Reappear_to_date'],
            'Reappear_to_date'=>['required','after:Reappear_from_date'],
            'Reappear_late_fee_date'=>['required','after:Reappear_to_date'],
            'Reappear_late_fee'=>'required',
        ], [
            'course.required' => 'Please Select Course',
            'batch.required' => 'Please Select Batch',
            'semester.required' => 'Please Select Semester',
            'Reappear_from_date.required' => 'Rrequired Reappear From Date',
            'Reappear_from_date.before' => 'Reappear start Date is less then end date.',
            'Reappear_to_date.required' => 'Rrequired Reappear To Date',
            'Reappear_to_date.after' => 'Reappear End Date Should be after Start date.',
            'Reappear_late_fee_date.required' => 'Rrequired Reappear Late Fee Date',
            'Reappear_late_fee_date.after' => 'Rrequired Reappear Late Fee Date Should be after End Date',
            'Reappear_late_fee.required' => 'Rrequired Reappear Late Fee Amount',
        ]);

        $check = ReappearSetting::where(['course_id'=>$request->course,'semester'=>$request->semester,'batch'=>$request->batch])->where('Reappear_late_fee_date','>=',date('Y-m-d'))->first();
        
        if($check){
            return back()->withErrors('The Reappear Form process is ongoing and the last date for late fee submission is: '.date('d-m-Y',strtotime($check->Reappear_late_fee_date)));
        }

        try{
            $entry = ReappearSetting::updateorcreate([
                    'id'=>$request->id ?? null,
                ],[
                    'course_id'=>$request->course,
                    'semester'=>$request->semester,
                    'batch'=>$request->batch,
                    'Reappear_from_date'=>$request->Reappear_from_date,
                    'Reappear_to_date'=>$request->Reappear_to_date,
                    'Reappear_late_fee_date'=>$request->Reappear_late_fee_date,
                    'Reappear_late_fee'=>$request->Reappear_late_fee,
                    'system'=>$request->ip(),
                ]);
        }catch (\Exception $e) {
            // fixpgSequence('reappear_settings');
            return back()->withError('There Is some issue please try again!');
        }
        
        if($entry->wasRecentlyCreated){
            return back()->withSuccess('Reappear Form Add Successfully!');
        }else{
            return back()->withSuccess('Reappear Form updated Successfully!');
        }
    }

    public function deleteReappearsetting(Request $request){
        ReappearSetting::where('id',$request->id)->delete();
        return back()->withSuccess('Reappear Form Delete Successfully!');
    }

    public function notification(Request $request){
        $request->validate([
            'Ntitle'=>'required',
            'Nformdate'=>['required','before:Ntodate'],
            'Ntodate'=>['required','after:Nformdate'],
        ], [
            'Ntitle.required' => 'Title Required!',
            'Nformdate.required' => 'Rrequired Notification Start Date',
            'Nformdate.before' => 'Notification start Date is less then end date.',
            'Ntodate.required' => 'Rrequired Notification End Date',
            'Ntodate.after' => 'Notification End Date Should be after Start date.',
        ]);

        try{
            $entry =  Notification::updateorcreate([
                'id'=>$request->id ?? null,
            ],[
                'Ntitle'=>$request->Ntitle,
                'Nslug'=>Str::slug($request->Ntitle),
                'Nfor'=>$request->Ntype,
                'Nfrom_date'=>$request->Nformdate,
                'Nto_date'=>$request->Ntodate,
                'Nlink'=>$request->Nlink ?? null,
                'system'=>$request->ip(),
            ]);
        }catch (\Exception $e) {
            // fixpgSequence('notifications');
            return back()->withError('There Is some issue please try again!');
        }
        
        if($entry->wasRecentlyCreated){
            return back()->withSuccess('Notification Add Successfully!');
        }else{
            return back()->withSuccess('Notification updated Successfully!');
        }
    }

    public function deletenotification(Request $request){Notification::where('id',$request->id)->delete(); return back()->withSuccess('Notification Delete Successfully!');}

    public function students(){
        $students = Student::with('course')->get();
        $ins = null;
        if(Auth::guard('institute')->user()){
            $ins = Auth::guard('institute')->user();
            $students = $students->where('institute_id',Auth::guard('institute')->user()->id);
        }
        return view('admin.student',compact('ins','students'));
    }

    public function uploadstudentlist(){
        $incomingdata = session('data') ? session('data') : null;
        $updatedata = session('updatedata') ? session('updatedata') : null;
        
        if(isset($updatedata) && count($updatedata) != 0){
            $heading = $updatedata->first()->optionalSubject;
        }else{
            $heading = null;
        }

        $subjectinfo = new conmmandatacall();
        $course = $subjectinfo->course;
        $corsename = $subjectinfo->coursename;
        
        return view('admin.uploadstudent',compact('incomingdata','updatedata','heading','course','corsename'));
    }

    public function deletestudent(Request $request){
        $info = Student::where('id',$request->id)->first();
        $info->delete();
        return back()->withSuccess('Student ('.$info->name.') ('.$info->rollnumber.') Delete Successfully!');
    }

    public function itstudentList(){
        $subjectinfo = new conmmandatacall();
        $institutes = $subjectinfo->institutes;
        $course = $subjectinfo->course;
        $corsename = $subjectinfo->coursename;
        $semester = $subjectinfo->semester;

        return view('admin.itStudentList',compact('course','corsename','semester','institutes'));
    }

    // public function searchforitstudent(Request $request){
    //     $itstudent = ResultData::with('student')->where([
    //         'course_id'=>$request->exportcourse,
    //         'Stud_batch'=>$request->exportbatch,
    //         'Stud_semester'=>4,
    //     ])->get()->map(function ($result) {
    //         return [
    //             'id' => $result->id,
    //             'student_id' => $result->student_id,
    //             'course_id' => $result->course_id,
    //             'Stud_batch' => $result->Stud_batch,
    //             'Stud_semester' => $result->Stud_semester,
    //             'institute_id' => $result->institute_id,
    //             'created_at' => $result->created_at,
    //             'name' => $result->student->name,
    //             'NCHMCT_Rollnumber' => $result->student->NCHMCT_Rollnumber,
    //             'JNU_Rollnumber' => $result->student->JNU_Rollnumber,
    //         ];
    //     });

    //     if($request->exportinstitute){
    //         $itstudent = $itstudent->where('institute_id',$request->exportinstitute);
    //     }

    //     $itstudent = $itstudent;

    //     $view = view('search.IT.search',compact('itstudent'));

    //     return $view;
    // }

    // public function transfertoitstudents(Request $request){
    //     $secound = conmmandatacall::fatchdataforIT($request->exportcourse,$request->exportbatch,2)->where('End_Result', '!=', 'Fail')->where('End_Result_CGPA','>=','3')->get()->map(function ($result) {
    //         return [
    //             'id' => $result->id,
    //             'student_id' => $result->student_id,
    //             'course_id' => $result->course_id,
    //             'Stud_batch' => $result->Stud_batch,
    //             'Stud_semester' => $result->Stud_semester,
    //             'institute_id' => $result->institute_id,
    //             'End_Result' => $result->End_Result,
    //             'End_Result_CGPA' => $result->End_Result_CGPA,
    //             'Stud_academic_year'=>$result->Stud_academic_year,
    //             'created_at' => $result->created_at,
    //             'name' => $result->student->name,
    //             'NCHMCT_Rollnumber' => $result->student->NCHMCT_Rollnumber,
    //             'JNU_Rollnumber' => $result->student->JNU_Rollnumber,
    //         ];
    //     });
    //     $third = conmmandatacall::fatchdataforIT($request->exportcourse,$request->exportbatch,3)->get()->map(function ($result) {
    //         return [
    //             'id' => $result->id,
    //             'student_id' => $result->student_id,
    //             'course_id' => $result->course_id,
    //             'Stud_batch' => $result->Stud_batch,
    //             'Stud_semester' => $result->Stud_semester,
    //             'institute_id' => $result->institute_id,
    //             'End_Result' => $result->End_Result,
    //             'End_Result_CGPA' => $result->End_Result_CGPA,
    //             'Stud_academic_year'=>$result->Stud_academic_year,
    //             'created_at' => $result->created_at,
    //             'name' => $result->student->name,
    //             'NCHMCT_Rollnumber' => $result->student->NCHMCT_Rollnumber,
    //             'JNU_Rollnumber' => $result->student->JNU_Rollnumber,
    //         ];
    //     });
    //     $fourth = conmmandatacall::fatchdataforIT($request->exportcourse,$request->exportbatch,4)->get()->map(function ($result) {
    //         return [
    //             'id' => $result->id,
    //             'student_id' => $result->student_id,
    //             'course_id' => $result->course_id,
    //             'Stud_batch' => $result->Stud_batch,
    //             'Stud_semester' => $result->Stud_semester,
    //             'institute_id' => $result->institute_id,
    //             'End_Result' => $result->End_Result,
    //             'End_Result_CGPA' => $result->End_Result_CGPA,
    //             'Stud_academic_year'=>$result->Stud_academic_year,
    //             'created_at' => $result->created_at,
    //             'name' => $result->student->name,
    //             'NCHMCT_Rollnumber' => $result->student->NCHMCT_Rollnumber,
    //             'JNU_Rollnumber' => $result->student->JNU_Rollnumber,
    //         ];
    //     });

        
    //     if(!isset($request->ttype)){
            
    //         $wherenot = $third->pluck('NCHMCT_Rollnumber');
            
    //         $secound = $secound->whereNotIn('NCHMCT_Rollnumber',$wherenot);
            
    //         $fourth = $fourth->whereNotIn('NCHMCT_Rollnumber',$wherenot);

    //         if($fourth->count() > 0){
    //             $first = $fourth->select('NCHMCT_Rollnumber')->toArray();
    //             $comp = $secound->select('NCHMCT_Rollnumber')->toArray();
    //             $compair = conmmandatacall::arraysHaveSameValues($first,$comp);
                
    //             if($compair === true){
    //                 return response()->json([
    //                     'status' => 'error',
    //                     'errors' => 'Students are already in IT'
    //                 ], 422);
    //             }
    //         }

    //         $subjects = Subject::where(['course_id'=>$request->exportcourse,'Semester'=>4])->select('id','Subject_code')->get();
            
    //         $it_subjects = [];
    //         $it_appearsub = '';
    //         $it_dp = [];
            
    //         foreach($subjects as $subject){
    //             $it_subjects[$subject->id] = $subject->Subject_code;
    //             if (!empty($it_appearsub)) {
    //                 $it_appearsub .= ", " . $subject->Subject_code;
    //             } else {
    //                 $it_appearsub = $subject->Subject_code;
    //             }
    //         }

    //         foreach($secound as $key=>$single){
    //             unset($single['id']);
    //             unset($single['End_Result']);
    //             unset($single['End_Result_CGPA']);
    //             $single['Stud_semester']=4;
    //             $single['Stud_academic_year']=$third[0]['Stud_academic_year'];
    //             $single['system']=$request->ip();
    //             $single['created_at']=now();
    //             $single['updated_at']=now();
    //             $it_dp[] = $single;
    //             unset($single['name']);
    //             unset($single['NCHMCT_Rollnumber']);
    //             unset($single['JNU_Rollnumber']);
    //             $secound[$key] = $single;
    //         }

    //         ResultData::insert($secound->toArray());

    //         $itstudent = $it_dp;

    //         $view = view('search.IT.search',compact('itstudent'));
    
    //         return $view;
        
    //     }else{

    //         $secound = $secound->count();
            
    //         $maindata = $third;

    //         $fourth = $fourth->pluck('NCHMCT_Rollnumber');
            
    //         if($maindata->count() != $secound){
    //             return response()->json([
    //                 'status' => 'error',
    //                 'errors' => 'IT students are not in the 3rd semester. Please upload the IT students third semester marks before transferring 3rd semester students to IT.'
    //             ], 422);
    //         }

    //         if($fourth === $secound){
    //             return response()->json([
    //                 'status' => 'error',
    //                 'errors' => 'Students are already in IT'
    //             ], 422);
    //         }

    //         $third = $maindata = $maindata->whereNotIn('NCHMCT_Rollnumber',$fourth);

    //         $subjects = Subject::where(['course_id'=>$request->exportcourse,'Semester'=>4])->select('id','Subject_code');

    //         $it_subjects = [];
    //         $it_appearsub = '';
    //         $it_dp = [];
    //         foreach($subjects as $subject){
    //             $it_subjects[$subject->id] = $subject->Subject_code;
    //             if (!empty($it_appearsub)) {
    //                 $it_appearsub .= ", " . $subject->Subject_code;
    //             } else {
    //                 $it_appearsub = $subject->Subject_code;
    //             }
    //         }

    //         foreach($third as $key=>$single){
    //             unset($single['id']);
    //             unset($single['End_Result']);
    //             unset($single['End_Result_CGPA']);
    //             $single['Stud_semester']=4;
    //             $single['Stud_academic_year']=$third[0]['Stud_academic_year'];
    //             $single['system']=$request->ip();
    //             $single['created_at']=now();
    //             $single['updated_at']=now();
    //             $it_dp[] = $single;
    //             unset($single['name']);
    //             unset($single['NCHMCT_Rollnumber']);
    //             unset($single['JNU_Rollnumber']);
    //             $third[$key] = $single;
    //         }

    //         ResultData::insert($third->toArray());

    //         $itstudent = $it_dp;

    //         $view = view('search.IT.search',compact('itstudent'));
    
    //         return $view;
    //     }
    // }

    // public function itadmitcardprint(Request $request){

    //     // return $request;

    //     $request->validate([
    //         'exportinstitute'=>'required'
    //     ],[
    //         'exportinstitute.required'=>'Please Select Institute to Print Admitcard'
    //     ]);

    //     $maindata = conmmandatacall::fatchdataforIT($request->exportcourse, $request->exportbatch, 4);

    //     if($request->exportinstitute){
    //         $maindata = $maindata->where('institute_id',$request->exportinstitute);
    //     }

    //     // Create two clones of the data
    //     $maindataClone1 = clone $maindata;
    //     $maindataClone2 = clone $maindata;
        
    //     $getacdmitcard = $maindataClone1->select('Stud_batch','Stud_semester','created_at')->distinct('created_at')->get();
        
    //     foreach($getacdmitcard as $key=>$single){
    //         $getacdmitcard[$key]['distinct_count'] = $maindataClone2->where('created_at',$single->created_at)->count();
    //     }

    //     $view = view('search.IT.downloadbatch',compact('getacdmitcard'));

    //     return $view;
    // }

    // public function printitadmicard(Request $request){

    //     $request->validate([
    //         'exportinstitute'=>'required'
    //     ],[
    //         'exportinstitute.required'=>'Please Select Institute to Print Admitcard'
    //     ]);
        
    //     $getacdmitcard = conmmandatacall::fatchdataforIT($request->exportcourse,$request->exportbatch,4);

    //     if($request->exportinstitute){
    //         $getacdmitcard = $getacdmitcard->where('institute_id',$request->exportinstitute);
    //     }
        
    //     $getacdmitcard = $getacdmitcard->where('created_at',$request->created_at)->get()->map(function ($result) {
    //         return [
    //             'id' => $result->id,
    //             'student_id' => $result->student_id,
    //             'course_id' => $result->course_id,
    //             'Stud_batch' => $result->Stud_batch,
    //             'Stud_semester' => $result->Stud_semester,
    //             'InstituteName' => $result->institute->InstituteName,
    //             'End_Result' => $result->End_Result,
    //             'End_Result_CGPA' => $result->End_Result_CGPA,
    //             'Stud_academic_year'=>$result->Stud_academic_year,
    //             'created_at' => $result->created_at,
    //             'name' => $result->student->name,
    //             'NCHMCT_Rollnumber' => $result->student->NCHMCT_Rollnumber,
    //             'JNU_Rollnumber' => $result->student->JNU_Rollnumber,
    //         ];
    //     });

    //     if(count($getacdmitcard) > 0){
    //         $pdf = FacadePdf::loadView('pdf.itpdfadmitcard', compact('getacdmitcard'));

    //         return $pdf->stream();
    //     }else{
    //         return back()->withErrors('No Data Found!');
    //     } 
    // }
}
