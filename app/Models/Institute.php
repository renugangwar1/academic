<?php

namespace App\Models;

use App\Notifications\InstituteResetPasswordNotification;
use App\Notifications\VerifyInstituteEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Institute extends Authenticatable implements MustVerifyEmail
{
    use Notifiable,SoftDeletes;

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyInstituteEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new InstituteResetPasswordNotification($token, $this->email));
    }
    
    
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'InstituteName',
        'InstituteCode',
        'email',
        'password',
        'system',
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
    ];
}
