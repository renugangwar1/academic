<?php

// dd($request->file('importfile'));

use App\Exports\UploadExport;
use App\Models\Course;
use App\Models\ExcelLog;
use App\Models\Institute;
use App\Models\Subject;
use App\Models\Student;
use App\Models\ResultData;
use App\Models\DegreeResult;
use App\Models\DiplomaResult;
use App\Models\ResultDataBackup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Exceptions\CustomeMessageDisplay;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class basic{
    protected $value;
    
    public function __construct($value=null){
        $this->value = $value;
    }
    
    function storeuploadexcel($uploaded,$rowentry,$tablename,$path,$file,$sem){
        if(!isset($uploaded)){
            return 'Heading Needed';
        }

        if(!isset($rowentry)){
            return 'Entry Data Needed';
        }

        if(!isset($tablename)){
            return 'Table Name Required';
        }

        if(!isset($path)){
            return 'Storage Path Required';
        }
        
        if(!isset($file)){
            return 'filePath is not required';
        }
        
        $headings = (new HeadingRowImport)->toArray(storage_path($uploaded));
        
        // $heading = $dataImport->getHeadings();
        $dataRows = collect($rowentry);
        
        // Create Excel file of imported data
        $fileName = $tablename.'_'.$sem.'Semester_' . time();
        
        $pathname = $path.'/'.$fileName. '.xlsx';
        
        // Ensure the subdirectory exists
        if (!Storage::exists($path)) {
            Storage::makeDirectory($path, 0755, true);
        }
        
        Excel::store(new UploadExport($dataRows,$headings), $pathname, 'excel');
        
        // Generate a URL for the stored file
        $fileUrl = Storage::url($pathname);
        
        if($fileUrl){
            $current = ExcelLog::where('excel_link',$file)->first();
            if($current){
                ExcelLog::where('excel_link',$file)->update([
                    'excel_title'=>$fileName,
                    'excel_link'=>$pathname,
                    'system'=>Request::ip(),
                ]); 
            }
        }

        return;
    }
}


class RowCountImport implements ToCollection
{
    public $rowCount = 0;
    public $firstRow = [];
    public $secondRow = [];
    public $requestrows = [];
    protected $tb;
    protected $semester;
    protected $tearm;
    
    public function __construct($tb,$semester,$tearm){
        $this->tb = $tb;
        $this->semester = $semester;
        $this->tearm = $tearm;
    }

    public function collection(Collection $rows)
    {
        if($rows->count() > 5001){
            throw new CustomeMessageDisplay('Current Excel contains more than 5000 entries. Please split the Excel into batches of 5000 entries.');
        }
        
        // Subtract 1 to exclude the header row
        $this->rowCount = $rows->count() - 1;
        
        // Get the first row
        $this->firstRow = $rows->first()->slice(3)->toArray();
        
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getFirstRow()
    {
        return $this->firstRow;
    }

    public function requestrows(){
        $Gsub = Subject::where(['course_id'=>$this->tb->id,'Semester'=>$this->semester])->orderBy('Subject_code')->get();
        if(Auth::guard('institute')->user()->id ?? null){
            $Gsub = $Gsub->where('Subject_type','practical');
        }
        $Gsub = $Gsub->mapWithKeys(function($val,$key){
            return [$key=>$val->Subject_code];
        });
        return reset($Gsub);
    }

    public function checkexcelwithdb(){
        $db = $this->requestrows();
        $dbkey = 0;
        $selectSubjects = '';
        $uploadSubjects = '';
        $tearm = 0;
        foreach($this->firstRow as $key=>$single){
            $tearm = substr($single,0,3) != ucwords($this->tearm) ? 1 : 0;
            $dbkey += in_array(substr($single,4),$db) != true ? 1 : 0;
            // $selectSubjects = $selectSubjects.(strlen($selectSubjects) != 0 ? ',' : '').substr($single,4);
            // dd($tearm);
            // $uploadSubjects = $uploadSubjects.(strlen($uploadSubjects) != 0 ? ',' : '').$db[$dbkey]['Subject_code'];
            // $dbkey++;
        }
        
        if($tearm === 1){
            throw new CustomeMessageDisplay("Excel file data does not match the selected {$this->tearm}-term. Please check the uploading Excel file!");
        }
        
        if($dbkey != 0){
            throw new CustomeMessageDisplay('Excel file data does not match the selected Semester. Please check the uploading Excel file!');
        }
    }
}

class SchemaCheck{
    public function columncheck($checkingtable,$column){
        $columnExists = DB::table('information_schema.columns')
                ->where('table_name', $checkingtable)
                ->where('column_name', $column)
                ->exists();

        return $columnExists;
    }
}

class conmmandatacall{

    public $institutes;
    public $course;
    public $coursename;
    public $semester;

    public function __construct(){
        $this->formselectdata();
    }

    public function formselectdata(){
        $subjectinfo = Subject::orderBy('Semester')->get();
        $this->institutes = Institute::orderby('InstituteCode')->get();
        $this->course = Course::pluck('id','Min_duration')->unique();
        $this->coursename = Course::pluck('Course_name','id')->unique()->toArray();
        $this->semester = $subjectinfo->pluck('Semester')->unique();
    }

    public function getsubjects($course,$semester){
        $course = Course::where('id',$course)->first();
        $db = Subject::where(['course_id'=>$course->id,'Semester'=>$semester])->orderBy('Subject_code')->get();
        return $db;
    }

    public static function SubjectCodeCheck($code){
        $pattern = '/[()\[\],.\'\"\/ ]/';
        

        // Replace the matched characters with an empty string
        $sanitizedString = preg_replace($pattern, '', $code);
        
        return $sanitizedString;
    }


    public static function fetchData(object $data, int $offset, int $limit)
    {
        // fetching data into chunks
        return $data->offset($offset)->limit($limit)->get();
    }

    public static function fatchdataforIT($course_id, $batch, int $semester){
        
        $data = ResultData::with('student:id,name,NCHMCT_Rollnumber,JNU_Rollnumber')->where([
                'course_id' => $course_id,
                'Stud_batch' => $batch,
                'Stud_semester' => $semester,
            ])->select(
                'id', 'student_id', 'course_id', 'Stud_batch', 'Stud_semester','Stud_academic_year' , 'institute_id', 'End_Result', 'End_Result_CGPA', 'created_at'
            );
            

        return $data;
    }

    public static function arraysHaveSameValues($array1, $array2) {
        // Sort both arrays
        sort($array1);
        sort($array2);
    
        // Compare the sorted arrays
        return $array1 == $array2;
    }
}

// icici paymentgatway
class Eazypay{
    
    public $merchant_id;
    public $encryption_key;
    public $sub_merchant_id;
    public $reference_no;
    public $paymode;
    // public $merchant_reference_no;
    public $return_url;

    const DEFAULT_BASE_URL = 'https://eazypay.icicibank.com/EazyPG?';

    public function __construct()
    {
        $this->merchant_id              =    env('ICICI_ICID');
        $this->encryption_key           =    env('ICICI_KEY');
        $this->sub_merchant_id          =    rand(111111, 999999);
        // $this->merchant_reference_no    =    generateReferenceID();
        $this->paymode                  =    '9';
        $this->return_url               =    'https://nhtet-nchm.in/api/return_response';
    }

    public function getPaymentUrl($amount, $reference_no, $mobile ,$optionalField=null)
    {
        
        $nonurl = self::DEFAULT_BASE_URL."merchantid=".$this->merchant_id."&mandatory fields=".$reference_no."|".$this->sub_merchant_id."|".$amount."|".$mobile."&optional fields=".$optionalField."&returnurl=".$this->return_url."&Reference No=".$reference_no."&submerchantid=".$this->sub_merchant_id."&transaction amount=".$amount."&paymode=".$this->paymode;
        
        // return $nonurl;
        $mandatoryField   =    $this->getMandatoryField($amount, $reference_no , $mobile);
        $optionalField    =    $this->getOptionalField($optionalField);
        $amount           =    $this->getAmount($amount);
        $reference_no     =    $this->getReferenceNo($reference_no);

        $paymentUrl = $this->generatePaymentUrl($mandatoryField, $optionalField, $amount, $reference_no);
        
        // $newtest = $paymentUrl."||".$nonurl;
        
        return $paymentUrl;
        // return redirect()->to($paymentUrl);
    }

    protected function generatePaymentUrl($mandatoryField, $optionalField, $amount, $reference_no)
    {
        $encryptedUrl = self::DEFAULT_BASE_URL."merchantid=".$this->merchant_id."&mandatory fields=".$mandatoryField."&optional fields=".$optionalField."&returnurl=".$this->getReturnUrl()."&Reference No=".$reference_no."&submerchantid=".$this->getSubMerchantId()."&transaction amount=".$amount."&paymode=".$this->getPaymode();

        return $encryptedUrl;
    }

    protected function getMandatoryField($amount, $reference_no , $mobile)
    {
        return $this->getEncryptValue($reference_no.'|'.$this->sub_merchant_id.'|'.$amount.'|'.$mobile);
    }

    // optional field must be seperated with | eg. (20|20|20|20)
    protected function getOptionalField($optionalField=null)
    {
        if (!is_null($optionalField)) {
            return $this->getEncryptValue($optionalField);
        }
        return null;
    }

    protected function getAmount($amount)
    {
        return $this->getEncryptValue($amount);
    }

    protected function getReturnUrl()
    {
        return $this->getEncryptValue($this->return_url);
    }

    protected function getReferenceNo($reference_no)
    {
        return $this->getEncryptValue($reference_no);
    }

    protected function getSubMerchantId()
    {
        return $this->getEncryptValue($this->sub_merchant_id);
    }

    protected function getPaymode()
    {
        return $this->getEncryptValue($this->paymode);
    }

    // use @ to avoid php warning php 

    protected function getEncryptValue($str)
    {
        if($str != null){
            $cipher = "aes-128-ecb";
            $key = env('ICICI_KEY');
            in_array($cipher, openssl_get_cipher_methods(true));
            $ivlen = openssl_cipher_iv_length($cipher);
            //echo "ivlen [". $ivlen . "]";
            $iv = openssl_random_pseudo_bytes(1);
            // echo "iv [". $iv . "]";
            $ciphertext = openssl_encrypt($str, $cipher, $key, $options=0, "");
            // echo $ciphertext;

            return $ciphertext; 
        }
        // $block = @mcrypt_get_block_size('rijndael_128', 'ecb');
        // $pad = $block - (strlen($str) % $block);
        // $str .= str_repeat(chr($pad), $pad);
        // return base64_encode(@mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->encryption_key, $str, MCRYPT_MODE_ECB));
    }
    
}
// end icici paymentgatway

// subject compailer
class SubjectCompailer{
    // bsc and msc course
    public static function BscOrMsccourse(array $list,array $subjectlist,string $term){
        if (!$list) {
            abort(500, "Data is not Existed.");
        }
        if (!$term) {
            abort(500, "Please Select Term before compiling result.");
        }
        if (!$subjectlist) {
            abort(500, "The Subject List is missing.");
        }

        $subject = $subjectlist;
        
        $optsubcount = count(array_filter($subject,function($data){
            return $data['Optional_subject'] == 1;
        }));
        
        
        $total = count($subject)-($optsubcount/2);
        $helf = (int)number_format($total/2);
        
        $entryid = [];

        foreach($list as $single){
            
            $appearsub = '';
            $reappearsub = '';
            $marks = $term.'_marks';
            $currentstudent = jsondecodetoarray($single[$marks]);
            
            if(isset($currentstudent)){// this section is for check the current subject is in appeard or in reappeard. student wise.
                foreach($currentstudent as $sub=>$mark){
                    $subsplit = explode('_',$sub);
                    
                    $currentsubd = array_filter($subject,function($data) use ($subsplit){// GET CURRENT SUBJECT MAXMARKS
                        return $data['Subject_code'] === $subsplit[1];
                    });
                    
                    $currentsubd = reset($currentsubd);
                    
                    $passmark = $term.'_pass_mark'; //get variable passingmark from subject list
                    
                    if($mark < $currentsubd[$passmark]){
                        $reappearsub = $reappearsub.($reappearsub != null ? ' , ' : '').$subsplit[1];
                    }else{
                        $appearsub = $appearsub.($appearsub != null ? ' , ' : '').$subsplit[1];
                    }
                    
                }
            }

            if($term == 'Mid'){// mid term compailing section

                $midset = [
                    'Mid_Reappear_subject'=>$reappearsub,
                    'Mid_Appear_subject'=>$appearsub,
                    'Reappear_subject_count'=>stringcount($reappearsub),
                    'Total_Reappear_subject'=>totalreapearconverter($reappearsub,$term),
                    'Compaile_date'=>now(),
                    'Mid_Result'=>stringcount($reappearsub) === 0 ? 'Pass' : (stringcount($appearsub) >= $helf ? 'Reappear' : 'Fail'),// store for check in admitcard print
                ];
                
                $entry = ResultData::where('id',$single['id'])->update($midset);

                $entryid[] = $single['id'];
                
            }else if(isset($single['Mid_Result'])){// end term compailing section
                
                if($single['Mid_Result'] != 'Fail' && $single['End_marks'] === null){
                    abort(500, "End tearm data of institute {$single['institute_id']} not found.");
                }

                $midmraks = jsondecodetoarray($single['Mid_marks'],true);
                $endmraks = jsondecodetoarray($single['End_marks'],true);
                $subtotal = [];
                $subgrad = [];
                $subgradpoint = [];
                $subcredit = [];
                $subcreditpoint = [];

                $endsave = [];


                foreach($subject as $singlesub){
                    if($singlesub['Optional_subject'] == 0 || ($singlesub['Optional_subject'] == 1 && $single['Optional_subject'] === $singlesub['Subject_code'])){
                        
                        $midsearch = 'Mid_'.$singlesub['Subject_code'];
                        $endsearch = 'End_'.$singlesub['Subject_code'];
                        
                        $midsearchvalue = $midmraks[$midsearch] ?? 0;
                        $endsearchvalue = $endmraks[$endsearch] ?? 0;
    
    
                        $subjectmaxobtainmark = ((double) $singlesub['Mid_max_mark'] + (double) $singlesub['End_max_mark']);
                        
                        $total = max(0, $midsearchvalue) + max(0, $endsearchvalue);
                        
                        $subtotal[$singlesub['Subject_code']] = $total;
                        $grad = $endsearchvalue < $singlesub['End_pass_mark'] ? gradefinder(0) : gradefinder(percentfinder($subjectmaxobtainmark, $total));
                        $subgrad[$singlesub['Subject_code']] = $grad;
                        $subgradpoint[$singlesub['Subject_code']] = $grad == 'F' ? 0 : gradepointfinder(percentfinder($subjectmaxobtainmark,$total));
                        $subcredit[$singlesub['Subject_code']] = $singlesub['Credit'];
                        $subcreditpoint[$singlesub['Subject_code']] = $grad == 'F' ? 0 : ((int) gradepointfinder(percentfinder($subjectmaxobtainmark,$total))) * (int) $singlesub['Credit'];
                    }
                }

                $endsave['Marks_total'] = json_encode($subtotal);
                $endsave['Marks_grade'] = json_encode($subgrad);
                $endsave['Marks_grade_point'] = json_encode($subgradpoint);
                $endsave['Marks_credit'] = json_encode($subcredit);
                $endsave['Marks_credit_point'] = json_encode($subcreditpoint);

                ResultData::where('id',$single['id'])->update($endsave);
                
                $totalReappear = totalreapearconverter($single['Mid_Reappear_subject'],'Mid').(strlen($reappearsub) && strlen($single['Mid_Reappear_subject']) ? ', ' : '').totalreapearconverter($reappearsub,$term);
                $totalReappearcount = stringcount($single['Mid_Reappear_subject']) + stringcount($reappearsub);
                
                $secondset = [
                    'End_Reappear_subject'=> $reappearsub,
                    'Total_Reappear_subject'=>$totalReappear,
                    'Reappear_subject_count'=>$totalReappearcount,
                ];
                
                $entry = ResultData::where('id',$single['id'])->update($secondset);
                
                $entryid[] = $single['id'];
            }else{
                abort(500, "Mid tearm data not compaild.");
            }
        }
        return $entryid;
    }
    // end bsc and msc course

    public static function Itcourse(){}
}


// result generating class
class ResultGenerate{
    
    public static function BscOrMsccourse(array $list, array $subjectlist, int $semester){
       
        if (!$list) {
            abort(500, "The Data Collection is missing.");
        }
        if (!$subjectlist) {
            abort(500, "Subject list are missing.");
        }
        if (!$semester) {
            abort(500, "The Semester is missing.");
        }

        $subject = $subjectlist;

        if(count($list) > 0){
            $totalmid = array_reduce($subject, function($carry, $item) {
                return $carry + $item['Mid_max_mark'];
            }, 0);
            
            $totalend = array_reduce($subject, function($carry, $item) {
                return $carry + $item['End_max_mark'];
            }, 0);
            
            $total = $totalmid+$totalend;
            
            $entryid = [];
            foreach($list as $data){
                
                if(!isset($data['Marks_total'])){
                    abort(500, "End term data not compiled yet!");
                }
                
                $excludingmark = excludingmark($data,$subject);
                
                // decode json data of student
                $subtotal = json_decode($data['Marks_total']);
                $subtotalcredit = json_decode($data['Marks_credit_point']);
                $subscredit = json_decode($data['Marks_credit']);

                // totaling data of store json data
                $totaloftotal = array_sum((array) $subtotal);
                $countsub = ($total - $excludingmark);
                $totalgrade = gradefinder(percentfinder($countsub,$totaloftotal),2);
                $totalcpoint = array_sum((array) $subtotalcredit);
                $sgpasses = array_sum((array) $subscredit);
                $sgpa = sprintf("%.2f", sgpafinder($totalcpoint,$sgpasses));

                $setone = [
                    'Grand_Total'=>$totaloftotal,
                    'Grand_Total_Grade'=>$totalgrade,
                    'Grand_Credit_Point'=>$totalcpoint,
                    'End_Result_SGPA'=>$sgpa,
                ];

                ResultData::where('id',$data['id'])->update($setone);

                $entryid[] = $data['id'];
            }
        
            return $entryid;
        }else{
            abort(500, "Data not avilable!");
        }
    }


    public static function DeeplomaResult(array $list, array $subjectlist, int $semester){
        
        if (!$list) {
            abort(500, "Data not avilable.");
        }
        if (!$subjectlist) {
            abort(500, "Subject list are missing.");
        }
        if (!$semester) {
            abort(500, "The Semester is missing.");
        }

        $summid = collect($subjectlist)->sum('Mid_max_mark');
        $sumend = collect($subjectlist)->sum('End_max_mark');
        $totalmarks = $summid+$sumend;

        foreach($list as $single){
            
            // decode json data of student
            $midmarks = json_decode($single['Mid_marks']);
            $endmarks = json_decode($single['End_marks']);
            
            $midsum = array_sum((array) $midmarks);
            $endsum = array_sum((array) $endmarks);

            $totalmarksbysub = [];
            $reappearsub = [];

            foreach($subjectlist as $sub){
                $mid = 'Mid_'.$sub['Subject_code'];
                $end = 'End_'.$sub['Subject_code'];
                $passingMark = $sub['Mid_pass_mark'] + $sub['End_pass_mark'];
                $obtainMark = $midmarks->$mid + $endmarks->$end;
                if($obtainMark < $passingMark){
                    $reappearsub[] = $sub['Subject_code'];
                }
                $totalmarksbysub[$sub['Subject_code']] = floor($obtainMark); 
            }

            $totalmarksjson = json_encode($totalmarksbysub);

            $grandtotal = floor($midsum+$endsum);

            $percentage = percentfinder($totalmarks,$grandtotal);

            $result = count($reappearsub) > 0 ? 'Compartment' : ($percentage >= 60 ? 'First' : ($percentage >= 50 ? 'Second' : ($percentage >= 40 ? 'Pass':'Compartment')));

            $setone = [
                'Marks_total'=>$totalmarksjson,
                'Grand_Total'=>$grandtotal,
                'Total_Percentage'=>$percentage,
                'Result'=>$result,
                'Reappear_subject'=>implode(',',$reappearsub),
                'Reappear_subject_count'=>count($reappearsub),
                'Result_date'=>now(),
            ];

            DiplomaResult::where('id',$single['id'])->update($setone);

            $entryid[] = $single['id'];
        }

        return $entryid;
    }
    
    public static function CGPCallculation(array $list, array $subjectlist, int $semester){
        if (!$list) {
            abort(500, "The Data Collection is missing.");
        }
        if (!$subjectlist) {
            abort(500, "Subject list are missing.");
        }
        if (!$semester) {
            abort(500, "The Semester is missing.");
        }

        $subject = $subjectlist;

        if(count($list) > 0){
            $totalmid = array_reduce($subject, function($carry, $item) {
                return $carry + $item['Mid_max_mark'];
            }, 0);
            
            $totalend = array_reduce($subject, function($carry, $item) {
                return $carry + $item['End_max_mark'];
            }, 0);
            
            $total = $totalmid+$totalend;
            
            $entryid = [];
            foreach($list as $data){
                
                if(!isset($data['Marks_total'])){
                    abort(500, "End term data not compiled yet!");
                }

                $excludingmark = excludingmark($data,$subject);
                $countsub = ($total - $excludingmark); 
                $totaloftotal = array_sum((array) json_decode($data['Marks_total']));

                $totalsubject = (int)number_format((stringcount($data['Mid_Reappear_subject'])+stringcount($data['Mid_Appear_subject'])));
                $reappearcount = (int)number_format(stringcount($data['Total_Reappear_subject']));
                $checkeven = checkEven($semester);
                $totalpercent = number_format(percentfinder($countsub,$totaloftotal),2);
                $cgpa = sprintf("%.2f", cgpafinder($data['student_id'],$semester,$checkeven));
                $result = resultfind($checkeven,$reappearcount,$cgpa,$totalsubject);
                $settwo = [
                    'Total_Percentage'=>$totalpercent,
                    'End_Result_CGPA'=>$cgpa,
                    'End_Result'=>$result,
                    'Result_date'=>now(),
                ];

                ResultData::where('id',$data['id'])->update($settwo);

                $entryid[] = $data['id'];
            }
        
            return $entryid;
        }else{
            abort(500, "Data not avilable!");
        }
    }
}


class PrintDetails{
    protected $Coures_id,$Semester,$Batch,$InstituteID,$Rollnumber;
    
    public function __construct(int $Coures_id, int $Semester, string $Batch, int $InstituteID=null, string $Rollnumber=null){
        $this->Coures_id = $Coures_id;
        $this->Semester = $Semester;
        $this->Batch = $Batch;
        $this->InstituteID = $InstituteID;
        $this->Rollnumber = $Rollnumber;
    }

    // public function PrintAdmitCard(){
    //     $subjects = Subject::where(['course_id'=>$this->Coures_id,'Semester'=>$this->Semester])->select('Optional_subject','It_status')->get()->toArray();
    //     $optionalSubjectsCount = count(array_filter($subjects,function($data){
    //         return $data['Optional_subject'] == 1; 
    //     }));
        
    //     $itsubject = count(array_filter($subjects,function($data){
    //         return $data['It_status'] == 1; 
    //     }));
        
    //     $totalsubject = count($subjects) - ($optionalSubjectsCount / 2);
    //     // $checkhalf = intval($totalsubject / 2);
        
    //     $getadmitcard = ResultData::with(['institute:id,InstituteName','student:id,name,NCHMCT_Rollnumber,JNU_Rollnumber','course:id,Course_name'])
    //     ->select('id', 'student_id', 'Stud_batch', 'course_id', 'Mid_Result', 'Reappear_subject_count', 'Stud_academic_year', 'Stud_semester', 'Mid_Appear_subject', 'institute_id')
    //     ->where([
    //         ['course_id',$this->Coures_id],
    //         ['Stud_batch',$this->Batch],
    //         ['Stud_semester',$this->Semester],
    //         ['Mid_Result','!=',null],
    //         ['Reappear_subject_count','!=',$totalsubject],
    //         ])->orderBy(Student::select('JNU_Rollnumber')
    //     ->whereColumn('students.id', 'result_data.student_id'))->get();
        
    //     if($getadmitcard->count() === 0){
    //         return back()->withErrors('Selected data is not Compiled!');
    //     }
        
    //     // Filter by Rollnumber or InstituteID
    //     if ($this->Rollnumber) {
    //         $getadmitcard = $getadmitcard->filter(function ($data) {
    //             return $data->student->NCHMCT_Rollnumber === $this->Rollnumber;
    //         });

    //         if ($getadmitcard->isEmpty()) {
    //             return back()->withErrors('Current Student Mid Result Not Clear!');
    //         }
    //     } elseif ($this->InstituteID) {
    //         $getadmitcard = $getadmitcard->filter(function ($data) {
    //             return $data->institute_id === $this->InstituteID;
    //         });

    //         if ($getadmitcard->isEmpty()) {
    //             return back()->withErrors('No Data Available for Selected Institute!');
    //         }
    //     } else {
    //         return back()->withErrors('Please Select Institute to print admit card');
    //     }

    //     $getadmitcard = $this->filterdataforAdmitcard($getadmitcard,$itsubject === count($subjects));
        
    //     // return view('pdfadmitcard',compact('getacdmitcard'));
    //     $pdf = FacadePdf::loadView('pdf.pdfadmitcard', compact('getadmitcard'));
        
    //     return $pdf->stream();
        
    // }


    public function PrintAdmitCard()
{
    $subjects = Subject::where([
        'course_id' => $this->Coures_id,
        'Semester' => $this->Semester
    ])->select('Optional_subject', 'It_status')->get()->toArray();

    $optionalSubjectsCount = count(array_filter($subjects, function ($data) {
        return $data['Optional_subject'] == 1;
    }));

    $itsubject = count(array_filter($subjects, function ($data) {
        return $data['It_status'] == 1;
    }));

    $totalsubject = count($subjects) - ($optionalSubjectsCount / 2);

    // Fetch students whose result is not declared (End_marks is NULL)
    $getadmitcard = ResultData::with([
            'institute:id,InstituteName',
            'student:id,name,NCHMCT_Rollnumber,JNU_Rollnumber',
            'course:id,Course_name'
        ])
        ->select(
            'id', 'student_id', 'Stud_batch', 'course_id', 'Mid_Result',
            'Reappear_subject_count', 'Stud_academic_year', 'Stud_semester',
            'Mid_Appear_subject', 'institute_id', 'End_marks' // Add End_marks here
        )
        ->where([
            ['course_id', $this->Coures_id],
            ['Stud_batch', $this->Batch],
            ['Stud_semester', $this->Semester],
            ['Mid_Result', '!=', null],
            ['Reappear_subject_count', '!=', $totalsubject],
        ])
        ->whereNull('End_marks') // ✅ Only include students with no declared result
        ->orderBy(Student::select('JNU_Rollnumber')
            ->whereColumn('students.id', 'result_data.student_id'))
        ->get();

    // No data found
    if ($getadmitcard->count() === 0) {
        return back()->withErrors('Selected data is not Compiled or Result already declared!');
    }

    // Filter by roll number
    if ($this->Rollnumber) {
        $getadmitcard = $getadmitcard->filter(function ($data) {
            return $data->student->NCHMCT_Rollnumber === $this->Rollnumber;
        });

        if ($getadmitcard->isEmpty()) {
            return back()->withErrors('Current Student Mid Result Not Clear or Result Declared!');
        }

    // Filter by institute
    } elseif ($this->InstituteID) {
        $getadmitcard = $getadmitcard->filter(function ($data) {
            return $data->institute_id === $this->InstituteID;
        });

        if ($getadmitcard->isEmpty()) {
            return back()->withErrors('No Data Available for Selected Institute or Result Declared!');
        }

    } else {
        return back()->withErrors('Please Select Institute to print admit card');
    }

    // Apply final data formatting
    $getadmitcard = $this->filterdataforAdmitcard($getadmitcard, $itsubject === count($subjects));

    // Load PDF
    $pdf = FacadePdf::loadView('pdf.pdfadmitcard', compact('getadmitcard'));
    return $pdf->stream();
}


    public function PrintDiplomaAdmitCard(){
        $subjects = Subject::where(['course_id'=>$this->Coures_id,'Semester'=>$this->Semester])->get();
        
        $getreappera = DiplomaResult::with(['institute:id,InstituteName','student:id,name,NCHMCT_Rollnumber,JNU_Rollnumber','course:id,Course_name'])->where([
            'course_id'=>$this->Coures_id,
            'Stud_batch'=>$this->Batch,
            'Stud_semester'=>$this->Semester
        ])->get();

        $reappear = $getreappera->Where('Reappear_subject_count', '!=', 0);
        $midmark = $getreappera->WhereNull('End_marks');
        
        $collection = array_merge($reappear->toArray(),$midmark->toArray());
        
        $subjectlist = $subjects->pluck('Subject_code')->mapWithKeys(function($single) {
            return [$single => $single];
        });

        $newlist = implode(',',$subjectlist->toArray());

        $firsttime = [];
        
        foreach($collection as $single){
            $firsttime[] = [
                'Institute_name'=>$single['institute']['InstituteName'],
                'Course_name'=>$single['course']['Course_name'],
                'NCHMCT_rollnumber'=>$single['student']['NCHMCT_Rollnumber'],
                'Student_name'=>$single['student']['name'],
                'Batch'=>$single['Stud_batch'],
                'Semester'=>$single['Stud_semester'],
                'Date_of_issue'=>date('d/M/Y'),
                'Appear_subject'=>$single['Reappear_subject'] ?? $newlist
            ];
        }

        $getadmitcard = $firsttime;
        
        // return view('pdfadmitcard',compact('getacdmitcard'));
        $pdf = FacadePdf::loadView('pdf.diplomaadmitcard', compact('getadmitcard'));
        
        return $pdf->stream();
        
    }

    public function PrintResult($Rollnumber=null){
        $generateresult = ResultData::with(['course:id,Course_name','student'])->select('id', 'student_id', 'course_id', 'Stud_academic_year', 'Stud_semester', 'institute_id', 'End_Result_SGPA', 'End_Result_CGPA', 'Marks_grade', 'Marks_credit', 'Grand_Credit_Point')
        ->where(['course_id'=>$this->Coures_id, 'Stud_batch'=>$this->Batch, 'Stud_semester'=>$this->Semester])
        ->where('End_Result_SGPA','!=',null)
        ->where('End_Result_CGPA','!=',null)->get()->toArray();

        if(!$generateresult){
            return back()->withErrors('Result not generated yet!');
        }

        if($this->Rollnumber){
            $generateresult = array_filter($generateresult,function($data){
                return $data['student']['NCHMCT_Rollnumber'] === $this->Rollnumber;
            });

            if(!$generateresult){
                return back()->withErrors(['error' => 'Result of Provided Roll Number not Exist!']);
            }

        }else if($this->InstituteID){
            $generateresult = array_filter($generateresult,function($data){
                return $data['institute_id'] === $this->InstituteID;
            });
            
            if(!$generateresult){
                return back()->withErrors(['error' => 'Institute Result Not Found!']);
            }
        
        }else{
            return back()->withErrors('Please Select Institute for print admitcard');
        }
        
        $results = resultfiltaring($generateresult);
        
        return view('pdf.htmlresult', compact('results'));
        
        // $html = view('pdf.pdfresult', compact('results'))->render();
        
        // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        
        // return $pdf->stream();
        
    }


    public function PrintDiplomaResult($Rollnumber=null){
        $generateresult = DiplomaResult::with(['course:id,Course_name','student'])->where(['course_id'=>$this->Coures_id, 'Stud_batch'=>$this->Batch, 'Stud_semester'=>$this->Semester])
        ->get()->toArray();

        if(!$generateresult){
            return back()->withErrors('Result not generated yet!');
        }

        if($this->Rollnumber){
            $generateresult = array_filter($generateresult,function($data){
                return $data['student']['NCHMCT_Rollnumber'] === $this->Rollnumber;
            });

            if(!$generateresult){
                return back()->withErrors(['error' => 'Result of Provided Roll Number not Exist!']);
            }

        }else if($this->InstituteID){
            $generateresult = array_filter($generateresult,function($data){
                return $data['institute_id'] === $this->InstituteID;
            });
            
            if(!$generateresult){
                return back()->withErrors(['error' => 'Institute Result Not Found!']);
            }
        
        }else{
            return back()->withErrors('Please Select Institute for print admitcard');
        }
        
        $results = Diplomaresultfiltaring($generateresult);
        
        return view('pdf.htmldiplomaresult', compact('results'));
        
        // $html = view('pdf.pdfdiplomaresult', compact('results'))->render();
        
        // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        
        // return $pdf->stream();
    }

//     protected function filterdataforAdmitcard($data,$isIt){
//         return $data->map(function($result) use ($isIt){
//             $checkprevsem = ResultData::where(['student_id'=>$result->student_id,'Stud_semester'=>($result->Stud_semester-1)])->exists();
//             $semesterval = $isIt === true ? ($checkprevsem != true ? ordinalget($result->Stud_semester - 1).' (In IT)' : ordinalget($result->Stud_semester).' (In IT)') : ($result->Stud_semester == 3  ? ordinalget($result->Stud_semester +1).'(In Institute)' : ordinalget($result->Stud_semester));
//             return [
//                 'Institute_name'=>$result->institute->InstituteName,
//                 'Course_name'=>$result->course->Course_name,
//                 'NCHMCT_rollnumber'=>$result->student->NCHMCT_Rollnumber,
//                 'Student_name'=>$result->student->name,
//                 'Exam_type'=>'REGULAR',
//                 'Academic_year'=>$result->Stud_academic_year,
//                 'Semester'=> $semesterval,
//                 'Date_of_issue'=>date('d-M-Y'),
//                 'Appear_subject'=>$result->Mid_Appear_subject,
//             ];
//         });
//     }
// }

protected function filterdataforAdmitcard($data, $isIt)
{
    return $data->filter(function ($result) {
      
        return is_null($result->End_marks);
    })->map(function ($result) use ($isIt) {

      
        $checkprevsem = ResultData::where([
            'student_id' => $result->student_id,
            'Stud_semester' => ($result->Stud_semester - 1)
        ])->exists();

     
        $semesterval = $isIt === true
            ? ($checkprevsem != true
                ? ordinalget($result->Stud_semester - 1) . ' (In IT)'
                : ordinalget($result->Stud_semester) . ' (In IT)')
            : ($result->Stud_semester == 3
                ? ordinalget($result->Stud_semester +1 ) . ' (In Institute)'
                : ordinalget($result->Stud_semester));

      
        return [
            'Institute_name' => $result->institute->InstituteName,
            'Course_name' => $result->course->Course_name,
            'NCHMCT_rollnumber' => $result->student->NCHMCT_Rollnumber,
            'Student_name' => $result->student->name,
            'Exam_type' => 'REGULAR',
            'Academic_year' => $result->Stud_academic_year,
            'Semester' => $semesterval,
            'Date_of_issue' => date('d-M-Y'),
            'Appear_subject' => $result->Mid_Appear_subject,
        ];
    });
}
}

class History{
    protected $Batch,$Semester,$Course,$Rollnumber;
    
    public function __construct(string $Batch,string $Semester,string $Course,string $Rollnumber){
        $this->Batch = $Batch;
        $this->Semester = $Semester;
        $this->Course = $Course;
        $this->Rollnumber = $Rollnumber;
    }

    public function Current($student){
        $current = ResultData::with(['student','course'])->where(['Stud_batch' => $this->Batch, 'Stud_semester' => $this->Semester ,'student_id'=>$student['id']])
        ->orderBy('updated_at')->get();
        
        $final = $current->map(function($result){
            return [
                'updated_at' => date('d-m-Y h:i',strtotime($result['updated_at'])),
                'name' => $result['student']['name'],
                'NCHMCT_Rollnumber' => $result['student']['NCHMCT_Rollnumber'],
                'JNU_Rollnumber' => $result['student']['JNU_Rollnumber'],
                'InstituteName' => $result['institute_id'],

                'Mid_marks'=> $result['Mid_marks'],
                'End_marks'=> $result['End_marks'],
                'Marks_total'=> $result['Marks_total'],
                'Marks_grade_point'=> $result['Marks_grade_point'],
                'Marks_credit_point'=> $result['Marks_credit_point'],
                'Marks_grade'=> $result['Marks_grade'],
                'Grand_Total'=> $result['Grand_Total'],
                'Grand_Credit_Point'=> $result['Grand_Credit_Point'],
                'Total_Percentage'=> $result['Total_Percentage'],
                'End_Result'=> $result['End_Result'],
                'End_Result_SGPA'=> $result['End_Result_SGPA'],
                'End_Result_CGPA'=> $result['End_Result_CGPA'],
                'Optional_subject'=> $result['Optional_subject'],
                'Total_Reappear_subject'=> $result['Total_Reappear_subject'],
            ];
        })->toArray();

        return $final;
    }

    public function Previus($model,$student){
        // dd($student);
        $previus = ResultDataBackup::with($model)->where(['Stud_batch' => $this->Batch, 'Stud_semester' => $this->Semester,'student_id'=>$student['id']])
        ->orderBy('updated_at');
        
        if($previus->count() > 0){
            $final = $previus->get()->map(function ($result) use ($model) {
                return [
                    'updated_at' => date('d-m-Y h:i',strtotime($result['updated_at'])),
                    'name' => $result[$model]['name'],
                    'NCHMCT_Rollnumber' => $result[$model]['NCHMCT_Rollnumber'],
                    'JNU_Rollnumber' => $result[$model]['JNU_Rollnumber'],
                    'InstituteName' => $result['institute_id'],
    
                    'Mid_marks'=> $result['Mid_marks'],
                    'End_marks'=> $result['End_marks'],
                    'Marks_total'=> $result['Marks_total'],
                    'Marks_grade_point'=> $result['Marks_grade_point'],
                    'Marks_credit_point'=> $result['Marks_credit_point'],
                    'Marks_grade'=> $result['Marks_grade'],
                    'Grand_Total'=> $result['Grand_Total'],
                    'Grand_Credit_Point'=> $result['Grand_Credit_Point'],
                    'Total_Percentage'=> $result['Total_Percentage'],
                    'End_Result'=> $result['End_Result'],
                    'End_Result_SGPA'=> $result['End_Result_SGPA'],
                    'End_Result_CGPA'=> $result['End_Result_CGPA'],
                    'Optional_subject'=> $result['Optional_subject'],
                    'Total_Reappear_subject'=> $result['Total_Reappear_subject'],
                ];
            })->toArray();
            return $final;
        }else{
            return [];
        }
    }


    public function SHistory($model,$student){
        
        $history = ResultDataBackup::with($model)->where(['Stud_batch' => $this->Batch, 'Stud_semester' => $this->Semester,'student_history_id'=>$student['id']])
        ->orderBy('updated_at');
        
        if($history->count() > 0){
            $final = $history->get()->map(function ($result) use ($model) {
                return [
                    'updated_at' => date('d-m-Y h:i',strtotime($result['updated_at'])),
                    'name' => $result[$model]['name'],
                    'NCHMCT_Rollnumber' => $result[$model]['NCHMCT_Rollnumber'],
                    'JNU_Rollnumber' => $result[$model]['JNU_Rollnumber'],
                    'InstituteName' => $result['institute_id'],
    
                    'Mid_marks'=> $result['Mid_marks'],
                    'End_marks'=> $result['End_marks'],
                    'Marks_total'=> $result['Marks_total'],
                    'Marks_grade_point'=> $result['Marks_grade_point'],
                    'Marks_credit_point'=> $result['Marks_credit_point'],
                    'Marks_grade'=> $result['Marks_grade'],
                    'Grand_Total'=> $result['Grand_Total'],
                    'Grand_Credit_Point'=> $result['Grand_Credit_Point'],
                    'Total_Percentage'=> $result['Total_Percentage'],
                    'End_Result'=> $result['End_Result'],
                    'End_Result_SGPA'=> $result['End_Result_SGPA'],
                    'End_Result_CGPA'=> $result['End_Result_CGPA'],
                    'Optional_subject'=> $result['Optional_subject'],
                    'Total_Reappear_subject'=> $result['Total_Reappear_subject'],
                ];
            })->toArray();
            return $final;
        }else{
            return [];
        }
    }
}


class DataView{
    public static function degreeview($request){
        $select=['student_id',
        'institute_id',
        'Mid_marks',
        'End_marks',
        'Marks_total',
        'Marks_grade_point',
        'Marks_credit_point',
        'Marks_grade',
        'Grand_Total',
        'Grand_Credit_Point',
        'Total_Percentage',
        'End_Result',
        'End_Result_SGPA',
        'End_Result_CGPA',
        'Optional_subject',
        'Total_Reappear_subject',
        ];
        
        $jshead=['Stud_name',
        'Stud_nchm_roll_number',
        'Stud_jnu_roll_number',
        'institute_id',
        'Grand_Total',
        'Grand_Credit_Point',
        'Total_Percentage',
        'End_Result',
        'End_Result_SGPA',
        'End_Result_CGPA',
        ];

        $tb = Subject::where(['course_id'=>$request->exportcourse,'Semester'=>$request->exportsemester])->orderBy('Subject_code')->get();

        $semfinder = checkEven($request->exportsemester) == true ? 'CGPA' : 'SGPA';
        
        $heading = ['Student Name','NCHM Roll Number','JNU Roll Number','Institute Code','Total','Total Credit','Percentage','Result','SGPA',$semfinder];

        foreach($tb as $key=>$head){
            $subjecthead[$key]['code'] = $head->Subject_code;
            $subjecthead[$key]['credit'] = $head->Credit;
        }
        
        foreach($tb as $head){
            $jshead[] = 'Mid'.$head->Subject_code;
            $jshead[] = 'End'.$head->Subject_code;
            $jshead[] = 'GradePoint'.$head->Subject_code;
            $jshead[] = 'CreditPoint'.$head->Subject_code;
            $jshead[] = 'Grade'.$head->Subject_code;
        }

        $allcourse = Course::get();
        
        $data = ResultData::where(['course_id'=>$request->exportcourse,'Stud_batch' => $request->exportbatch, 'Stud_semester' => $request->exportsemester]);

        if(isset($request->exportinstitute)){
            $data = $data->where('institute_id',$request->exportinstitute);
        }
        
        $data = $data->select($select)
        ->count();

        $course = $allcourse->pluck('Course_name','id');
        $seardb[] = $request->exportcourse;
        $seardb[] = $request->exportbatch;
        $seardb[] = $request->exportsemester;
        $seardb[] = isset($request->exportinstitute) ? $request->exportinstitute : null;
        $seardb[] = $select;
        $jsonArray = json_encode($seardb);
        $selectitms = json_encode($select);
        $jshead = json_encode($jshead);
        if($data > 0){
            $view = view('search.excel.view',compact('heading','course','jsonArray','selectitms','subjecthead','jshead'));
            return $view;
        }else{
            return back()->withErrors('Data Not Available for Requested Period');
        }
    }

    public static function diplomaview($request){
        $select=['student_id',
        'institute_id',
        'Mid_marks',
        'End_marks',
        'Marks_total',
        'Grand_Total',
        'Total_Percentage',
        'Result',
        'Reappear_subject',
        ];
        
        $jshead=['Stud_name',
        'Stud_nchm_roll_number',
        'institute_id',
        'Grand_Total',
        'Total_Percentage',
        'Result',
        ];

        $tb = Subject::where(['course_id'=>$request->exportcourse,'Semester'=>$request->exportsemester])->orderBy('Subject_code')->get();

        $heading = ['Student Name','NCHM Roll Number','Institute Code','Total','Percentage','Result'];

        foreach($tb as $key=>$head){
            $subjecthead[$key]['code'] = $head->Subject_code;
        }
        
        foreach($tb as $head){
            $jshead[] = 'Mid'.$head->Subject_code;
            $jshead[] = 'End'.$head->Subject_code;
        }

        $allcourse = Course::get();
        
        $data = DiplomaResult::where(['course_id'=>$request->exportcourse,'Stud_batch' => $request->exportbatch, 'Stud_semester' => $request->exportsemester]);

        if(isset($request->exportinstitute)){
            $data = $data->where('institute_id',$request->exportinstitute);
        }
        
        $data = $data->select($select)
        ->count();

        $course = $allcourse->pluck('Course_name','id');
        $seardb[] = $request->exportcourse;
        $seardb[] = $request->exportbatch;
        $seardb[] = $request->exportsemester;
        $seardb[] = isset($request->exportinstitute) ? $request->exportinstitute : null;
        $seardb[] = $select;
        $jsonArray = json_encode($seardb);
        $selectitms = json_encode($select);
        $jshead = json_encode($jshead);
        // dd($heading,$course,$jsonArray,$selectitms,$subjecthead,$jshead);
        if($data > 0){
            $view = view('search.excel.diplomaview',compact('heading','course','jsonArray','selectitms','subjecthead','jshead'));
            return $view;
        }else{
            return back()->withErrors('Data Not Available for Requested Period');
        }
    }
}