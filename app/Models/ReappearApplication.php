<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReappearApplication extends Model
{
    use HasFactory,SoftDeletes;

    public function Course(){
        return $this->belongsTo(Course::class);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'course_id',
        'semester',
        'batch',
        'Reappear_applaid',
        'Reappear_fee',
        'Reappear_late_fee',
        'Reappear_payment_status',
        'system',
    ];
}
