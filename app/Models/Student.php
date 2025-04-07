<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\StudentResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Course;
use App\Models\ResultData;

class Student extends Authenticatable implements MustVerifyEmail
{
    use Notifiable,SoftDeletes;
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new StudentResetPasswordNotification($token, $this->email));
    }

    
    use HasFactory;
    
    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function resultdata(){
        return $this->hasMany(ResultData::class);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'NCHMCT_Rollnumber',
        'JNU_Rollnumber',
        'course_id',
        'batch',
        'password',
        'system',
        'institute_id',
        'optionalSubject',
        'email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'optionalSubject' => 'array',
    ];
}
