<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'address', 'store', 'sale_percent', 'next_of_kin_address', 'next_of_kin_phone', 'next_of_kin', 'state', 'city', 'image', 'unique_id', 'salary', 'credit_unit', 'wallet_balance', 'c_person', 'pin', 'invoice'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at', 
    ];

    public function getRoleAttribute()
    {
        $role = $this->attributes['role'];
        if ($role==1) {
            return 'None';
        }
        elseif ($role==2) {
            return 'Marketing';
        }

        elseif ($role==3) {
            return 'Admin';
        }

        elseif ($role==4) {
            return 'Accounting';
        }

        else{
            return 'Customer';
        }
    }
}
