<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use App\User;

class RoomMovement extends Model
{
    use SoftDeletes;
    protected $table = 'rooom_movement';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id', 'product_id', 'qty', 'user_id', 'ref_id', 'status', 'move_type', 'moved', 'manager_id'
    ];

    protected $dates = [
        'deleted_at', 
    ];

    public function getMovedAttribute()
    {
        $moved = $this->attributes['moved'];
        if ($moved) {
            $store = Store::where('deleted_at', NULL)->find($moved);
            if ($store) {
                return $store->name;
            }
            else{
                return 'From Purchase';
            }
        }
        else{
            return '---';
        }
    }

    public function getUserIdAttribute()
    {
        $user_id = $this->attributes['user_id'];
        if ($user_id) {
            $user = User::where('deleted_at', NULL)->find($user_id);
            if ($user) {
                return $user->name;
            }
            else{
                return '---';
            }
        }
        else{
            return '---';
        }
    }

    public function getManagerIdAttribute()
    {
        $user_id = $this->attributes['manager_id'];
        if ($user_id) {
            $user = User::where('deleted_at', NULL)->find($user_id);
            if ($user) {
                return $user->name;
            }
            else{
                return '---';
            }
        }
        else{
            return '---';
        }
    }
}
