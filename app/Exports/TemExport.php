<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\ResultData;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectMaster;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

class TemExport implements FromCollection,WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
        $this->inscode = Auth::guard('institute')->user();
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $instcode = Auth::guard('institute')->user()->id ?? null;
        
        $data = ResultData::with('student:id,name,NCHMCT_Rollnumber')->where([
            'course_id' => $this->request->tempcourse,
            'Stud_batch'=>$this->request->tempbatch,
            'Stud_academic_year' => $this->request->tempacademicyear,
            'Stud_semester' => $this->request->tempsemester
        ])->select('student_id','Stud_academic_year','Mid_marks','End_marks');
        
        if($instcode){
            $data = $data->where('result_data.institute_id',$instcode);
        }
            
        // dd($data->first());
        
        if($data->count() > 0){
            $sublist = Subject::where(['course_id'=>$this->request->tempcourse,'Semester'=>$this->request->tempsemester])->select('Subject_code','Subject_type')->get();
            $selectTearmMarks = ucfirst($this->request->temptermmarks).'_marks';
            
            if($this->request->temptermmarks == 'end'){
                if($instcode){
                    $sublist = $sublist->where('Subject_type','practical');
                }
            }
            
            $farry = $sublist->map(function($sort){
                return ucfirst($this->request->temptermmarks).'_'.$sort->Subject_code;
            });
            $data = $data->get()->map(function($result) use ($selectTearmMarks,$sublist,$farry,$instcode){
                $subjects = (array) json_decode($result->$selectTearmMarks);
                $numlist = $sublist->mapWithKeys(function($single)use($subjects){
                    $key = ucfirst($this->request->temptermmarks).'_'.$single->Subject_code;
                    return [$key => $subjects[$key] ?? 0];
                });
                
                $newd = [
                    'student_name'=>$result->student->name,
                    'nchmct_rollnumber'=>$result->student->NCHMCT_Rollnumber,
                    'stud_academic_year'=>$result->Stud_academic_year,
                ];
                
                return array_merge($newd,reset($numlist));
            });
            return $data;
        }else{
            $studentlist = Student::where(['course_id'=>$this->request->tempcourse,'batch'=>$this->request->tempbatch])->orderBy('NCHMCT_Rollnumber')
            ->select(
                'name',
                'NCHMCT_Rollnumber',
            );

            if($instcode){
                $studentlist = $studentlist->where('institute_id',$instcode);
            }
                
            $studentlist = $studentlist->get()->map(function ($result) {
                return [
                    'student_name' => $result->name,
                    'nchmct_rollnumber' => $result->NCHMCT_Rollnumber,
                    'stud_academic_year' => $this->request->tempacademicyear,
                ];
            });

            if($studentlist->count() > 0){
                return $studentlist;
            }else{
                return collect([]);
            }
        }
    }

    public function headings(): array
    {
        $instcode = Auth::guard('institute')->user()->id ?? null;
        $subjects = Subject::where(['Semester'=>$this->request->tempsemester,'course_id'=>$this->request->tempcourse])->select('Subject_code','Subject_type')->orderBy('Subject_code')->get();
        
        $start = ['Student Name',
        'Student NCHM_Roll_Number',
        'Student Academic_Year'];
        
        $midterm = $start;
        
        if($this->request->temptermmarks != 'mid'){
            if($instcode){
                $subjects = $subjects->where('Subject_type','practical');
            }
            foreach($subjects as $single){
                $midterm[] = 'End_'.$single->Subject_code;
            }
        }else{
            foreach($subjects as $single){
                $midterm[] = 'Mid_'.$single->Subject_code;
            }
        }
        // end term add 
        $endterm = $midterm;
        // dd($endterm);

        return $endterm;
    }
}
