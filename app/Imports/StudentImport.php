<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;

use App\Models\Institute;
use App\Models\Course;
use App\Models\StudentHistory;
use App\Models\ResultData;
use App\Models\ResultDataBackup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\row;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\CustomeMessageDisplay;

class StudentImport implements ToModel,WithStartRow,WithHeadingRow,WithValidation
{
    public $importedIds = [];

    protected $hashedPassword,$clintip,$studentlist,$courselist,$institutelist;

    public function __construct($studentlist,$courselist,$institutelist)
    {
        $this->studentlist = $studentlist;
        $this->courselist = $courselist;
        $this->institutelist = $institutelist;
        $this->hashedPassword = Hash::make('123456');
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
        
        $check = array_filter($this->studentlist,function($data) use ($row){
            return (string) $data['NCHMCT_Rollnumber'] === (string) $row['student_nchm_roll_number'];
        });

        $check = reset($check);
        if($check){
            if($check['batch'] != $row['student_batch']){
                try {
                    // Update the $check array
                    $check['student_id'] = $check['id'];
                    $check['system'] = $this->clintip;
                    $check['optionalSubject'] = \json_encode($check['optionalSubject']);
                    unset($check['id']);
                    
                    // Create a new StudentHistory record
                    $history = StudentHistory::create($check);
                    // Find the Student record by student_id and delete it

                    if($history){
                        $data = ResultData::where('student_id',$check['student_id']);
                        if($data->count() != 0){
                            $data = $data->first()->toArray();
                            unset($data['id']);
                            $insert = ResultDataBackup::insert($data);
                            if($insert){
                                $update = ResultDataBackup::where('student_id',$check['student_id'])->update([
                                    'student_id'=>null,
                                    'result_data_id'=>null,
                                    'student_history_id'=>$history->id,
                                ]);
                                ResultData::where('student_id',$check['student_id'])->forceDelete();  
                            }
                        }
                        $student = Student::where('NCHMCT_Rollnumber',$check['NCHMCT_Rollnumber'])->forceDelete();
                    }   

                } catch (\Exception $e) {
                    // Handle any other exceptions that may occur
                    return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
                }
            }else if($check['batch'] === $row['student_batch'] && $check['JNU_Rollnumber'] != $row['student_jnu_roll_number']){
                Student::where('NCHMCT_Rollnumber',$check['NCHMCT_Rollnumber'])->update([
                    'JNU_Rollnumber'=>$row['student_jnu_roll_number'],
                ]);
                $this->importedIds[] = $check['id'];
                return;
            }else{
                return;
            }
        }  
            
        if(isset(Auth::guard('institute')->user()->id) && Auth::guard('institute')->user()->id != substr($row['student_nchm_roll_number'], 4, 3)){
            throw new CustomeMessageDisplay('NCHMCT Roll Number does not belong to the current login institute!');
        }
        
        $institutevalue = Auth::guard('institute')->user()->id ?? substr($row['student_nchm_roll_number'], (substr($row['student_nchm_roll_number'], 0, 1) === 'R' ? 5 : 4), 3);
        
        $checkinst = array_filter($this->institutelist,function($data) use ($institutevalue){
            return (string) $data['InstituteCode'] === (string) $institutevalue;
        });

        $checkinst = reset($checkinst);

        if(!$checkinst){
            throw new CustomeMessageDisplay("Institute Code {$institutevalue} does not exist!");
        }

        if(!$row['student_jnu_roll_number']){
            throw new CustomeMessageDisplay("( {$row['student_nchm_roll_number']} ) Does not contain JNU Roll Number. Please check before uploading the Excel file.");
        }

        $course = array_filter($this->courselist,function($data) use ($row){
            return $data['Course_name'] === $row['student_course'];
        });

        $course = reset($course);

        if(!$course){
            throw new CustomeMessageDisplay("( {$row['student_course']} ) Does not Existes. Please check Course List!");
        }
        

        // dd($row,$this->hashedPassword,$this->clintip,$institutevalue);
        try{
            $data = [
                'name'=>$row['student_name'],
                'NCHMCT_Rollnumber'=>$row['student_nchm_roll_number'],
                'JNU_Rollnumber'=>$row['student_jnu_roll_number'],
                'course_id'=>$course['id'],
                'batch'=>$row['student_batch'],
                'password'=>$this->hashedPassword,
                'system'=>$this->clintip,
                'institute_id'=>$institutevalue,
                'created_at'=> now(),
                'updated_at'=> now(),
            ];

            $studentuplod = Student::create($data);

        }catch (\Exception $e) {
            fixpgSequence('students');
            throw new CustomeMessageDisplay('There is a technical issue, please re-upload your Excel file!');
        }
        
        $this->importedIds[] = $studentuplod->id;

    }

    public function rules(): array
    {
        $primaryrule = ['*.student_name'=>'required',
        '*.student_nchm_roll_number'=>['required'],
        '*.student_batch'=>'required'];

        return $primaryrule;
    }

    public function customValidationMessages()
    {
        $primarymsg = [
        'student_name.required'=>'Please Provide Student Name',
        'student_nchm_roll_number.required'=>'Please Provide NCHM Roll Number',
        'student_batch.required'=>'Please Provide Student Batch'];

        return $primarymsg;
    }
}
