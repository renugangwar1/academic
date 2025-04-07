<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\ExcludeITScope;

class Subject extends Model
{
    use HasFactory,SoftDeletes;

    // protected static function booted()
    // {
    //     static::addGlobalScope(new ExcludeITScope);
    // }

    public function Course(){
        return $this->belongsTo(Course::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'Subject_code',
        'Subject_name',
        'Subject_type',
        'Semester',
        'Reappear_fee',
        'Optional_subject',
        'Credit',
        'Mid_max_mark',
        'Mid_pass_mark',
        'End_max_mark',
        'End_pass_mark',
        'It_status',
        'system',
    ];
}
