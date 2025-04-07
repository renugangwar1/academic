<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Course;
use App\Models\ResultData;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\CustomeMessageDisplay;
use Illuminate\Support\Facades\DB;

class StudentUpdate implements ToModel,WithStartRow,WithHeadingRow,WithValidation
{

    public $importedIds = [];

    protected $clintip,$studentlist,$subjectlist;

    public function __construct($studentlist)
    {
        $this->studentlist = $studentlist;
        $this->clintip = Request::getClientIp();
    }

    public function startRow(): int
    {
        return 2;
    }

    public function headingRow(): int
    {
        return 1;
    }

    /**
    * @param row $row
    */
    public function model(array $row)
    {
        set_time_limit(0);

        
        $currentstud = array_filter($this->studentlist,function($data) use ($row){
            return (string) $data['NCHMCT_Rollnumber'] === (string) $row['student_nchm_roll_number'];
        });
        
        $currentstud = reset($currentstud);
        
        
        if(!$currentstud){
            return;
        }
        
        unset($row['student_name']); //unset student name before send to foreach loop
        unset($row['student_nchm_roll_number']); //unset student nchm roll number before send to foreach loop
        unset($row['institute_code']); //unset student nchm roll number before send to foreach loop
        
        // dd($currentstud,$row);
        $json = []; // assign a empty array for add optional subject
        
        foreach($row as $key=>$sub){
            if($sub != null){
                $uppercase = strtoupper($sub);
                $getsem = explode('_',$key);
                
                if(!is_numeric($getsem[1])){ throw new CustomeMessageDisplay("Your Excel file not contain Original headings!"); }
                
                $checkcourse = Subject::where(['course_id'=>$currentstud['course_id'],'Semester'=>$getsem[1],'Optional_subject'=>true])->pluck('Subject_code')->toArray();
                
                if(\in_array($uppercase,$checkcourse)){
                    $json[$key] = $uppercase;
                }
            }
        }

        if(count($json) != 0){
            $jsonconvert = json_encode($json);
            
            if(json_encode($currentstud['optionalSubject']) === $jsonconvert){
                return;
            }
            
            $update = Student::where('id', $currentstud['id'])->update([
                'optionalSubject' => $jsonconvert,
                'system' => $this->clintip,
                'updated_at' => now(),
            ]);

            if($update){
                $this->importedIds[] = $currentstud['id'];
            }
        }else{
            throw new CustomeMessageDisplay("Current Course does not have any optional subjects!"); 
        }

        return;
    }

    public function rules(): array
    {
        $primaryrule = ['*.student_name'=>'required',
        '*.student_nchm_roll_number'=>'required'];

        return $primaryrule;
    }

    public function customValidationMessages()
    {
        $primarymsg = ['student_name.required'=>'Please Provide Student Name',
        'student_nchm_roll_number.required'=>'Please Provide NCHM Roll Number'];

        return $primarymsg;
    }
}
