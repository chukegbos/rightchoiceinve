<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\IncomeCategory;
use App\Account;
use App\Ledger;
use Illuminate\Support\Str;
use DB;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('api');
    }


    public function index(Request $request)
    {
        $query =  Account::where('accounts.deleted_at', NULL)
            ->join('account_types', 'accounts.type', '=', 'account_types.id');

        if($request->account)
        {
            $query->where('accounts.account', $request->account);
        }

        if($request->ref_id)
        {
            $query->where('accounts.ref_id', $request->ref_id);
        }

        if($request->type)
        {
            $query->where('accounts.type', $request->type);
        }

        if($request->tax_line)
        {
            $query->where('accounts.tax_line', $request->tax_line);
        }
        
        $account = $query->orderBy('accounts.ref_id', 'asc')
            ->select(
                'accounts.id as id',
                'accounts.ref_id as ref_id',
                'accounts.account as account',
                'accounts.currency as currency',
                'accounts.balance_total as balance_total',
                'accounts.description as description',
                'accounts.tax_line as tax_line',
                'account_types.name as type',
                'account_types.id as type_id'
            )
            ->paginate(10);
        return $account;
    }

    public function trialbalance(Request $request)
    {
        $params = [];
        $params['grouped_ledgers'] = Ledger::where('deleted_at', NULL)->groupBY('account')->get();
        
        $params['all_accounts'] = array();

        foreach ($params['grouped_ledgers'] as $ledger) {
            $each_account = $ledger->getOriginal('account');

            $query = Ledger::where('deleted_at', NULL)->where('account', $each_account);

            if ($request->end_date) {
                $query->where('ledgers.ledger_date', '<=', $request->end_date);
            }  

       
                
            $query->select(DB::raw("SUM(debit) As debit, SUM(credit) As credit"))
            ->addSelect(
                'account as account'
            )
            ->groupBy('ledgers.account')->where('ledgers.position', 1);

            $get_accounts = $query->get();
            array_push($params['all_accounts'], $get_accounts);
        }



        $query1 =  Ledger::where('ledgers.deleted_at', NULL);

        if ($request->end_date) {
            $query1->where('ledgers.ledger_date', '<=', $request->end_date);
        }   



        $params['account'] = $query1->get();

        $get_total_debit = array();
        $get_total_credit = array();

        foreach ($params['account'] as $account) {
            array_push($get_total_credit, $account->credit);
            array_push($get_total_debit, $account->debit);
        }

        $params['total_credit'] = array_sum($get_total_credit);
        $params['total_debit'] = array_sum($get_total_debit);
        

        return $params;
    }

    public function balancesheet(Request $request)
    {
        $params = [];

        //income
        $query_income =  Ledger::where('ledgers.deleted_at', NULL)
            ->where('ledgers.debit', 0)
            ->join('accounts', 'ledgers.account', '=', 'accounts.id')
            ->where('accounts.deleted_at', NULL)
            ->groupBy('ledgers.account');

        if ($request->start_date) {
            $query_income->where('ledgers.ledger_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query_income->where('ledgers.ledger_date', '<=', $request->end_date);
        }
        
        $params['income'] = $query_income->select(DB::raw("SUM(ledgers.credit) As amount, accounts.account as account_name"))->get();

        $get_total_income = array();

        foreach ($params['income'] as $account) {
            array_push($get_total_income, $account->amount);
        }

        $params['total_income'] = array_sum($get_total_income);


        //expense
        $query_expense =  Ledger::where('ledgers.deleted_at', NULL)
            ->where('ledgers.credit', 0)
            ->join('accounts', 'ledgers.account', '=', 'accounts.id')
            ->where('accounts.deleted_at', NULL)
            ->groupBy('ledgers.account');

        if ($request->start_date) {
            $query_expense->where('ledgers.ledger_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query_expense->where('ledgers.ledger_date', '<=', $request->end_date);
        }

        $params['expense'] = $query_expense->select(DB::raw("SUM(ledgers.debit) As amount, accounts.account as account_name"))->get();

        $get_total_expense = array();

        foreach ($params['expense'] as $account) {
            array_push($get_total_expense, $account->amount);
        }

        $params['total_expense'] = array_sum($get_total_expense);
        
        $params['profit'] = $params['total_income'] - $params['total_expense'];
        return $params;    }

    public function profitloss(Request $request)
    {
        $params = [];

        //income
        $query_income =  Ledger::where('ledgers.deleted_at', NULL)
            ->where('ledgers.account', '!=', 4)
            ->where('ledgers.account', '!=', 5)
            ->where('ledgers.account', '!=', 6)
            ->where('ledgers.account', '!=', 7)
            ->where('ledgers.debit', 0)
            ->join('accounts', 'ledgers.account', '=', 'accounts.id')
            ->where('accounts.deleted_at', NULL)
            ->groupBy('ledgers.account');

        if ($request->start_date) {
            $query_income->where('ledgers.ledger_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query_income->where('ledgers.ledger_date', '<=', $request->end_date);
        }
        
        $params['income'] = $query_income->select(DB::raw("SUM(ledgers.credit) As amount, accounts.account as account_name"))->get();

        $get_total_income = array();

        foreach ($params['income'] as $account) {
            array_push($get_total_income, $account->amount);
        }

        $params['total_income'] = array_sum($get_total_income);


        //expense
        $query_expense =  Ledger::where('ledgers.deleted_at', NULL)
            ->where('ledgers.account', '!=', 4)
            ->where('ledgers.account', '!=', 5)
            ->where('ledgers.account', '!=', 6)
            ->where('ledgers.account', '!=', 7)
            ->where('ledgers.credit', 0)
            ->join('accounts', 'ledgers.account', '=', 'accounts.id')
            ->where('accounts.deleted_at', NULL)
            ->groupBy('ledgers.account');

        if ($request->start_date) {
            $query_expense->where('ledgers.ledger_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query_expense->where('ledgers.ledger_date', '<=', $request->end_date);
        }

        $params['expense'] = $query_expense->select(DB::raw("SUM(ledgers.debit) As amount, accounts.account as account_name"))->get();

        $get_total_expense = array();

        foreach ($params['expense'] as $account) {
            array_push($get_total_expense, $account->amount);
        }

        $params['total_expense'] = array_sum($get_total_expense);
        
        $params['profit'] = $params['total_income'] - $params['total_expense'];
        return $params;
    }

    public function search(Request $request)
    {
        $search_term = $request[0];
        $account = Account::where('deleted_at', NULL)->where('account', 'like', '%' . $search_term . '%')->get();
        return $account;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'account' => 'required',
        ]);

        $last_account = Account::where('deleted_at', null)->latest()->first();
        if (!$last_account) {
            $ref_id = 100000;
        }
        else{
           $ref_id = $last_account->ref_id + 1000; 
        }

        if ($request->tax_line) {
            $tax= $request->tax_line;
        }
        else{
            $tax = 0;
        }
        return Account::create([
            'ref_id' => $ref_id,
            'account' => $request->account,
            'currency' => $request->currency,
            'type' => $request->type,
            'description' => $request->description,
            'tax_line' => $tax,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'account' => 'required',
            'type' => 'required',
        ]);

        $account = Account::where('deleted_at', NULL)->find($id);

        if ($request->tax_line) {
            $tax= $request->tax_line;
        }
        else{
            $tax = 0;
        }

        $account->update([
            'account' => $request->account,
            'currency' => $request->currency,
            'type' => $request->type,
            'description' => $request->description,
            'tax_line' => $tax,
        ]);

        return 'ok';
    }

    public function destroy($id)
    {
        $account = Account::where('deleted_at', NULL)->find($id);
        $account->delete();
    }
}
