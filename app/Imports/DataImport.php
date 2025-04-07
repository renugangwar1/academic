<?php

namespace App\Imports;

use App\Models\Institute;
use App\Models\ResultData;
use App\Models\ResultDataBackup;
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

use function PHPUnit\Framework\isNull;

class DataImport implements ToModel,WithStartRow,WithHeadingRow
{
    protected $course,$batch,$semester,$institute,$tb,$term,$totalrow,$excelsubjects,$existchecklist,$existprevlist,$studentList;
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
    
    public function __construct($batch,$semester,$institute,$tb,$term,$totalrow,$excelsubjects,$existchecklist,$existprevlist,$studentList)
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
        $this->existprevlist = $existprevlist;
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
    * @param row $row
    */
    public function model(array $row)
    {
        
        $inscode = Auth::guard('institute')->user()->id ?? null;
        
        set_time_limit(0);
        
        $this->currentrow++;  //store current row count
        
        $cloneforoptsub = clone collect($this->getsubjects);

        $optsub = $cloneforoptsub->where('Optional_subject',true)->pluck('Subject_code')->toArray();

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

        if(count($optsub) > 0){
            $currentstudoptsub = $existstudent['optionalSubject']['semester_'.$this->semester] ?? null;
            if($currentstudoptsub && !in_array($existstudent['optionalSubject']['semester_'.$this->semester],$optsub)){
                $this->optionalerror[] = "Roll Number {$row['student_nchm_roll_number']} Optional Subject of Semester {$this->semester} is Incorrect. Please rectify before re-uploading marks!";
                return null;
            }else if(!$currentstudoptsub){
                $this->optionalerror[] = "Roll Number {$row['student_nchm_roll_number']} Optional Subject is empty in Semester {$this->semester}";
                return null;
            } 
        }
        
        $getsubjects = $this->getsubjects; // subjects list in laravel:object formate
        
        $institutevalue = $this->institute ?? substr($row['student_nchm_roll_number'], 4, 3);
        
        $institute = Institute::where('id',$institutevalue)->exists();
        
        if(!$institute){
            flushcache();
            \abort(500,"Institute ({$institutevalue}) does not exist in the database at row {$this->currentrow}");
        }
        
        // dd($this->existchecklist);
        $check = array_filter($this->existchecklist,function($data) use ($existstudent, $institutevalue){
            return $data['student_id'] === $existstudent['id'] && $data['institute_id'] === (int) $institutevalue && $data['Stud_semester'] === $this->semester;
        });

        if(count($check)){
            $check = reset($check);
            
            if($check[ucfirst($this->term.'_Result')] && Auth::guard('institute')->check()){
                flushcache();
                \abort(500,"Current data is already compiled. Please contact NCHMCT for updating marks!");
            }
        }
        
        
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
            
            $itcheck = collect($getsubjects)->pluck('It_status')->toArray();
            
            if($this->semester > 1){
                
                $prevsem = in_array(0,$itcheck) ? ($this->semester-1) : ($this->semester-2);
                
                $previusestatus = array_filter($this->existprevlist,function($data) use ($existstudent,$prevsem){
                    return $data['student_id'] === $existstudent['id'] && $data['Stud_semester'] === $prevsem;
                }); // getting value from cached data

                $previusestatus = reset($previusestatus);
                
                if(!$previusestatus){ // display abort error if the previuse semester marks not exist
                    return null;
                }
                
                if(!$previusestatus['End_Result_CGPA']){
                    flushcache();
                    \abort(500,"{$row['student_nchm_roll_number']} Previous Semester marks are not compiled at row {$this->currentrow}");
                }
                
                $total = (int)number_format(stringcount($previusestatus['Mid_Reappear_subject'])+stringcount($previusestatus['Mid_Appear_subject']));
                
                if($previusestatus){
                    // check if the CGPA is less then 3
                    if(!checkEven($this->semester) && $this->semester != 1 && $previusestatus['End_Result_CGPA'] < 3){
                        return null;
                    }
                    
                    // check if the reappear is equal to total number of subject
                    if(checkEven($this->semester) && stringcount($previusestatus['Total_Reappear_subject']) === $total){
                        return null;
                    }
                }
            }
            
            
            $addsub = [];
            $addsubstatus = [];
            
            foreach($getsubjects as $single){
                
                $strsublower = strtolower($this->term.'_'.implode('_',preg_split('/[-=\/.:]/',$single['Subject_code']))); // subject variable in lowercase
                
                $strsubupper = 'Mid_'.strtoupper($single['Subject_code']); // subject variable in uppercase
                $maxmark = ucwords($this->term).'_max_mark';// max mark variable for subject
                
                if($single['It_status'] != 1 && isset($row[$strsublower]) && !is_string($row[$strsublower]) && $row[$strsublower] > $single[$maxmark]){// skip entry if the mark more then Max mark
                    return null;
                }
                
                $currentsubjectvalue = $single['It_status'] == 1 ? 'IT' : (isset($row[$strsublower]) ? ($row[$strsublower] >= 0 && $row[$strsublower] <= $single[$maxmark] ? $row[$strsublower] : 'AB') : 'AB'); // assign mark of current subject
                if($single['Optional_subject'] != 0){
                    if(isset($existstudent['optionalSubject']) && $existstudent['optionalSubject']['semester_'.$single['Semester']] === $single['Subject_code']){
                        $addsub[$strsubupper] = $currentsubjectvalue ? $currentsubjectvalue : 0;
                        $addsubstatus[$single['Subject_code']] = $currentsubjectvalue != 'AB' ? 'Present' : 'Absent';
                        $addon['Optional_subject'] = $existstudent['optionalSubject']['semester_'.$single['Semester']];//assing optional
                    }else{
                        if(!isset($existstudent['optionalSubject'])){
                            $this->errorrow[] = $row;
                            $this->message[] = 'On Student Data!'.$existstudent['name'].' ( '.$existstudent['NCHMCT_Rollnumber'].' ) ';
                            return null;
                        }
                    }
                }else{
                    $addsub[$strsubupper] = $currentsubjectvalue ? $currentsubjectvalue : 0;
                    $addsubstatus[$single['Subject_code']] = $currentsubjectvalue != 'AB' ? 'Present' : 'Absent';
                }
                
            }
            
            $addon['Mid_marks'] = json_encode($addsub);
            $addon['Mid_apear_status'] = json_encode($addsubstatus);
            
        }else if($this->term === 'end')
        {
            if(!$check['Mid_Result']){
                flushcache();
                \abort(500, "Mid Tearm Marks Not Compaild Yet!");
            }
            
            $appear = explode(',', str_replace(' ','',$check['Mid_Appear_subject']));
            
            $endmarksforjson = [];
            $endmarksstatus = [];
            
            if($check['End_marks']){
                $sublist = json_decode($check['End_marks']);
                $apearstatus = json_decode($check['End_apear_status']);
                $newlist = collect($sublist)->mapWithKeys(function($single,$key)use($row,$getsubjects){
                    $subcode = explode('_',$key);
                    $current = collect($getsubjects)->where('Subject_code',$subcode[1])->first();
                    $endtermmark = is_numeric($row[strtolower($key)]) ? ($row[strtolower($key)] >= 0 && $row[strtolower($key)] <= $current['End_max_mark'] ? $row[strtolower($key)] : 'AB') : 'AB';
                    return array_key_exists(strtolower($key),$row) == false ? [$key=>$single] : [$key=>$endtermmark != 'AB' ? $endtermmark : 0];
                });
                
                $Sstatus = collect($apearstatus)->mapWithKeys(function($single,$key)use($row){
                    return array_key_exists('end_'.strtolower($key),$row) == false ? [$key=>$single] : [$key=>is_numeric($row['end_'.strtolower($key)]) ? 'Present' : 'Absent'];
                });
                $endmarksstatus = reset($Sstatus);
                $endmarksforjson = reset($newlist);
            }else{
                foreach($getsubjects as $singlesubject){
                    $rowkey = 'end_'.strtolower(implode('_',preg_split('/[-=\/.:]/',$singlesubject['Subject_code'])));

                    if(in_array($singlesubject['Subject_code'],$appear)){
                        $endtermmark = is_numeric($row[$rowkey]) ? ($row[$rowkey] >= 0 && $row[$rowkey] <= $singlesubject['End_max_mark'] ? $row[$rowkey] : 'AB') : 'AB';
                        $endmarksforjson['End_'.$singlesubject['Subject_code']] = $endtermmark != 'AB' ? $endtermmark : 0;
                        $endmarksstatus[$singlesubject['Subject_code']] = $endtermmark != 'AB' ? 'Present' : 'Absent';
                    }
                }
            }
            
            $addon['End_marks']=json_encode($endmarksforjson);
            $addon['End_apear_status']=json_encode($endmarksstatus);
            
        }else{
            return null;
        }
        
        $termmarks = ucfirst($this->term).'_marks';
        $termresult = ucfirst($this->term).'_Result';
        
        
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
                
                // if(isset($check[$termmarks]) && !$check[$termresult]){
                //     return null;
                //     // flushcache();
                //     // \abort(500, "Selected semester {$this->term}-Tearm marks is not compaild Yet!");
                // }

                if(isset(Auth::user()->role) && Auth::user()->role == 3){
                    // backup entry in backupdata table
                    $fkey = 'result_data_id';
                    
                    $check[$fkey] = $check['id'];
                    
                    unset($check['id']);

                    ResultDataBackup::insert($check);
                    
                    if($this->term === 'mid'){
                        $midtearmupdate = ['Mid_Result'=>Null,'Mid_Reappear_subject'=>NULL,'End_Result'=>NULL];
                        ResultData::where('id',$check['id'] ?? $check['result_data_id'])->update($midtearmupdate);
                    }else if($this->term === 'end'){
                        $endtearmupdate = ['End_Reappear_subject'=>NULL,'End_Result'=>NULL];
                        ResultData::where('id',$check['id']  ?? $check['result_data_id'])->update($endtearmupdate);
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


// $halfsubcount = (int) number_format(((stringcount($check['Mid_Appear_subject'])+stringcount($check['Mid_Reappear_subject']))/2));
            
// if(checkEven($this->semester) != true && stringcount($check['Mid_Appear_subject']) < $halfsubcount){// end tearm prevention for those whos appear subject is less then minimum subject count
//     return null;
// }

// if(checkEven($this->semester) === true && stringcount($check['Mid_Appear_subject']) === 0){// end tearm prevention for those whos appear subject is less then minimum subject count
//     return null;
// }