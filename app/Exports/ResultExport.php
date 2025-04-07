<?php

namespace App\Exports;

use App\Models\ResultData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Subject;

class ResultExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    protected $course,$batch,$semester,$institute;

    public function __construct($course,$batch,$semester,$institute)
    {
        $this->course = $course;
        $this->batch = $batch;
        $this->semester = $semester;
        $this->institute = $institute;
        
        $this->subjects = $subjects = Subject::where(['course_id'=>$course,'Semester'=>$semester])->orderBy('Subject_code')->get();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // $db = Subject::where(['course_id'=>$this->course,'Semester'=>$this->semester])->get();

        // $totalsubject = (count($db) - (count($db->where('Optional_subject',true))/2));

        // $checkhalf = number_format($totalsubject/2);

        $startarry = ['student_id',
                        'course_id',
                        'Stud_batch',
                        'Stud_semester',
                        'Stud_academic_year',
                        'institute_id',
                        'Mid_marks',
                        'End_marks',
                        'Marks_total',
                        'Marks_grade',
                        'Marks_grade_point',
                        'Marks_credit',
                        'Marks_credit_point',
                        'Grand_Total',
                        'Grand_Credit_Point',
                        'Total_Percentage',
                        'End_Result',
                        'End_Result_SGPA',
                        'End_Result_CGPA',
                        'Total_Reappear_subject'
                    ];

        $query = ResultData::with(['course','student'])->where([
            'course_id' => $this->course,
            'Stud_batch' => $this->batch,
            'Stud_semester' => $this->semester,
        ]);

        // if(checkEven($this->semester) != true){
        //     $query = $query->where('Reappear_subject_count','<',$checkhalf);
        // }else{
        //     $query = $query->where('Reappear_subject_count','!=',$totalsubject);
            
        //     if($query->count() == 0){
        //         return back()->withErrors('This Student Result Not Clear!');
        //     }
        // }

        if ($this->institute) {
            $query->Where('institute_id', $this->institute);
        }
        
        $results = $query->select($startarry)->get();
        
        foreach($results as $key=>$single){
            $temp = [];
            $temp[] = $single['student']['name'] ?? '';
            $temp[] = $single['student']['NCHMCT_Rollnumber'] ?? '';
            $temp[] = $single['student']['JNU_Rollnumber'] ?? '';
            $temp[] = $single['course']['Course_name'] ?? '';
            $temp[] = $single['Stud_batch'] ?? '';
            $temp[] = $single['Stud_semester'] ?? '';
            $temp[] = $single['Stud_academic_year'] ?? '';
            $temp[] = $single['institute_id'] ?? '';
            
            $midtearm = (array)$midtearm = json_decode($single['Mid_marks']);
            $endtearm = (array)$endtearm = json_decode($single['End_marks']);
            $Total = (array)$Total = json_decode($single['Marks_total']);
            $gradePoint = (array)$gradePoint = json_decode($single['Marks_grade_point']);
            $creditPoint = (array)$creditPoint = json_decode($single['Marks_credit_point']);
            $grade = (array)$grade = json_decode($single['Marks_grade']);
            
            foreach($this->subjects as $subject){
                $temp[] = $midtearm['Mid_'.$subject->Subject_code] ?? '';
                $temp[] = $endtearm['End_'.$subject->Subject_code] ?? '';
                $temp[] = $Total[$subject->Subject_code] ?? '';
                $temp[] = $grade[$subject->Subject_code] ?? '';
                $temp[] = $gradePoint[$subject->Subject_code] ?? '';
                $temp[] = $creditPoint[$subject->Subject_code] ?? '';
            }

            $temp[] = $single['Grand_Total'] ?? '';
            $temp[] = $single['Grand_Credit_Point'] ?? '';
            $temp[] = $single['Total_Percentage'] ?? '';
            $temp[] = $single['End_Result'] ?? '';
            $temp[] = $single['End_Result_SGPA'] ?? '';
            $temp[] = $single['End_Result_CGPA'] ?? '';
            $temp[] = $single['Total_Reappear_subject'] ?? '';

            $results[$key] = $temp;
            
        }

        return collect($results);
    }

    public function headings(): array
    {

        $startarry = ['Student Name',
            'Student NCHM_Roll_Number',
            'Student JNU_Roll_Number',
            'Student Course',
            'Student Batch',
            'Student Semester',
            'Student Academic_Year',
            'Student Institute Id',
        ];

        foreach($this->subjects as $subject){
            $startarry[] = 'Mid'.$subject->Subject_code;
            $startarry[] = 'End'.$subject->Subject_code;
            $startarry[] = 'Total'.$subject->Subject_code;
            $startarry[] = 'Grade'.$subject->Subject_code;
            $startarry[] = 'GradePoint'.$subject->Subject_code;
            $startarry[] = 'CreditPoint'.$subject->Subject_code;
        }

        $startarry = array_merge($startarry,[
            'Grand_Total',
            'Total Credit Point',
            'Percentage',
            'Result',
            'Result SGPA',
            'Result CGPA',
            'Reappear Subject'
        ]);

        return $startarry;
    }
}
