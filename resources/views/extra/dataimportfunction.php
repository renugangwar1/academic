$getsubjects = SubjectMaster::where('Table_name',$this->tb)->orderBy('Subject_code')->get();

        $check = DB::table($this->tb)->where([
            'Stud_name'=>$row['student_name'],
            'Stud_nchm_roll_number'=>$row['student_nchm_roll_number'],
            'Stud_jnu_roll_number'=>$row['student_jnu_roll_number'],
            'Stud_course'=>$this->course,
            'Stud_batch'=>$this->batch,
            'Stud_semester'=>$this->samester,
            'Stud_academic_year'=>$row['student_academic_year'],
        ]);
        
        if($this->institute != null){
            $check = $check->where('institute_id',$this->institute)->first();
        }else{
            $check = $check->first();
        }

        $primary = [
        'Stud_name'=>$row['student_name'],
        'Stud_nchm_roll_number'=>$row['student_nchm_roll_number'],
        'Stud_jnu_roll_number'=>$row['student_jnu_roll_number'],
        'Stud_course'=>$this->course,
        'Stud_batch'=>$this->batch,
        'Stud_semester'=>$this->samester,
        'Stud_academic_year'=>$row['student_academic_year'],
        'institute_id'=>$this->institute,
        'system'=>Request::getClientIp()];

        // mid term marks addon
        $midaddon = $primary;

        foreach($getsubjects as $single){
            $midaddon['Mid_'.strtoupper($single->Subject_code)] = $row['mid_'.$single->Subject_code];
        }

        // end term marks addon
        $endaddon = $midaddon;

        foreach($getsubjects as $single){
            $endaddon['End_'.strtoupper($single->Subject_code)] = $row['end_'.$single->Subject_code];
        }

        if($check == null){
            $endaddon['created_at'] = now();
            $endaddon['updated_at'] = now();
            DB::table($this->tb)->insert($endaddon);
        }else{
            $backupdb = backupdb($this->tb);
            $keyfor = $backupdb[1];
            $dbfor = $backupdb[0];
            $check[$keyfor] = 0;
            dd($check);
            if($check->End_Result != null && $check->End_Result == 'Reappear'){
                $check[$keyfor] = $check->id;
                $check->makeHidden(['id']);
                DB::table($dbfor)->insert($check->toArray());
            }
            
            $endaddon['updated_at'] = now();
            
            DB::table($this->tb)->where([
                'Stud_name'=>$row['student_name'],
                'Stud_nchm_roll_number'=>$row['student_nchm_roll_number'],
                'Stud_jnu_roll_number'=>$row['student_jnu_roll_number'],
                'Stud_course'=>$this->course,
                'Stud_batch'=>$this->batch,
                'Stud_semester'=>$this->samester,
                'Stud_academic_year'=>$row['student_academic_year'],
                'institute_id'=>$this->institute,
            ])->update($endaddon);
        }