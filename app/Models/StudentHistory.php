<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentHistory extends Model
{
    use HasFactory,SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'name',
        'NCHMCT_Rollnumber',
        'JNU_Rollnumber',
        'course_id',
        'batch',
        'institute_id',
        'optionalSubject',
        'email',
        'created_at',
        'updated_at',
        'deleted_at',
        'system',
    ];
}
