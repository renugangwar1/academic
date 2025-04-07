<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReappearSetting extends Model
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
        'course_id',
        'semester',
        'batch',
        'Reappear_from_date',
        'Reappear_to_date',
        'Reappear_late_fee_date',
        'Reappear_late_fee',
        'system',
    ];
}
