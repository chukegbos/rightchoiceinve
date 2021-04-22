<?php

namespace App;

use App\User;
use App\AccountType;
use App\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Ledger extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ledger_id', 'account', 'debit', 'credit', 'ledger_date', 'description', 'user_id', 'outlet_id', 'position', 'amount'
    ];

    protected $dates = [
        'deleted_at', 'main_date', 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
      'remember_token', 'deleted_at'
    ];

    public function getAccountAttribute()
    {
        $account_id = $this->attributes['account'];
       
        $account = Account::where('deleted_at', NULL)->find($account_id);
        return $account->account;
    }
  }
