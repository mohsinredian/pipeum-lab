<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Otps;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRolesAndAbilities;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'restaurant_id',
        'password',
        'role_id',
        'username',
        'email_verified_at',
        'department_id',
        'mobile_number',
        'super_department',
        'rights',
        'last_password_change_date',
        'designation'
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
        'email_verified_at' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function otp()
    {
        return $this->hasMany(\App\Models\Otps::class);
    }


        public function report(){
            return $this->hasMany(Employee::class, 'report_to');}
}
