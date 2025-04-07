<?php

namespace App\Http\Controllers;

use App\Models\TBAdmitCard;
use App\Http\Requests\StoreTBAdmitCardRequest;
use App\Http\Requests\UpdateTBAdmitCardRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Bsc;
use App\Models\Msc;
use App\Models\MscFirst;
use App\Models\ResultData;
use App\Models\DiplomaResult;
use App\Models\Student;
use App\Models\Course;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Dompdf\Options;
use PrintDetails;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Session;

class TBAdmitCardController extends Controller
{
    public function getData(Request $request,$ins){
        if ($request->ajax()) {
            $data = [];
            if($ins != 0){
                $data = Student::with('Course')->where('institute_id',$ins)->latest()->get();
            }else{
                $data = Student::with('Course')->latest()->get();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getexceldata(Request $request,$table){
        $tb = json_decode($table);
        if ($request->ajax()) {
            $getcourse = Course::where('id',$tb[0])->first();
            
            if($getcourse->Course_type == 1){
                $data = ResultData::with('student')->where(['course_id' => $tb[0], 'Stud_batch' => $tb[1], 'Stud_semester' => $tb[2]]);
            }else{
                $data = DiplomaResult::with('student')->where(['course_id' => $tb[0], 'Stud_batch' => $tb[1], 'Stud_semester' => $tb[2]]);
            }
            
            $subjects = Subject::where(['course_id' => $tb[0], 'Semester' => $tb[2]])->get();
            
            if(isset($tb[3])){
                $data = $data->where('institute_id',$tb[3]);
            }
            
            if($getcourse->Course_type == 1){
                $data = $data->select($tb[4])->latest()->orderBy(Student::select('JNU_Rollnumber')
                ->whereColumn('students.id', 'result_data.student_id'))->select('student_id','institute_id','Mid_marks','End_marks','Marks_grade_point','Marks_credit_point','Marks_grade','Grand_Total','Grand_Credit_Point','Total_Percentage','End_Result','End_Result_SGPA','End_Result_CGPA')->get()->toArray();
            }else{
                $data = $data->select($tb[4])->latest()->orderBy(Student::select('NCHMCT_Rollnumber')
                ->whereColumn('students.id', 'diploma_results.student_id'))->select('student_id','institute_id','Mid_marks','End_marks','Grand_Total','Total_Percentage','Result')->get()->toArray();
            }

            
            if($getcourse->Course_type == 1){
                foreach($data as $key=>$single){
                    $single['Stud_name'] = $single['student']['name'];
                    $single['Stud_nchm_roll_number'] = $single['student']['NCHMCT_Rollnumber'];
                    $single['Stud_jnu_roll_number'] = $single['student']['JNU_Rollnumber'];
                    $midtearm = (array)$midtearm = json_decode($single['Mid_marks']);
                    $endtearm = (array)$endtearm = json_decode($single['End_marks']);
                    $gradePoint = (array)$gradePoint = json_decode($single['Marks_grade_point']);
                    $creditPoint = (array)$creditPoint = json_decode($single['Marks_credit_point']);
                    $grade = (array)$grade = json_decode($single['Marks_grade']);
                    $subjectmarks = [];
                    foreach($subjects as $subject){
                        $subjectmarks['Mid'.$subject->Subject_code] = $midtearm['Mid_'.$subject->Subject_code] ?? '';
                        $subjectmarks['End'.$subject->Subject_code] = $endtearm['End_'.$subject->Subject_code] ?? '';
                        $subjectmarks['GradePoint'.$subject->Subject_code] = $gradePoint[$subject->Subject_code] ?? '';
                        $subjectmarks['CreditPoint'.$subject->Subject_code] = $creditPoint[$subject->Subject_code] ?? '';
                        $subjectmarks['Grade'.$subject->Subject_code] = $grade[$subject->Subject_code] ?? '';
                    }
                    unset($single['student_id']);
                    unset($single['student']);
                    unset($single['Mid_marks']);
                    unset($single['End_marks']);
                    unset($single['Marks_grade_point']);
                    unset($single['Marks_credit_point']);
                    unset($single['Marks_grade']);
                    $single = array_merge($single,$subjectmarks);
                    $data[$key] = $single;
                }
            }else{
                foreach($data as $key=>$single){
                    $single['Stud_name'] = $single['student']['name'];
                    $single['Stud_nchm_roll_number'] = $single['student']['NCHMCT_Rollnumber'];
                    $midtearm = (array)$midtearm = json_decode($single['Mid_marks']);
                    $endtearm = (array)$endtearm = json_decode($single['End_marks']);
                    $subjectmarks = [];
                    foreach($subjects as $subject){
                        $subjectmarks['Mid'.$subject->Subject_code] = $midtearm['Mid_'.$subject->Subject_code] ?? '';
                        $subjectmarks['End'.$subject->Subject_code] = $endtearm['End_'.$subject->Subject_code] ?? '';
                    }
                    unset($single['student_id']);
                    unset($single['student']);
                    unset($single['Mid_marks']);
                    unset($single['End_marks']);
                    $single = array_merge($single,$subjectmarks);
                    $data[$key] = $single;
                }
            }

            $data = collect($data);
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function conversion($dat){
        $dat->map(function($item) {
            return array_values((array) $item);
        });

        return $dat;
    }

    public function search(Request $request){
        $validate = $request->validate([
            'course' => 'required',
            'batch' => 'required',
            'semester' => 'required',
            'institute' => 'nullable|required_without:rollno',
            'rollno' => 'nullable|required_without:institute',
        ], [
            'course.required' => 'Please select Course!',
            'batch.required' => 'Please select Batch!',
            'semester.required' => 'Please select Semester!',
            'institute.required_without' => 'Please provide either Institute or Roll Number!',
            'rollno.required_without' => '',
        ]);

        $course = Course::where('id',$request->course)->first();
        
        $print = new PrintDetails($request->course,$request->semester,$request->batch,$request->institute,$request->rollno);
        
        if($request->searchfor === 'admitcard'){
            if($course->Course_type == 1){
                return $print->PrintAdmitCard();
            }else{
                return $print->PrintDiplomaAdmitCard();
            }
        }else{
            if($course->Course_type == 1){
                return $print->PrintResult();
            }else{
                return $print->PrintDiplomaResult();
            }
        }
    }

    public function findsubject(Request $request){
        $find = Subject::where(['course_id'=>$request->course,'Semester'=>$request->semester])->orderBy('Subject_code')->get();
        return $find;
    }

    public function downloadErrors()
    {
        $errors = Session::get('errors')->all();

        // Create a temporary file to write the errors
        $filename = 'errors.txt';
        $file = fopen($filename, 'w');
        foreach ($errors as $error) {
            fwrite($file, $error . PHP_EOL);
        }
        fclose($file);

        // Return the file as a download response
        return response()->download($filename)->deleteFileAfterSend(true);
    }

//     public function testedmitcard(){
//         $datas = DB::table('test_data')
//         ->leftJoin('institutes', 'institutes.id', '=', 'test_data.institute_id')
//         ->whereNull('test_data.end_marks') 
//         ->get()
//         ->toArray();
        
//         $data = array_map(function($data) {
          
//             return [
//                 'InstituteName' => $data->InstituteName,
//                 'NCHMCT_Rollnumber' => $data->Stud_nchm_roll_number,
//                 'name' => $data->Stud_name,
//                 'Stud_academic_year' => '2024-2025', // Should be a string, not a calculation
//                 'Stud_semester' => 1,
//                 'IT_Appear_subject' => $data->subjects,
//             ];
//         }, $datas);

//         $getacdmitcard = $data;

//         // return view('pdfadmitcard',compact('getacdmitcard'));
//         $pdf = FacadePdf::loadView('pdf.itpdfadmitcard', compact('getacdmitcard'));

//         return $pdf->stream();
//     }
// }

public function testedmitcard(Request $request)
{
   
    $semester = $request->semester; 

  
    $datas = DB::table('result_data')
        ->leftJoin('institutes', 'institutes.id', '=', 'result_data.institute_id')
        ->where('result_data.Stud_semester', $semester)
        ->where('result_data.Stud_academic_year', '2024-2025')
        ->whereNull('result_data.End_marks') // result not declared
        ->select(
            'institutes.InstituteName',
            'result_data.Stud_nchm_roll_number',
            'result_data.Stud_name',
            'result_data.subjects',
            'result_data.Stud_semester'
        )
        ->get()
        ->toArray();

   
    $data = array_map(function ($data) {
        return [
            'InstituteName' => $data->InstituteName,
            'NCHMCT_Rollnumber' => $data->Stud_nchm_roll_number,
            'name' => $data->Stud_name,
            'Stud_academic_year' => '2024-2025',
            'Stud_semester' => $data->Stud_semester,
            'IT_Appear_subject' => $data->subjects,
        ];
    }, $datas);

    $getacdmitcard = $data;

   
    $pdf = FacadePdf::loadView('pdf.itpdfadmitcard', compact('getacdmitcard'));
    return $pdf->stream();
}
}