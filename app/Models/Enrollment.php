<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Institute;


class Enrollment extends Model
{
    use HasFactory,softDeletes;
    
    protected $table = 'enrollments';

    protected $fillable = [
        'institute_id',
        'year_of_admission',
        'institute_name',
        'chapter_name',
        'programme_of_study',
        'student_name_en',
        'student_name_hi',
        'date_of_birth',
        'category',
        'pwbd_category',
        'hh_oh_vh',
        'percentage',
        'father_name',
        'father_mobile',
        'mother_name',
        'guardian_name',
        'local_address',
        'permanent_address',
        'state_of_domicile',
        'nationality',
        'student_email',
        'student_mobile',
        'abc_id',
        'student_image',
        'nchm_roll_no',
        'board_10th', 'school_10th', 'year_10th', 'subject_10th', 'percentage_10th',
        'board_12th', 'school_12th', 'year_12th', 'subject_12th', 'percentage_12th',
        'board_other', 'school_other', 'year_other', 'subject_other', 'percentage_other',
     
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class, 'institute_id');
    }
}
