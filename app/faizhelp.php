<?php

use App\Models\Bsc;
use App\Models\Msc;
use App\Models\ResultData;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\SubjectMaster;
use App\Models\Course;
use Hamcrest\Type\IsNumeric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;
use PhpParser\Node\Expr\Cast\Double;
use Sabberworm\CSS\Property\Import;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cache;

function strtoarray($str=null){
    if($str !== null){
        $exp = explode(',',$str);
        return $exp;
    }
}

function removeNonDigits($string) {
    // Use preg_replace to replace all non-digit characters with an empty string
    return preg_replace('/\D/', '', $string);
}

function slnumber($last=null){
    $split = explode('/',$last);
    $define = $split[3] ?? 1;
    $number = (isset($split) ? (substr($split[2]??'XXXX', -2)) : date('y')).($split[1] ?? 'XXX').sprintf('%06d', $define);
    return $number;
}

function ordinalget($val){
    if($val < 11){
        switch($val % 10){
            case 1: return $val.'st';
            case 2: return $val.'nd';
            case 3: return $val.'rd';
            default : return $val.'th';
        }
    }
    return $val.'th';
}

function subjectcode($code=null){
    if($code != null){
        $firstPart = substr($code, 0, 3);
        $secondPart = substr($code, 3);
        $corse[] = $firstPart;
        array_push($corse,$secondPart);
        return $corse; 
    }else{
        return null;
    }
}

class columgen{
    function columngen($code=null,$type=null){
        if($code != null && $type != null){
            $gen = $type.strtoupper($code);
            return $gen;
        }
    }  
}

function academicyear($limit=null){
    $academicyears = [];

    for ($i = 0; $i <= ($limit ?? 1); $limit--) {
        $endYear = date('Y') - $limit;
        $academicyear =  $endYear. '-' .($endYear + 1);
        $academicyears[] = $academicyear;
    }

    return $academicyears;
}

function fixpgSequence($tname) {
    $driver = DB::connection()->getDriverName();
    
    if ($driver != 'pgsql') {
        return;
    }
    
    try {
        DB::rollBack();
        DB::beginTransaction();

        // Get the sequence name dynamically
        $result = DB::select("SELECT pg_get_serial_sequence(?, 'id') as seq", [$tname]);
        if (empty($result)) {
            throw new Exception("Sequence name not found for table $tname");
        }
        
        $sequenceName = $result[0]->seq;
        
        // Set the sequence value
        DB::select("SELECT setval(?, (SELECT COALESCE(MAX(id), 1) FROM $tname) + 1)", [$sequenceName]);

        DB::commit();
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Failed to fix sequence for table ' . $tname . ': ' . $e->getMessage());
        Log::error('Exception Trace: ' . $e->getTraceAsString());
        // Optionally, you can rethrow the exception or handle it as needed
        throw $e;
    }
}

function inputvaluecheck($input=null){
    if (is_int($input) && $input == null) {
        return $input;
    } elseif (is_string($input) && $input != 'NULL') {
        return $input;
    } elseif($input == 'NULL') {
        return null;
    } else{
        return null;
    }
}

function batch($year=null){
    if($year != null){
        return $year.'-'.($year+3);
    }else{
        $year = date('Y');
        $batch = array();
        for($i=3; $i >= 0 ; $i--){
            $batch[]=($year-$i).'-'.($year-$i+3);
        }
        return $batch;
    }
}

// function course(){
//     $course = Course::select('id','Min_duration')->get();
//     $courselist = [];
//     foreach($course as $single){
//         $courselist[$single->Min_duration] = $single->id;
//     }
//     return $courselist;
// }

// function compailer($arry=null,$subject=null,$term=null,$semester=null){
//     $checkopt = $subject;
//     $optsubcount = $checkopt->where('Optional_subject',true)->count();
//     $total = count($subject)-($optsubcount/2);
//     $helf = (int)number_format($total/2);
//     if($arry != null){
//         foreach($arry as $single){
//             if($term != null){//condition to check and add subject to appearsubject or reappearsubject with checking optionalsubject codition
//                 $appearsub = '';
//                 $reappearsub = '';
//                 $marks = $term.'_marks';
//                 $currentstudent = jsondecodetoarray($single->$marks);
                
//                 if(isset($currentstudent)){
//                     foreach($currentstudent as $sub=>$mark){
//                         $subsplit = explode('_',$sub);
//                         $currentsubd = $subject;
//                         $passmark = $term.'_pass_mark';
//                         if($mark < $currentsubd->where('Subject_code',$subsplit[1])->first()->$passmark){
//                             $reappearsub = $reappearsub.($reappearsub != null ? ' , ' : '').$subsplit[1];
//                         }else{
//                             $appearsub = $appearsub.($appearsub != null ? ' , ' : '').$subsplit[1];
//                         }
//                     }
//                 }
//             }else{
//                 return false;
//             }
            
//             if($term == 'Mid'){// mid term compailing section
//                 ResultData::where(['Stud_nchm_roll_number'=>$single->Stud_nchm_roll_number,'Stud_semester'=>$semester])->update([
//                     'Mid_Reappear_subject'=>$reappearsub,
//                     'Mid_Appear_subject'=>$appearsub,
//                     'Reappear_subject_count'=>stringcount($reappearsub),
//                     'Mid_Result'=>stringcount($reappearsub) === 0 ? 'Pass' : (stringcount($appearsub) >= $helf ? 'Reappear' : 'Fail'),// store for check in admitcard print
//                     'Total_Reappear_subject'=>totalreapearconverter($reappearsub,$term),
//                     'Compaile_date'=>now(),
//                 ]);
                
//             }else if(isset($single->Mid_Result)){// end term compailing section
//                 $midmraks = jsondecodetoarray($single->Mid_marks,true);
//                 $endmraks = jsondecodetoarray($single->End_marks,true);
//                 $subtotal = [];
//                 $subgrad = [];
//                 $subgradpoint = [];
//                 $subcredit = [];
//                 $subcreditpoint = [];

//                 $endsave = [];

//                 foreach($subject as $singlesub){
//                     if($singlesub->Optional_subject == 1 && $single->Optional_subject === $singlesub->Subject_code){
                        
//                         $midsearch = 'Mid_'.$singlesub->Subject_code;
//                         $endsearch = 'End_'.$singlesub->Subject_code;
                        
//                         $midsearchvalue = $midmraks[$midsearch] ?? 0;
//                         $endsearchvalue = $endmraks[$endsearch] ?? 0;
    
    
//                         $subjectmaxobtainmark = ((double) $singlesub->Mid_max_mark + (double) $singlesub->End_max_mark);
                        
//                         $total = (is_numeric($midsearchvalue) === true && $midsearchvalue >= 0 ? $midsearchvalue : 0)  + (is_numeric($endsearchvalue) === true && $endsearchvalue >= 0 ? $endsearchvalue : 0);
                        
    
//                         $subtotal[$singlesub->Subject_code] = $total;
//                         $subgrad[$singlesub->Subject_code] = $grad = $endsearchvalue < $singlesub->End_pass_mark ? gradefinder(0) : gradefinder(percentfinder($subjectmaxobtainmark,$total));
//                         $subgradpoint[$singlesub->Subject_code] = $grad == 'F' ? 0 : gradepointfinder(percentfinder($subjectmaxobtainmark,$total));
//                         $subcredit[$singlesub->Subject_code] = $singlesub->Credit;
//                         $subcreditpoint[$singlesub->Subject_code] = $grad == 'F' ? 0 : ((int) gradepointfinder(percentfinder($subjectmaxobtainmark,$total))) * (int) $singlesub->Credit;
//                     }
//                 }
                
//                 $endsave['Marks_total'] = json_encode($subtotal);
//                 $endsave['Marks_grade'] = json_encode($subgrad);
//                 $endsave['Marks_grade_point'] = json_encode($subgradpoint);
//                 $endsave['Marks_credit'] = json_encode($subcredit);
//                 $endsave['Marks_credit_point'] = json_encode($subcreditpoint);
                
//                 $updateendmarks = ResultData::where(['Stud_nchm_roll_number'=>$single->Stud_nchm_roll_number,'Stud_semester'=>$semester])->update($endsave);
                
//                 if($updateendmarks){

//                     $totalReappear = totalreapearconverter($single->Mid_Reappear_subject,'Mid').(strlen($reappearsub) > 0 && strlen($single->Mid_Reappear_subject) > 0 ? ', ' : '').totalreapearconverter($reappearsub,$term);
//                     $totalReappearcount = stringcount($single->Mid_Reappear_subject) + stringcount($reappearsub);
                    
//                     //get min paper clear count
//                     $passsubcount = stringcount($reappearsub) + stringcount($appearsub);

//                     ResultData::where(['Stud_nchm_roll_number'=>$single->Stud_nchm_roll_number,'Stud_semester'=>$semester])->update([
//                         'End_Reappear_subject'=> $reappearsub,
//                         'Total_Reappear_subject'=>$totalReappear,
//                         'Reappear_subject_count'=>$totalReappearcount,
//                     ]);
//                 }
//             }else{
//                 return false;
//             }
//         }
//         return true;
//     }else{
//         return false;
//     }
// }

// foreach($endmraks as $key=>$val){
//     $midcall = explode('_',$key);
    
//     $midsearch = 'Mid_'.$midcall[1];
//     $midsearchvalue = $midmraks[$midsearch];
    
    
//     $cassigned = $subject;
//     $currentsub = $cassigned->where('Subject_code',$midcall[1])->first();
    
//     $subjectmaxobtainmark = ((int) $currentsub->Mid_max_mark + (int) $currentsub->End_max_mark);
//     $total = (is_numeric($midsearchvalue) === true && $midsearchvalue >= 0 ? $midsearchvalue : 0)  + (is_numeric($val) === true && $val >= 0 ? $val : 0);
    
//     $subtotal[$midcall[1]] = number_format($total,2);
//     $subgrad[$midcall[1]] = $grad = $val < $currentsub->End_pass_mark ? gradefinder(0) : gradefinder(percentfinder($subjectmaxobtainmark,$total));
//     $subgradpoint[$midcall[1]] = $grad == 'F' ? 0 : gradepointfinder(percentfinder($subjectmaxobtainmark,$total));
//     $subcredit[$midcall[1]] = $currentsub->Credit;
//     $subcreditpoint[$midcall[1]] = $grad == 'F' ? 0 : ((int) gradepointfinder(percentfinder($subjectmaxobtainmark,$total))) * (int) $currentsub->Credit;
    
// }

function jsondecodetoarray($jsonval){
    return (array)$currentstudent = json_decode($jsonval);
}

function totalreapearconverter($current=null,$term=null){
    if(isset($current) && strlen($current) > 0){
        $list = [];
        $newarray = explode(',',str_replace(' ','',$current));
        foreach($newarray as $single){
            $list[] = $term == 'Mid' ? 'IE_'.$single : 'TE_'.$single;
        }
        $new = implode(', ',$list);
        return $new;
    }
    return null;
}

function removerepeatmidendsubject($array1,$array2) {
    $list1 = explode(',',$array1);
    $list2 = explode(',',$array2);
    foreach($list2 as $single){
        if(!in_array($single,$list1)){
            $list1[] = $single;
        }
    }
    return implode(',',$list1);
}

function optionalsubsequance($sub1=null,$sub2=null){
    if($sub1 < $sub2){
        return $sub1.'/'.$sub2;
    }else{
        return $sub2.'/'.$sub1;
    }
}

function removeDuplicateValues($inputString) {
    // Explode the input string by comma and trim each value
    $values = array_map('trim', explode(',', $inputString));

    // Remove duplicates
    $uniqueValues = array_unique($values);

    // Join the unique values back into a string separated by comma
    $resultString = implode(', ', $uniqueValues);

    return $resultString;
}

function stringcount($inputString){
    if($inputString != null){
        $values = explode(',',$inputString);
        return count($values);
    }
    
    return 0;
}

// Function to check if a number is odd or even
function checkEven($number) {
    if ($number % 2 == 0) {
        return true;
    } else {
        return false;
    }
}


// function resultgenerate($arry=null,$subject=null,$semester=null){
//     if($arry != null && count($arry) > 0){
//         $midcall = $subject;
//         $endcall = $subject;
//         $totalmid = $midcall->sum('Mid_max_mark');
//         $totalend = $endcall->sum('End_max_mark');
//         $total = $totalmid+$totalend;

//         foreach($arry as $data){
//             $excludingmark = excludingmark($data,$subject);
            
//             // decode json data of student
//             $subtotal = json_decode($data->Marks_total);
//             $subtotalcredit = json_decode($data->Marks_credit_point);
//             $subscredit = json_decode($data->Marks_credit);
            
//             // totaling data of store json data
//             $totaloftotal = array_sum((array) $subtotal);
//             $countsub = ($total - $excludingmark);
//             $totalcpoint = array_sum((array) $subtotalcredit);
//             $sgpasses = array_sum((array) $subscredit);
//             $totalpercent = number_format(percentfinder($countsub,$totaloftotal),2);

//             $sgpa = sprintf("%.2f", sgpafinder($totalcpoint,$sgpasses));

//             $grandtotal = ResultData::where(['Stud_nchm_roll_number'=>$data->Stud_nchm_roll_number,'Stud_semester'=>$data->Stud_semester])->update([
//                 'Grand_Total'=>$totaloftotal,
//                 'Grand_Total_Grade'=>gradefinder(percentfinder($countsub,$totaloftotal),2),
//                 'Grand_Credit_Point'=>$totalcpoint,
//                 'Total_Percentage'=>$totalpercent,
//                 'End_Result_SGPA'=>$sgpa,
//             ]);

//             // number_format($sgpa,2),

//             if($grandtotal){
                
//                 $halfsubject = (int)number_format((stringcount($data->Mid_Reappear_subject)+stringcount($data->Mid_Appear_subject))/2);
//                 $reappearcount = (int)number_format(stringcount($data->Total_Reappear_subject));
//                 $checkeven = checkEven($semester);
                
//                 $cgpa = sprintf("%.2f", cgpafinder($data,$semester,$checkeven,$sgpa));

//                 $result = resultfind($checkeven,$reappearcount,$cgpa,$halfsubject);
                
//                 ResultData::where(['Stud_nchm_roll_number'=>$data->Stud_nchm_roll_number,'Stud_semester'=>$data->Stud_semester])->update([
//                     'End_Result'=>$result,
//                     'End_Result_CGPA'=>$cgpa,
//                     'Result_date'=>now(),
//                 ]);
//             }
//         }

//         // number_format($cgpa,2)

//         return true;
//     }else{
//         return false;
//     }
// }

function resultfind($checkeven,$reappearcount,$cgpa,$totalsubject){
    return $checkeven != true ? ($reappearcount === 0 ? 'Pass' : ($reappearcount === $totalsubject  ? 'Fail' : 'Reappear')) : ($reappearcount === 0 ? 'Pass' : ($cgpa >= 3 ? 'Reappear' : 'Fail'));// re correction required!
}

function excludingmark($student=null,$subject=null){
    if(isset($student) && isset($subject)){
        $studentsub = explode(',',str_replace(' ','',($student['Mid_Reappear_subject'].$student['Mid_Appear_subject'])));
        $subjectsub = collect($subject)->pluck('Subject_code')->toArray();
        $diffrance = array_diff($subjectsub,$studentsub);
        $total = 0;
        foreach($diffrance as $single){
            $currentsub = collect($subject)->where('Subject_code',$single)->pluck('Mid_max_mark','End_max_mark')->toArray();
            foreach($currentsub as $key=>$val){ 
                $total += $key+$val;
            }
        }

        return $total;
    }
}

function sumofcredit($course=null,$semester=null,$optionalsub=null){
    if(isset($course)&&isset($semester)){
        $getcredit = SubjectMaster::where(['Course_code'=>$course,'Semester'=>$semester])->sum('Credit');
        $remove = 0;
        if(isset($optionalsub)){
            $remove = SubjectMaster::where(['Course_code'=>$course,'Semester'=>$semester,'Optional_subject'=>strtolower($optionalsub)])->first('Credit');
            $remove = $remove->Credit ?? 0;
        }
        return ($getcredit-$remove);
    }
}

// grade finder function 
function gradefinder($total=null){
    if($total >= 0){
        $grade = $total <= 100 && $total >= 95 ? 'A+' : ($total <= 94.99 && $total >= 85 ? 'A' : ($total <= 84.99 && $total >= 75 ? 'A-' : ($total <= 74.99 && $total >= 65 ? 'B+' : ($total <= 64.99 && $total >= 55 ? 'B' : ($total <= 54.99 && $total >= 45 ? 'B-' : ($total <= 44.99 && $total >= 35 ? 'C+' : ($total <= 34.99 && $total >= 25 ? 'C' : ($total <= 24.99 && $total >= 15 ? 'C-' : 'F'))))))));
        return $grade;
    }else{
        return 'F';
    }
}

// grade point finder function 
function gradepointfinder($total=null){
    if($total != null){
        $grade = $total <= 100 && $total >= 95 ? 9 : ($total <= 94.99 && $total >= 85 ? 8 : ($total <= 84.99 && $total >= 75 ? 7 : ($total <= 74.99 && $total >= 65 ? 6 : ($total <= 64.99 && $total >= 55 ? 5 : ($total <= 54.99 && $total >= 45 ? 4 : ($total <= 44.99 && $total >= 35 ? 3 : ($total <= 34.99 && $total >= 25 ? 2 : ($total <= 24.99 && $total >= 15 ? 1 : 0))))))));
        return $grade;
    }else{
        return false;
    }
}

function percentfinder($total=null,$mark=null){
    if(isset($total) && isset($mark)){
        return ((100/(Double)$total)*(Double)$mark);
    }else{
        return 0;
    }
}

function sgpafinder($cPoint=null,$cAssment=null){
    if(isset($cPoint) && $cPoint != 0 && isset($cAssment) && $cAssment != 0){
        return ($cPoint/$cAssment);
    }else{
        return 0;
    }
}

function cgpafinder($studentId,$semester,$evencheck){
    if(!isset($studentId) && !isset($semester)){
        return 'data not found';
    }

    $sgpa = 0;
    $totalcredit = 0;
    $semloop = $evencheck == true ? 1 : 0;
    for($i=0;$i<=$semloop;$i++){        
        $currentsem = $semester-$i;
        $marksheet = ResultData::select('id','End_Result_SGPA','Marks_credit')->where(['student_id'=>$studentId,'Stud_semester'=>$currentsem])->first();
        if(!$marksheet){
            \abort(500, "Previous Semester Data did not Exist.");
        }
        $sgpa += ($marksheet->End_Result_SGPA * array_sum((array)json_decode($marksheet->Marks_credit)));
        $totalcredit += array_sum((array)json_decode($marksheet->Marks_credit));
    }
    
    $CGPA = $sgpa/$totalcredit;
    return $CGPA;
}

function resultfiltaring($results=null){
    if(!isset($results)){ return;}
    $getfilter = reset($results);
    
    $arraynum = Subject::where([
        'course_id' => $getfilter['course_id'],
        'Semester' => $getfilter['Stud_semester']
    ])->orderBy('Subject_code')->get()->toArray();
    
    foreach ($results as $key=>$entry) {
        $totalcredit = 0;
        $subjectarray = [];
        $studentinfo = ["Stud_name"=>$entry['student']['name'],
                        "Stud_nchm_roll_number"=>$entry['student']['NCHMCT_Rollnumber'],
                        "Stud_jnu_roll_number"=>$entry['student']['JNU_Rollnumber'],
                        "Stud_course"=>$entry['course']['Course_name'],
                        "institute_id"=>$entry['institute_id'],
                        "Stud_semester"=>$entry['Stud_semester'],
                        "Stud_academic_year"=>$entry['Stud_academic_year'],
                        "End_Result_SGPA"=>$entry['End_Result_SGPA'],
                        "End_Result_CGPA"=>$entry['End_Result_CGPA']];
                        
        $currensemrecord = currentsemsterrecord($entry);
        $cumulativerecord = cumulativerecord($entry);
        $totalcredit = 0;
        $subgrad = (array)$subgrad = json_decode($entry['Marks_grade']);
        $subcredit = (array)$subcredit = json_decode($entry['Marks_credit']);
        foreach($arraynum as $data){
            if(isset($subgrad[$data['Subject_code']])){
                if($subgrad[$data['Subject_code']] != 'F'){
                    $totalcredit += $subcredit[$data['Subject_code']];
                }
                $subjectarray[removeNonDigits($data['Subject_code'])] = [
                    'coursecode' => $data['Subject_code'],
                    'coursetitle' => $data['Subject_name'],
                    'coursecredit' => $data['Credit'],
                    'coursegrade' => $subgrad[$data['Subject_code']]
                ];
            }
        }
        $results[$key] = ['data'=>$studentinfo,'currensemrecord'=>$currensemrecord, 'cumulativerecord'=>$cumulativerecord,'subjectarray' => $subjectarray, 'totalcredit' => $totalcredit];
    }
    return $results;
}


function Diplomaresultfiltaring($results=null){
    if(!isset($results)){ return;}
    $getfilter = reset($results);
    
    $arraynum = Subject::where([
        'course_id' => $getfilter['course_id'],
        'Semester' => $getfilter['Stud_semester']
    ])->orderBy('Subject_code')->get()->toArray();
    
    foreach ($results as $key=>$entry) {
        $totalmarks = (array) json_decode($entry['Marks_total']);
        $subjectarray = [];
        $studentinfo = ["Stud_name"=>$entry['student']['name'],
                        "Stud_nchm_roll_number"=>$entry['student']['NCHMCT_Rollnumber'],
                        "Stud_course"=>$entry['course']['Course_name'],
                        "institute_id"=>$entry['institute_id'],
                        "Stud_semester"=>$entry['Stud_semester'],
                        "Stud_academic_year"=>$entry['Stud_academic_year']];
        $totalmax = 0;
        $totalmin = 0;
        $totalobtain = 0;
        foreach($arraynum as $data){
            $totalmax += $data['Mid_max_mark'] + $data['End_max_mark'];
            $totalmin += $data['Mid_pass_mark'] + $data['End_pass_mark'];
            $totalobtain += floor($totalmarks[$data['Subject_code']]);
            $subjectarray[] = [
                'course_name'=>$data['Subject_name'],
                'course_type'=>$data['Subject_type'],
                'max' => $data['Mid_max_mark'] + $data['End_max_mark'],
                'min' => $data['Mid_pass_mark'] + $data['End_pass_mark'],
                'obtain' => floor($totalmarks[$data['Subject_code']])
            ];
        }
        $subjectarray['footer'] = [
            'course_name'=>'Grand Total',
            'course_type'=>'',
            'max' => $totalmax,
            'min' => $totalmin,
            'obtain' => $totalobtain
        ];
        
        $results[$key] = ['course'=>$entry['course'],'data'=>$studentinfo,'subjectarray' => $subjectarray,'result'=>$entry['Result']];
    }

    return $results;
}

function currentsemsterrecord($entry=null){
    if($entry != null){
        $currentsubcredit = json_decode($entry['Marks_credit']);
        $totalcredit = array_sum((array)$currentsubcredit);
        $currentsem = ['totalcredit'=>$totalcredit,'totalpoint'=>$entry['Grand_Credit_Point']];
        return $currentsem;
    }
}

function cumulativerecord($entry=null){//! need to go through
    if($entry != null){
        $totalcredit = 0;
        $totalpoint = 0;
        for($i=1;$i<=$entry['Stud_semester'];$i++){
            $arraynum = ResultData::select('Grand_Credit_Point','Marks_credit')->where(['student_id'=>$entry['student_id'],'Stud_semester'=>$i])->first()->toArray();
            
            $totalpoint += $arraynum['Grand_Credit_Point'] ?? 0;
            $subcredit = json_decode($arraynum['Marks_credit']);
            $totalcredit += array_sum((array)$subcredit);
        }

        $cumulative = ['totalcredit'=>$totalcredit,'totalpoint'=>$totalpoint];
        return $cumulative;
    }
}

// function backupdb($db=null){
//     if($db != null){
//         $correct = substr($db, 0 , -1);
//         $correct = $correct.'_backups';
//         $bdb[] = $correct;
//         $fkey =  substr($db, 0 , -1);
//         $fkey = $fkey.'_id';
//         array_push($bdb,$fkey);
//         return $bdb;
//     }
// }

function checkyearformat($year=null,$fomat=null){
    if($year != null && $fomat != null){
        $years = explode('-',$year);
        $format = explode('-',$fomat);
        foreach($years as $key=>$cyear){
            $nyear[$key] = substr($format[0], 0, 2).substr($cyear, -2);
        }
        $updateyear = implode('-',$nyear);
        return $updateyear;
    }else{
        return ;
    }
}


function BgColor() {
    // dd(Request::ip());
    $color = Setting::where('id',1)->first();
    return $color ? $color->bg_color : '';
}


function minpaperclear($db=null){
    if($db != null){
        $subjecttotal = SubjectMaster::where('Table_name',$db);
        if($subjecttotal){
            $optsub = 0;
            foreach($subjecttotal->get() as $sub){
                if($sub->Optional_subject !== null){
                    $optsub += 1;
                }
            }
            $total = $subjecttotal->count();
            $optsub = ($optsub/2);
            $clearpaper = isset($subjecttotal) ? (int) round(($total-$optsub)) : 0;
            return $clearpaper;
        }

        return 'Subject Not Found in Subject Master!';
    }
    return 'Semester Table name Requreid!';
}


function csc($data=null,$check=null,$link=null,$title=null){
    $provide = explode(',',$data);
    foreach($provide as $lst){
        if($lst == $check){
        
        }else{
            return false;
        }
    }
}


function uploadFile($file,$path,$applicant_id) {

    //Move Uploaded File to public folder
    $destinationPath = 'excels/'.$path;
    $imgexten = $file->getClientOriginalExtension();
    $filesave = $applicant_id.'.'.$imgexten;
    $file->move(storage_path($destinationPath), $filesave);
    $filepath = $destinationPath.'/'.$filesave;

    return $filepath;
}

function flushcache(){
    Cache::forget('subjects_list');
    Cache::forget('check_list');
    Cache::forget('previuse_list');
    Cache::forget('student_list');
}



// extra



// $total = (($midsubmark <= 100 && $midsubmark >= 0 ? (int)$midsubmark : 0) + ($endsubmark <= 100 && $endsubmark >= 0 ? (int)$endsubmark : 0));
                    
// $cassigned = SubjectMaster::where(['Semester'=>$semester,'Subject_code'=>strtolower($mark[$i]['Subject_code'])])->first();

// $grad = gradefinder(percentfinder($cassigned->End_max_mark,(int)$endsubmark)) === 'F' ? gradefinder(percentfinder($cassigned->End_max_mark,(int)$endsubmark)) : gradefinder(percentfinder(100,$total));

// $gradpoint = gradepointfinder(percentfinder(100,$total));

// $creditpoint = ((int) gradepointfinder(percentfinder(100,$total)) * (int) $cassigned->Credit);

