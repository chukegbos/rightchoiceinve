<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Debtor extends Model
{
    /*public function user(){
    	return $this->belongsTo('App\User');
    }
    */
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trans_id', 'user_id', 'status', 'amount', 'amount_paid', 'repayment_date'
    ];

    protected $dates = [
        'deleted_at'
    ];

    
    protected $hidden = [
      'remember_token', 'deleted_at'
    ];

    public function getUserIdAttribute()
    {
        $id = $this->attributes['user_id'];
        
      
        $user = User::where('deleted_at', NULL)->find($id);
        if ($user) {
            return $user->name;
        }
        else{
            return '---';
        }
    }

    /*public function getAmountAttribute()
    {
        $amount = $this->attributes['amount'];
        return ((7.5/100) * $amount) + $amount;
    }*/

  }
