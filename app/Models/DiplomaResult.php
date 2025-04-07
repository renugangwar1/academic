<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiplomaResult extends Model
{
    use HasFactory;

    public function institute(){
        return $this->belongsTo(Institute::class);
    }

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function student(){
        return $this->belongsTo(Student::class)->select('id','institute_id','course_id','name','NCHMCT_Rollnumber','JNU_Rollnumber','batch','optionalSubject');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'course_id',
        'Stud_batch',
        'Stud_semester',
        'Stud_academic_year',
        'institute_id',
        
        'Mid_marks',
        'End_marks',
        'Marks_total',
        
        'Grand_Total',
        'Total_Percentage',
        'Result',
        'Reappear_subject',
        'Reappear_subject_count',
        
        'Mid_apear_status',
        'End_apear_status',

        'Result_date',
        'system',
    ];
}
