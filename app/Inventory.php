<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use App\User;

class Inventory extends Model
{
	use SoftDeletes;
	
    protected $fillable = [
    	'product_id', 'product_name', 'cost_price', 'price', 'quantity', 'category', 'unit', 'threshold'
    ];

}
