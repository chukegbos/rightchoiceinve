<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sitename', 'address', 'phone', 'email', 'logo', 'about', 'facebook', 'linkedin', 'twitter', 'instagram', 'youtube', 'vision', 'mission', 'admin_percent', 'manager_percent', 'cashier_percent', 'naira_value', 'working_capital', 'percent_gain'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
      'remember_token', 'deleted_at', 
    ];
}