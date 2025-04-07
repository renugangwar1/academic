<?php

namespace App\Exports;

use App\Models\ResultData;
use App\Models\Subject;
use App\Models\SubjectMaster;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JNUExport implements FromCollection,WithHeadings,ShouldAutoSize,WithStyles
{
    protected $course,$batch,$semester,$institute;

    public function __construct($course,$batch,$semester,$institute)
    {
        $this->course = $course;
        $this->batch = $batch;
        $this->semester = $semester;
        $this->institute = $institute;
        // $this->tb = $tbn;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $getsubjects = Subject::where(['course_id'=>$this->course,'Semester'=>$this->semester])->orderBy('Subject_code')->get();

        $startarry = ['student_id','Marks_grade_point','Marks_grade','Grand_Credit_Point','End_Result_CGPA'];

        $query = ResultData::with('student')->where([
            'course_id' => $this->course,
            'Stud_batch' => $this->batch,
            'Stud_semester' => $this->semester,
        ]);

        
        if ($this->institute) {
            $query->Where('institute_id', $this->institute);
        }
        
        $results = $query->select($startarry)->get()->toArray();

        foreach($results as $key=>$result){
            $result['Stud_nchm_roll_number'] = $result['student']['NCHMCT_Rollnumber'];
            $result['Stud_name'] = $result['student']['name'];
            $subgradepoint = (array)$subgradepoint = json_decode($result['Marks_grade_point']);
            $subgrade = (array)$subgrade = json_decode($result['Marks_grade']);
            foreach($getsubjects as $subject){
                (array)$result[$subject->Subject_code.'_grade_point'] = $subgradepoint[$subject->Subject_code] ?? 0;
                (array)$result[$subject->Subject_code.'_grade'] = $subgrade[$subject->Subject_code] ?? '';
            }
            
            (array)$result['Credit_Point'] = $result['Grand_Credit_Point'];
            (array)$result['Result_CGPA'] = $result['End_Result_CGPA'];
            unset($result['student_id']);
            unset($result['student']);
            unset($result['Marks_grade_point']);
            unset($result['Marks_grade']);
            unset($result['Grand_Credit_Point']);
            unset($result['End_Result_CGPA']);
            
            $results[$key]=$result;
        }
        
        return collect($results);
    }

    public function headings(): array
    {

        $getsubjects = Subject::where(['course_id'=>$this->course,'Semester'=>$this->semester])->orderBy('Subject_code')->get();

        $startarry = ['Enrolment Number', 'Name of Participants'];

        $endarry = $startarry;

        foreach($getsubjects as $single){
            $endarry[] = $single->Subject_code.'_grade_point';
            $endarry[] = $single->Subject_code.'_grade';
        }

        array_push($endarry,'Total Points','CGP');

        return $endarry;
    }


    public function styles(Worksheet $sheet){
        return [
            '1'=>['font'=>['bold'=>true]]
        ];
    }
}
