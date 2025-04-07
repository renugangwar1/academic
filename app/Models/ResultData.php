<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\institute;
use App\Models\course;
use App\Models\Student;

class ResultData extends Model
{
    use HasFactory,SoftDeletes;

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
        
        'Mid_Reappear_subject',
        'Mid_Appear_subject',
        'Reappear_subject_count',
        'Mid_Result',

        'Mid_marks',
        'End_marks',
        'Marks_total',
        'Marks_grade',
        'Marks_grade_point',
        'Marks_credit',
        'Marks_credit_point',
        
        'Grand_Total',
        'Grand_Total_Grade',
        'Grand_Credit_Point',
        'Total_Percentage',
        'End_Result',
        'End_Reappear_subject',
        'Total_Reappear_subject',
        'Optional_subject',
        
        'End_Result_SGPA',
        'End_Result_CGPA',

        'Mid_apear_status',
        'End_apear_status',

        'Compaile_date',
        'Result_date',
        'system',
    ];
}
