<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Facades\Auth;

class StudentExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    protected $requesttype,$course,$batch;

    public function __construct($requesttype,$course,$batch)
    {
        $this->requesttype = $requesttype;
        $this->course = $course;
        $this->batch = $batch;
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $coursename = Course::where('id',$this->course)->first();
        if($this->requesttype === 'new'){
            $students = Student::limit(0)->get(); // This will return an empty collection
            $studentArray = $students->toArray(); // Convert the collection to an array
    
            // Add your custom rows
            $studentArray[] = ['', '','', $coursename->Course_name, $this->batch];
            $studentArray[] = ['', '','', $coursename->Course_name, $this->batch];
            $studentArray[] = ['', '','', $coursename->Course_name, $this->batch];
            
            // Convert back to collection if needed
            $studentCollection = collect($studentArray);
        }else{
            $instcode = Auth::guard('institute')->user()->id ?? 0;
            // 'name','rollnumber','course','batch','optionalSubject'
            $students = Student::where(['batch'=>$this->batch,'course_id'=>$coursename->id]);
            
            if($instcode){
                $students = $students->where('institute_id',$instcode);
            }
            
            $students = $students->get();
            
            $studentArray = [];
            foreach($students as $key=>$val){
                $studentArray[$key] = [$val['name'],$val['NCHMCT_Rollnumber'],$val['institute_id']];
                if(isset($val->optionalSubject)){
                    foreach($val->optionalSubject as $single){
                        $studentArray[$key][] = $single ?? '';
                    }
                }
            }

            // Convert back to collection if needed
            $studentCollection = collect($studentArray);
        }

        return $studentCollection;
    }

    public function headings(): array
    {
        if($this->requesttype === 'new'){
            $startarry = ['Student Name', 'Student NCHM_Roll_Number', 'Student JNU_Roll_Number', 'Student Course','Student Batch'];
        }else{
            $startarry = ['Student Name', 'Student NCHM_Roll_Number','Institute Code'];
            $semesters = Course::where('id',$this->course)->select('Min_duration')->first();
            for($i=1; $i <= ($semesters->Min_duration*2); $i++){
                $startarry[] = 'Semester '.$i;
            }
        }
        return $startarry;
    }
}
