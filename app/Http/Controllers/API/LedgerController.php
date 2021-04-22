<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Ledger;
use App\Setting;
use App\Account;
use App\AccountType;
use Illuminate\Support\Str;
use DB;

class LedgerController extends Controller
{
    public function __construct()
    {
        $this->middleware('api');
    }


    public function index(Request $request)
    {
        $params = [];

        $query =  Ledger::where('deleted_at', NULL)->orderBy('ledger_date', 'desc')->where('position', '!=', 0);
        if($request->account)
        {
            $params['account']  = Account::where('deleted_at', NULL)->where('ref_id', $request->account)->first();
            $query->where('account', $params['account']->id);
        }

        if ($request->start_date) {
            $query->where('ledger_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('ledger_date', '<=', $request->end_date . ' 23:59');
        }

        if ($request->selected==0) {
            $params['ledgers'] =  $query->get();
        }
        else{
            $params['ledgers']  =  $query->paginate($request->selected);
        }


        $query1 =  Ledger::where('deleted_at', NULL)->where('position', '!=', 0);
        if($request->account)
        {
            $params['account']  = Account::where('deleted_at', NULL)->where('ref_id', $request->account)->first();
            $query1->where('account', $params['account']->id);
        }

        if ($request->start_date) {
            $query1->where('ledger_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query1->where('ledger_date', '<=', $request->end_date . ' 23:59');
        }

        $params['all'] = $query1->count();




        //Ledger
        $params['grouped_ledgers'] = Ledger::where('deleted_at', NULL)->groupBY('account')->get();
        
        $params['all_accounts'] = array();

        foreach ($params['grouped_ledgers'] as $ledger) {
            $each_account = $ledger->getOriginal('account');

            $get_acc = Ledger::where('deleted_at', NULL)->where('account', $each_account);

            if ($request->start_date) {
                $get_acc->where('ledger_date', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $get_acc->where('ledger_date', '<=', $request->end_date . ' 23:59');
            }

            if ($request->selected==0) {
                $get_accounts = $get_acc->get();
            }
            else{
                $get_accounts =  $get_acc->paginate($request->selected);
            }

            array_push($params['all_accounts'], $get_accounts);
        }
        return $params;
    }

    public function ledgerID()
    {
        $last_ledger = Ledger::where('deleted_at', null)->latest()->first();
        if (!$last_ledger) {
            $ledger_id = 100000;
        }
        else{
           $ledger_id = $last_ledger->ledger_id + 100; 
        }

        return $ledger_id;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required',
            'ledger_date' => 'required',
            'account_one' => 'required',
            'account_two' => 'required',
            'description' => 'required',
        ]);

        $ledger_id = $this->ledgerID();

        $account_one = Account::where('deleted_at', NULL)->find($request->account_one);
        $account_type_one = AccountType::where('deleted_at', NULL)->find($account_one->type);

        $account_two = Account::where('deleted_at', NULL)->find($request->account_two);
        $account_type_two = AccountType::where('deleted_at', NULL)->find($account_two->type);

        Ledger::create([
            'ledger_id' => $ledger_id,
            'ledger_date' => $request->ledger_date,
            'account' => $request->account_one,
            'amount' => $request->amount,
            'debit' => $request->amount,
            'credit' => 0,
            'description' => $request->description,
            'position' => 1,
        ]);

        Ledger::create([
            'ledger_id' => $ledger_id,
            'ledger_date' => $request->ledger_date,
            'account' => $request->account_two,
            'amount' => $request->amount,
            'debit' => 0,
            'credit' => $request->amount,
            'description' => $request->description,
            'position' => 2,
        ]);
        return 'ok';
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'account' => 'required',
        ]);

        $account = Account::where('deleted_at', NULL)->find($id);

        $account->update([
            'account' => $request->account,
            'currency' => $request->currency,
            'type' => ($request->type) ? $request->type : 0,
            'description' => $request->description,
            'tax_line' => ($request->tax_line) ? $request->tax_line : 0,
        ]);

        return 'ok';
    }

    public function destroy($id)
    {
        $account = Account::where('deleted_at', NULL)->find($id);
        $account->delete();
    }
}
