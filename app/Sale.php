<?php

namespace App;

use App\User;
use App\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Sale extends Model
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
        'sale_id', 'cart', 'totalPrice', 'percent_discount', 'initialPrice', 'discount', 'amount_paid', 'user_id', 'mop', 'buyer', 'store_id', 'main_date', 'sec_id', 'account', 'status', 'cashier_id', 'market_id'
    ];

    protected $dates = [
        'deleted_at', 'main_date',
    ];

     public function getStoreIdAttribute()
    {
        $store_id = $this->attributes['store_id'];
        if ($store_id) {
            $store = Store::where('deleted_at', NULL)->find($store_id);
            if ($store) {
                return $store->name;
            }
            else {
               return '---';
            }
        }
        else {
           return '---';
        }
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
      'remember_token', 'deleted_at'
    ];

  }
