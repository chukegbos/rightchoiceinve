<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\IncomeCategory;
use App\Account;
use App\Store;
use Illuminate\Support\Str;
use DB;
class IncomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('api');
    }

    public function index(Request $request)
    {
        $params = [];
        $params['stores'] = Store::where('deleted_at', NULL)->get();
        $params['categories'] = IncomeCategory::where('deleted_at', NULL)->get();
        $query =  Account::where('accounts.deleted_at', NULL)
            ->where('accounts.type', 1)
            ->join('income_categories', 'accounts.category', '=', 'income_categories.id')
            ->join('stores', 'accounts.store', '=', 'stores.id')
            ->join('users', 'accounts.user_id', '=', 'users.id');
            if(auth('api')->user()->role != 3)
            {
                $query->where('accounts.store', auth('api')->user()->store);
            }

            if ($request->start_date) {
                $query->where('accounts.main_date', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $query->where('accounts.main_date', '<=', $request->end_date . ' 23:59');
            }

            if($request->category)
            {
                $query->where('accounts.category', $request->category);
            }

            if($request->store)
            {
                $query->where('accounts.store', $request->store);
            }

            $params['accounts'] = $query->select(
                'accounts.ref_id as ref_id',
                'accounts.id as id',
                'income_categories.name as category',
                'stores.name as store',
                'users.name as user_id',
                'accounts.amount as amount',
                'accounts.purpose as purpose',
                'accounts.main_date as main_date'
            )
            ->orderBy('main_date', 'desc')
            ->paginate(10);
        return $params;
    }

    public function balance(Request $request)
    {
        $query =  Account::where('accounts.deleted_at', NULL);

        if(auth('api')->user()->role != 3)
        {
            $query->where('accounts.store', auth('api')->user()->store);
        }

        if ($request->start_date) {
            $query->where('accounts.main_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('accounts.main_date', '<=', $request->end_date . ' 23:59');
        }

        $query->select(
            'accounts.main_date as main_date'
        );

        $query->select(DB::raw("Sum(amount_debit) As expenditure, Sum(amount_credit) As income, accounts.main_date as main_date"));

        $accounts = $query->orderBy('main_date', 'desc')->groupBy('main_date')->paginate(10);
      
        return $accounts;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|integer',
            'main_date' => 'required',
        ]);

        $ref_id = rand(2,999999);

        return Account::create([
            'ref_id' => $ref_id,
            'category' => $request->category,
            'purpose' => $request->purpose,
            'amount' => $request->amount,
            'main_date' => $request->main_date,  
            'store' => auth('api')->user()->store,
            'user_id' => auth('api')->user()->id,
            'type' => 1,
        ]);
    }

    public function destroy($id)
    {
        $account = Account::where('deleted_at', NULL)->find($id);
        $account->delete();
    }
}
