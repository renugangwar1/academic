<?php

namespace App\Imports;

use App\Models\Institute;
use App\Models\Student;
use App\Models\Subject;
use conmmandatacall;
use Hamcrest\Core\IsNull as CoreIsNull;
use Hamcrest\Type\IsNumeric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\row;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;
use PHPUnit\Framework\Constraint\IsNull;
use App\Models\DiplomaResult;
use App\Models\DiplomaResultBackup;

class DiplomaDataImport implements ToModel,WithStartRow,WithHeadingRow
{
    protected $course,$batch,$semester,$institute,$tb,$term,$totalrow,$excelsubjects,$existchecklist,$studentList;
    public $importedIds = [];
    public $rowentry = [];
    public $newinsert = [];
    public $dpdata = [];
    public $updateinsert = [];
    public $headings = [];
    public $message = [];
    public $errorrow = [];
    public $optionalerror = [];
    public $previusmarkserror = 0;
    public $currentrow = 0;
    public $studentcount = 0;
    public $studentnotexist = [];

    public function __construct($batch,$semester,$institute,$tb,$term,$totalrow,$excelsubjects,$existchecklist,$studentList)
    {
        $this->course = $tb->id;
        $this->batch = $batch;
        $this->semester = (int) $semester;
        $this->institute = Auth::guard('institute')->user() ? Auth::guard('institute')->user()->InstituteCode : $institute;
        $this->tb = $tb;
        $this->term = $term;
        $this->totalrow = $totalrow;
        $this->getsubjects = $excelsubjects;
        $this->existchecklist = $existchecklist;
        $this->studentList = $studentList;
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
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $inscode = Auth::guard('institute')->user()->id ?? null;
        set_time_limit(0);
        
        $this->currentrow++;  //store current row count
        
        $existstudent = array_filter($this->studentList,function($data) use ($row){
            return (int) $data['NCHMCT_Rollnumber'] === (int) $row['student_nchm_roll_number'];
        });

        $existstudent = reset($existstudent);
        
        if(!$existstudent){
            $this->studentnotexist[] = "Student {$row['student_name']} with Roll Number {$row['student_nchm_roll_number']} is not Existed in our Database!";
            return null;
        }else{
            $this->studentcount++;
        }

        $getsubjects = $this->getsubjects; // subjects list in laravel:object formate
        
        $institutevalue = $this->institute ?? substr($row['student_nchm_roll_number'], 4, 3);
        
        $institute = Institute::where('id',$institutevalue)->exists();
        
        if(!$institute){
            flushcache();
            \abort(500,"Institute ({$institutevalue}) does not exist in the database at row {$this->currentrow}");
        }
        
        $check = array_filter($this->existchecklist,function($data) use ($existstudent, $institutevalue){
            return $data['student_id'] === $existstudent['id'] && $data['institute_id'] === (int) $institutevalue && $data['Stud_semester'] === $this->semester;
        });

        $check = reset($check);

        $primary = [ // create array data with basic information for insertion into table.
            'student_id'=>$existstudent['id'],
            'course_id' =>$this->course,
            'Stud_batch' =>$this->batch,
            'Stud_semester' =>$this->semester,
            'Stud_academic_year' =>checkyearformat($row['student_academic_year'],$this->batch),
            'institute_id' =>$institutevalue];

        // primary insert into addon
        $addon = $primary;

        if($this->term === 'mid'){
            
            $addsub = [];
            $addsubstatus = [];
            
            foreach($getsubjects as $single){
                
                $strsublower = strtolower($this->term.'_'.implode('_',preg_split('/[-=\/.:]/',$single['Subject_code']))); // subject variable in lowercase
                
                $strsubupper = 'Mid_'.strtoupper($single['Subject_code']); // subject variable in uppercase
                $maxmark = ucwords($this->term).'_max_mark';// max mark variable for subject
                
                if($single['It_status'] == 1 && isset($row[$strsublower]) && !is_string($row[$strsublower]) && $row[$strsublower] > $single[$maxmark]){// skip entry if the mark more then Max mark
                    flushcache();
                    \abort(500,"Current Subject list contain IT Subject");
                }
                
                $currentsubjectvalue = isset($row[$strsublower]) ? ($row[$strsublower] >= 0 && $row[$strsublower] <= $single[$maxmark] ? $row[$strsublower] : 'AB') : 'AB'; // assign mark of current subject
                
                $addsub[$strsubupper] = $currentsubjectvalue ? $currentsubjectvalue : 0;
                $addsubstatus[$single['Subject_code']] = $currentsubjectvalue != 'AB' ? 'Present' : 'Absent';
                
            }
            $addon['Mid_marks'] = json_encode($addsub);
            $addon['Mid_apear_status'] = json_encode($addsubstatus);
            
        }else if($this->term === 'end')
        {

            if(!$check['Mid_marks']){
                flushcache();
                \abort(500, "Mid Tearm Marks of {$row['student_nchm_roll_number']} Not Avilable!");
            }

            $addsub = [];
            $addsubstatus = [];
            
            foreach($getsubjects as $single){
                
                $strsublower = strtolower($this->term.'_'.implode('_',preg_split('/[-=\/.:]/',$single['Subject_code']))); // subject variable in lowercase
                
                $strsubupper = 'End_'.strtoupper($single['Subject_code']); // subject variable in uppercase
                $maxmark = ucwords($this->term).'_max_mark';// max mark variable for subject
                
                if($single['It_status'] == 1 && isset($row[$strsublower]) && !is_string($row[$strsublower]) && $row[$strsublower] > $single[$maxmark]){// skip entry if the mark more then Max mark
                    flushcache();
                    \abort(500,"Current Subject list contain IT Subject");
                }
                
                $currentsubjectvalue = isset($row[$strsublower]) ? ($row[$strsublower] >= 0 && $row[$strsublower] <= $single[$maxmark] ? $row[$strsublower] : 'AB') : 'AB'; // assign mark of current subject
                
                $addsub[$strsubupper] = $currentsubjectvalue ? $currentsubjectvalue : 0;
                $addsubstatus[$single['Subject_code']] = $currentsubjectvalue != 'AB' ? 'Present' : 'Absent';
                
            }

            $addon['End_marks'] = json_encode($addsub);
            $addon['End_apear_status'] = json_encode($addsubstatus);
            
        }else{
            return null;
        }
        
        $termmarks = ucfirst($this->term).'_marks';
        
        if($check && $check[$termmarks] === $addon[$termmarks]){
            return null;
        }
        
        // addon insert into finel array data
        $finel = $addon;
        
        $finel['system'] = Request::getClientIp(); // complete array data for insertion into database on the basis of there tearm
        
        if($check && !Auth::guard('institute')->check()){//check if the uploading data is already existed or not
            $update = [];
            $finel['updated_at'] = now();
            
            if($check[$termmarks] === $finel[$termmarks]){
                return null;
            }else{
                
                if(isset($check[$termmarks])){
                    // backup entry in backupdata table
                    $fkey = 'diploma_result_id';
                    
                    $check[$fkey] = $check['id'];
                    
                    unset($check['id']);

                    DiplomaResultBackup::insert($check);
                    
                    if($this->term === 'mid'){
                        $midtearmupdate = ['Result'=>Null,'Reappear_subject'=>NULL];
                        DiplomaResult::where('id',$check['id'] ?? $check['diploma_result_id'])->update($midtearmupdate);
                    }else if($this->term === 'end'){
                        $endtearmupdate = ['Reappear_subject'=>NULL,'Result'=>NULL];
                        DiplomaResult::where('id',$check['id']  ?? $check['diploma_result_id'])->update($endtearmupdate);
                    }
                }

                $update = [
                    'conditions' => [
                        'student_id'=>$finel['student_id'],
                        'Stud_batch'=>$this->batch,
                        'Stud_semester'=>$this->semester,
                        'Stud_academic_year'=>checkyearformat($row['student_academic_year'],$this->batch),
                    ],
                    'data' => $finel
                ];
            }

            $this->updateinsert[] = $update;
            $this->dpdata[] = \array_merge($update['data'],$existstudent);
            $this->rowentry[] = $row;

            return;
        }else{
            $finel['updated_at'] = now();
            $finel['created_at'] = now();
            
            $this->newinsert[] = $finel;
            $this->dpdata[] = \array_merge($finel,$existstudent);
            $this->rowentry[] = $row;
            
            return;
        }
    }
}
