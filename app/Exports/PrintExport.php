<?php

namespace App\Exports;

use App\Models\ResultData;
use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PrintExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $course, $batch, $semester, $institute;

    public function __construct($course, $batch, $semester, $institute)
    {
        $this->course = $course;
        $this->batch = $batch;
        $this->semester = $semester;
        $this->institute = $institute;
    }

    public function collection()
    {
        $generateresult = ResultData::with(['course','student'])
            ->where([
                'course_id' => $this->course,
                'Stud_batch' => $this->batch,
                'Stud_semester' => $this->semester,
            ])->orderBy('institute_id')->get()->toArray();
        
        if ($this->institute) {
            $generateresult = array_filter($generateresult,function($data){
                return $data['institute_id'] == $this->institute;
            });
        }

        $results = array_filter($generateresult,function($data){
            return 'End_Result_SGPA' != null && 'End_Result_CGPA' != null;
        });
        
        // Apply any necessary filtering logic here
        $results = resultfiltaring($results);
        
        $data = [];
        foreach ($results as $result) {
            $row = [
                $result['data']['Stud_name'],
                $result['data']['Stud_nchm_roll_number'],
                $result['data']['Stud_jnu_roll_number'],
                $result['data']['Stud_course'],
                $result['data']['institute_id'],
                $result['data']['Stud_semester'],
                $result['data']['Stud_academic_year'],
                $result['data']['End_Result_SGPA'],
                $result['data']['End_Result_CGPA']
            ];
            
            foreach ($result['subjectarray'] as $subject) {
                $row[] = $subject['coursecode'];
                $row[] = $subject['coursetitle'];
                $row[] = $subject['coursecredit'];
                $row[] = $subject['coursegrade'];
                
            }

            $row[] = $result['currensemrecord']['totalcredit'];
            $row[] = $result['currensemrecord']['totalpoint'];
            
            $row[] = $result['cumulativerecord']['totalcredit'];
            $row[] = $result['cumulativerecord']['totalpoint'];
            
            $row[] = $result['totalcredit'];
            
            $data[] = $row;
        }

        return collect($data);
    }

    public function headings(): array
    {
        $getsubjects = Subject::where([
            'course_id' => $this->course,
            'Semester' => $this->semester,
        ])->orderBy('Subject_code')->get();

        $findopt = $getsubjects->where('Optional_subject',true)->count();
        $total = $getsubjects->count() - ($findopt != 0 ? $findopt/2 : 0);

        $cumulative = checkEven($this->semester) === true ? 'CUMULATIVE RECORD (CUMULATIVE GRADE POINT AVERAGE (C.G.P.A))' : 'CUMULATIVE RECORD (SEMESTER GRADE POINT AVERAGE (S.G.P.A))';

        $headingstart = [
            'Name of Student', 'Enrolment Number', 'Student JNU Rollnumber', 
            'Programme of Study', 'Academic Chapter Code', 'Semester', 
            'Academic Session','CURRENT SEMESTER RECORD (SEMESTER GRADE POINT AVERAGE (S.G.P.A))', $cumulative, 
        ];

        $subjectheadings = [];
        
        for($i = 1; $i <= $total; $i++) {
            $subjectheadings[] = 'Coure Code';
            $subjectheadings[] = 'Coure Title';
            $subjectheadings[] = 'Coure Credit';
            $subjectheadings[] = 'Coure Grade';
        }

        $headingend = ['Current Semester Record (Total Credits)', 'Current Semester Record (Total Points)', 
                        'CUMULATIVE RECORD (Total Credits)', 'CUMULATIVE RECORD (Total Points)',
                        'Total Valid Credit Earned'];

        return array_merge($headingstart, $subjectheadings, $headingend);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
