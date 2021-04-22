<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;


class Store extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'manager', 'address', 'target', 'stock_limit'
    ];

    public function getManagerAttribute()
    {
        $id = $this->attributes['manager'];
        
      
        $store_user = User::where('deleted_at', NULL)->find($id);
        if ($store_user) {
            return $store_user->name;
        }
    }

    protected $dates = [
        'deleted_at', 
    ];

    public function user()
    {
        return $this->belongsToMany('App\User');
    }

    public function inventory()
    {
        return $this->belongsToMany('App\Inventory');
    }
}
