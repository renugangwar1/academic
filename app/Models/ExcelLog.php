<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExcelLog extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class);
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'excel_title',
        'excel_link',
        'UserName',
        'Tearm',
        'Batch',
        'system',
    ];
}
