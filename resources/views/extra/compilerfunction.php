function compailer($arry=null,$db=null,$sub=null,$lenth=null,$term=null,$subcode=null,$semester=null){
    if($arry != null){
        foreach($arry as $single){
            if($db != null && $sub != null && $lenth != null && $term != null && $subcode != null){
                $appearsub = '';
                $reappearsub = '';
                $totalReappear = '';
                for($i=1;$i<=$lenth;$i++){
                    
                    $currentsubject = $term.$sub.($subcode+$i);
                    $recordsubject = $sub.($subcode+$i);
                    
                    $rmark = $term.'pass_mark';
                    
                    $mark = SubjectMaster::where(['Semester'=>$semester,'Subject_code'=>strtolower($recordsubject)])->first();
                    
                    if($mark->Optional_subject == null){
                        if($single->$currentsubject != null && $single->$currentsubject >= $mark->$rmark){
                            $appearsub = $appearsub.($appearsub != null ? ' , ' : '').$recordsubject;
                        }else{
                            $reappearsub = $reappearsub.($reappearsub != null ? ' , ' : '').$recordsubject;
                            $totalReappear = $totalReappear.($totalReappear != null ? ' , ' : '').$term.$recordsubject;
                        } 
                    }else{
                        $optionsub = $term.strtoupper($mark->Optional_subject);
                        $recordsubject = optionalsubsequance($recordsubject,strtoupper($mark->Optional_subject));
                        if($single->$currentsubject != null && $single->$currentsubject >= $mark->$rmark){
                            $appearsub = $appearsub.($appearsub != null ? ' , ' : '').$recordsubject;
                        }else{
                            if($single->$optionsub == null){
                                $reappearsub = $reappearsub.($reappearsub != null ? ' , ' : '').$recordsubject;
                                $totalReappear = $totalReappear.($totalReappear != null ? ' , ' : '').$term.$recordsubject;
                            }
                        }
                    }
                }
            }else{
                return false;
            }
            if($term == 'Mid_'){
                DB::table($db)->where('id',$single->id)->update([
                    'Mid_Reappear_subject'=>removeDuplicateValues($reappearsub),
                    'Mid_Appear_subject'=>removeDuplicateValues($appearsub),
                    'Reappear_subject_count'=>stringcount(removeDuplicateValues($reappearsub)),
                    'Mid_Result'=>stringcount(removeDuplicateValues($reappearsub)) == 0 ? 'Pass' : (stringcount(removeDuplicateValues($reappearsub)) <= 5 ? 'Reappear' : (stringcount(removeDuplicateValues($reappearsub)) > 5 && $semester < 3 ? 'Reappear' : 'Fail')),
                    'Total_Reappear_subject'=>removeDuplicateValues($totalReappear),
                ]);
            }else{
                for($i=1;$i<=$lenth;$i++){
                    $subendcode = $sub.($subcode+$i);
                    $endsub = $term.$subendcode;
                    $totalsub = 'Total_'.$subendcode;
                    $gradesub = 'Grade_'.$subendcode;
                    $midmark = 'Mid_'.$sub.($subcode+$i);
                    $gpoint = 'Grade_Point_'.$subendcode;
                    $cpoint = 'Credit_Point_'.$subendcode;
                    
                    $total = ((int)$single->$midmark + (int)$single->$endsub);

                    $cassigned = SubjectMaster::where(['Semester'=>$semester,'Subject_code'=>strtolower($subendcode)])->first();
                    
                    DB::table($db)->where('id',$single->id)->update([
                        $totalsub=>$total,
                        $gradesub=>gradefinder(percentfinder(100,$total)),
                        $gpoint=>gradepointfinder(percentfinder(100,$total)),
                        $cpoint=>((int) gradepointfinder(percentfinder(100,$total)) * (int) $cassigned->Credit),
                    ]);

                }

                $check = DB::table($db)->where('id',$single->id)->first();

                if(stringcount($check->Total_Reappear_subject) > 0){
                    $totalReappear = $totalReappear.','.$check->Total_Reappear_subject;
                }
                
                DB::table($db)->where('id',$single->id)->update([
                    'End_Reappear_subject'=> removeDuplicateValues($reappearsub),
                    'End_Result'=>stringcount(removeDuplicateValues($reappearsub)) == 0 ? 'Pass' : (stringcount(removeDuplicateValues($reappearsub)) <= 5 ? 'Reappear' : (stringcount(removeDuplicateValues($reappearsub)) > 5 && $semester < 3 ? 'Reappear' : 'Fail')),
                    'Total_Reappear_subject'=> removeDuplicateValues($totalReappear),
                ]);
            }
        }
    }else{
        return false;
    }
}





// if($request->course == 'm.sc.ha' && $request->batch != null && $request->semester != null){

//     $data =  Msc::where([
//         'Stud_course'=>$request->course,
//         'Stud_batch'=>$request->batch,
//         'Stud_semester'=>$request->semester,
//     ]);

//     if($request->institute != null){
//         $data = $data->where('institute_id',$request->institute);
//     }

//     $sql = $data->get();
    
//     compailer($sql,'mscs','MHA',5,$request->term,700,$request->semester);

//     $compileresult = Msc::where([
//         'Stud_course'=>$request->course,
//         'Stud_batch'=>$request->batch,
//         'Stud_semester'=>$request->semester,
//         'institute_id'=>$request->institute,
//     ])->get();

//     if($request->term == 'Mid_'){
//         $view = view('components.midcompile',compact('compileresult'));
//     }else if($request->term == 'End_'){
//         $view = view('components.endcompile',compact('compileresult'));
//     }

//     return $view;
// }else if($request->course == 'b.sc.h&ha' && $request->batch != null && $request->semester != null){
    
//     $data =  Bsc::where([
//         'Stud_course'=>$request->course,
//         'Stud_batch'=>$request->batch,
//         'Stud_semester'=>$request->semester,
//         'institute_id'=>$request->institute,
//     ]);

//     if($request->institute != null){
//         $data = $data->where('institute_id',$request->institute);
//     }

//     $sql = $data->get();

//     compailer($sql,'bscs','BHA',11,$request->term,100,$request->semester);
    
//     $compileresult = Bsc::where([
//         'Stud_course'=>$request->course,
//         'Stud_batch'=>$request->batch,
//         'Stud_semester'=>$request->semester,
//         'institute_id'=>$request->institute,
//     ])->get();

//     if($request->term == 'Mid_'){
//         $view = view('components.midcompile',compact('compileresult'));
//     }else if($request->term == 'End_'){
//         $view = view('components.endcompile',compact('compileresult'));
//     }

//     return $view;
// }else{
//     $errorMessage = 'An error occurred during compilation.';
//     session()->flash('error', $errorMessage);
// }



// if($request->course == 'm.sc.ha'){
//     $getacdmitcard = Msc::where(['Stud_semester'=>$request->semester,'Stud_batch'=>$request->batch])->paginate(15)->withQueryString();
//     if(count($getacdmitcard) > 0){
//         return view('admitcarview',compact('getacdmitcard'));
//     }else{
//         return back()->withErrors('This data is not Compiled!');
//     }
// }else if($request->course == 'b.sc.h&ha'){
//     $getacdmitcard = Bsc::where(['Stud_semester'=>$request->semester,'Stud_batch'=>$request->batch])->paginate(15)->withQueryString();
//     if(count($getacdmitcard) > 0){
//         return view('admitcarview',compact('getacdmitcard'));
//     }else{
//         return back()->withErrors('This data is not Compiled!');
//     }
// }else{
//     return back()->withErrors('Please Provide Valid Details');
// }