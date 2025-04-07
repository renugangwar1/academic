<?php

namespace App\Exports;

use App\Models\DiplomaResult;
use Maatwebsite\Excel\Concerns\FromCollection;

class DiplomaDataExport implements FromCollection
{
    protected $course,$batch,$semester,$institute;

    public function __construct($course,$batch,$semester,$institute)
    {
        $this->course = $course;
        $this->batch = $batch;
        $this->semester = $semester;
        $this->institute = $institute;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $startarry = ['student_id', 'Stud_batch', 'Stud_semester', 'Stud_academic_year', 'institute_id',
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

        $query = DiplomaResult::with('student:id,name,NCHMCT_Rollnumber','JNU_Rollnumber')->where([
            'course_id' => $this->course,
            'Stud_batch' => $this->batch,
            'Stud_semester' => $this->semester,
        ]);

        if ($this->institute) {
            $query->Where('institute_id', $this->institute);
        }
        
        $results = $query->select($startarry)->get();

        return $results;
    }

    public function headings(): array
    {

        // // $getsubjects = Subject::where(['course_id'=>$this->course,'Semester'=>$this->semester])->orderBy('Subject_code')->get();

        // $startarry = ['Student Name', 'Student NCHM_Roll_Number', 'Student JNU_Roll_Number', 'Student Course', 'Student Batch', 'Student Semester', 'Student Academic_Year', 'Student Institute Id',
        //     'Mid_marks',
        //     'End_marks',
        //     'Marks_total',
        //     'Marks_grade',
        //     'Marks_grade_point',
        //     'Marks_credit',
        //     'Marks_credit_point',
        //     'Grand_Total',
        //     'Total Credit Point',
        //     'Percentage',
        //     'Result',
        //     'Result SGPA',
        //     'Result CGPA',
        //     'Reappear Subject'
        // ];

        return [];
    }
}
