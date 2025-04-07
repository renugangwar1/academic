function resultgenerate($arry=null,$db=null,$sub=null,$lenth=null,$subcode=null,$semester=null){
if($arry != null && count($arry) > 0){
foreach($arry as $data){
$totaloftotal = 0;
$totalcpoint = 0;
$sgpasses = 0;
$cgpatotal = 0;
for($i=1; $i <= $lenth; $i++){ $searchval='Total_' .$sub.((int)$subcode+$i); $searcpval='Credit_Point_' .$sub.((int)$subcode+$i); $midsub='Mid_' .$sub.((int)$subcode+$i); if(isset($data->$midsub) && $data->$midsub >= 0){
    $getsgpacredit = SubjectMaster::where(['Semester'=>$semester,'Subject_code'=>strtolower($sub.((int)$subcode+$i))])->first();
    $sgpasses = $sgpasses + (int)$getsgpacredit->Credit;
    }
    if(isset($data->$midsub) && $data->$midsub >= 0){
    for($j=1; $semester>$j; $j++){
    $getcgpacredit = DB::table($db)->where(['id'=>$data->id,'Stud_semester'=>$j])->first();
    $cgpatotal = $cgpatotal + (int)$getcgpacredit->End_Result_SGPA;
    }
    }
    $totaloftotal = $totaloftotal + $data->$searchval;
    $totalcpoint = $totalcpoint + $data->$searcpval;
    }

    $countsub = (stringcount($data->Mid_Reappear_subject) + stringcount($data->Mid_Appear_subject)).'00';

    $sgpa = sgpafinder($totalcpoint,$sgpasses);
    $cgpa = $cgpatotal + sgpafinder($totalcpoint,$sgpasses);

    DB::table($db)->where('id',$data->id)->update([
    'Grand_Total'=>$totaloftotal,
    'Grand_Total_Grade'=>gradefinder(percentfinder((int)$countsub,$totaloftotal)),
    'Grand_Credit_Point'=>$totalcpoint,
    'Total_Percentage'=>percentfinder((int)$countsub,$totaloftotal),
    'End_Result_SGPA'=>$sgpa,
    'End_Result_CGPA'=>$cgpa,
    ]);
    }

    return true;
    }else{
    return false;
    }
    }










    // if($request->course == 'm.sc.ha' && $request->batch != null && $request->semester != null){

    // $data = Msc::where([
    // 'Stud_course'=>$request->course,
    // 'Stud_batch'=>$request->batch,
    // 'Stud_semester'=>$request->semester,
    // ]);

    // if($request->institute != null){
    // $data = $data->orwhere('institute_id',$request->institute);
    // }

    // $sql = $data->get();

    // resultgenerate($sql,'mscs','MHA',5,700,$request->semester);

    // $generateresult = Msc::where([
    // 'Stud_course'=>$request->course,
    // 'Stud_batch'=>$request->batch,
    // 'Stud_semester'=>$request->semester,
    // ]);

    // if($request->institute){
    // $generateresult = $generateresult->orwhere('institute_id',$request->institute);
    // }

    // $result = $generateresult->get();
    // if(count($result) > 0){
    // $view = view('components.resultcompile',compact('result'));

    // return $view;
    // }else{
    // return response()->json(['Data not Avilable!'], 500);
    // }

    // }else if($request->course == 'b.sc.h&ha' && $request->batch != null && $request->semester != null){

    // $data = Bsc::where([
    // 'Stud_course'=>$request->course,
    // 'Stud_batch'=>$request->batch,
    // 'Stud_semester'=>$request->semester,
    // ]);

    // if($request->institute != null){
    // $data = $data->orwhere('institute_id',$request->institute);
    // }

    // $sql = $data->get();

    // resultgenerate($sql,'bscs','BHA',11,100,$request->semester);

    // $generateresult = Bsc::where([
    // 'Stud_course'=>$request->course,
    // 'Stud_batch'=>$request->batch,
    // 'Stud_semester'=>$request->semester,
    // ]);

    // if($request->institute){
    // $generateresult = $generateresult->orwhere('institute_id',$request->institute);
    // }

    // $result = $generateresult->get();

    // if(count($result) > 0){
    // $view = view('components.resultcompile',compact('result'));

    // return $view;
    // }else{
    // return response()->json(['Data not Avilable!'], 500);
    // }

    // }else{
    // return response()->json(['Please Provide Valid Details.'], 500);
    // }


    // if($request->course == 'b.sc.h&ha'){
    // $generateresult = Bsc::where([
    // 'Stud_batch'=>$request->batch,
    // 'Stud_semester'=>$request->semester,
    // ]);

    // if($request->institute){
    // $generateresult = $generateresult->orwhere('institute_id',$request->institute);
    // }

    // $results = $generateresult->where('End_Result','Pass')->where('End_Result_SGPA','!=',null)->where('End_Result_CGPA','!=',null)->paginate(15)->withQueryString();
    // if(count($results) > 0){
    // return view('resultview',compact('results'));
    // }else{
    // return back()->withErrors('Result not generated yet!');
    // }
    // }else{
    // $generateresult = Msc::where([
    // 'Stud_batch'=>$request->batch,
    // 'Stud_semester'=>$request->semester,
    // ]);

    // if($request->institute){
    // $generateresult = $generateresult->orwhere('institute_id',$request->institute);
    // }

    // $results = $generateresult->where('End_Result','Pass')->where('End_Result_SGPA','!=',null)->where('End_Result_CGPA','!=',null)->paginate(15)->withQueryString();

    // if(count($results) > 0){
    // return view('resultview',compact('results'));
    // }else{
    // return back()->withErrors('Result not generated yet!');
    // }
    // }