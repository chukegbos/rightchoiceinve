<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Setting;
use App\Sale;
use App\Fund;
use App\Item;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('api');
    }

    public function index(Request $request)
    {
        $params = [];
        

        $query = User::where('deleted_at', NULL)->where('users.role', 0)->latest();

        if ($request->name) {
            $query->where('users.name', 'like', '%' . $request->name . '%')->orWhere('users.phone', 'like', '%' . $request->name . '%')->orWhere('users.email', 'like', '%' . $request->name . '%')->orWhere('users.c_person', 'like', '%' . $request->name . '%');
        }

        if ($request->selected==0) {
            $params['customers'] =  $query->get();
        }
        else{
            $params['customers'] =  $query->paginate($request->selected);
        }
        
        $query1 = User::where('deleted_at', NULL)->where('users.role', 0);
        if ($request->name) {
            $query1->where('users.name', 'like', '%' . $request->name . '%')->orWhere('users.phone', 'like', '%' . $request->name . '%')->orWhere('users.email', 'like', '%' . $request->name . '%')->orWhere('users.c_person', 'like', '%' . $request->name . '%');
        }
        $params['all'] = $query1->count();

        return $params;
    }

    public function searchcustomer(Request $request)
    {
        $search_term = $request[0];
        $users = User::where('deleted_at', NULL)
            ->where('users.role', 0)
            ->where('users.name', 'like', '%' . $search_term . '%')
            ->orWhere('users.c_person', 'like', '%' . $search_term . '%')
            ->get();
        return $users;
    }

    public function view(Request $request, $unique_id)
    { 
        //return $request;
        $params = [];
        $params['user'] = User::where('deleted_at', NULL)->where('unique_id', $unique_id)->first();

        if($params['user']){
            $params['orders'] = Sale::where('sales.deleted_at', NULL)
                ->where('sales.status', 'concluded')
                ->where('sales.buyer', $params['user']->id)
                ->orderBy('sales.main_date', 'Desc')
                ->select(
                    'sales.id as id',
                    'sales.sale_id as sale_id',
                    'sales.initialPrice as initialPrice',
                    'sales.totalPrice as totalPrice',
                    'sales.discount as discount',
                    'sales.mop as mop',
                    'sales.status as status',
                    'sales.main_date as main_date'  
                )->take(5)->get();

            $query1 = Sale::where('sales.deleted_at', NULL)
                ->where('sales.status', 'concluded')
                ->where('sales.buyer', $params['user']->id)
                ->orderBy('sales.main_date', 'Desc')
                ->select(
                    'sales.id as id',
                    'sales.sale_id as sale_id',
                    'sales.initialPrice as initialPrice',
                    'sales.totalPrice as totalPrice',
                    'sales.discount as discount',
                    'sales.mop as mop',
                    'sales.status as status',
                    'sales.main_date as main_date'  
                );

                if ($request->start_date) {
                    $query1->where('sales.main_date', '>=', $request->start_date);
                }

                if ($request->end_date) {
                    $query1->where('sales.main_date', '<=', $request->end_date);
                }

                $params['all_orders'] = $query1->get();

            $query_invoice = Sale::where('sales.deleted_at', NULL)
                ->where('sales.status', 'pending')
                ->where('sales.approved', 1)
                ->where('sales.buyer', $params['user']->id)
                ->orderBy('sales.main_date', 'Desc')
                ->join('users', 'sales.market_id', '=', 'users.id')
                ->where('users.deleted_at', NULL)
                ->select(
                    'sales.id as id',
                    'sales.sale_id as sale_id',
                    'sales.initialPrice as initialPrice',
                    'sales.totalPrice as totalPrice',
                    'sales.discount as discount',
                    'sales.mop as mop',
                    'sales.approved as approved',
                    'users.name as market_id',
                    'sales.status as status',
                    'sales.main_date as main_date'  
                );

                if ($request->start_date) {
                    $query_invoice->where('sales.main_date', '>=', $request->start_date);
                }

                if ($request->end_date) {
                    $query_invoice->where('sales.main_date', '<=', $request->end_date);
                }

                $params['invoices'] = $query_invoice->get();

            $query_quote = Sale::where('sales.deleted_at', NULL)
                ->where('sales.status', 'pending')
                ->where('sales.approved', '!=', 1)
                ->where('sales.buyer', $params['user']->id)
                ->orderBy('sales.main_date', 'Desc')
                ->join('users', 'sales.market_id', '=', 'users.id')
                ->where('users.deleted_at', NULL)
                ->select(
                    'sales.id as id',
                    'sales.sale_id as sale_id',
                    'sales.initialPrice as initialPrice',
                    'sales.totalPrice as totalPrice',
                    'sales.discount as discount',
                    'sales.mop as mop',
                    'sales.approved as approved',
                    'users.name as market_id',
                    'sales.status as status',
                    'sales.main_date as main_date'  
                );

                if ($request->start_date) {
                    $query_invoice->where('sales.main_date', '>=', $request->start_date);
                }

                if ($request->end_date) {
                    $query_invoice->where('sales.main_date', '<=', $request->end_date);
                }

                $params['quotes'] = $query_quote->get();

            $params['order_count'] = Sale::where('deleted_at', NULL)->where('status', 'concluded')->where('buyer', $params['user']->id)->count();

            $query3 = Sale::where('deleted_at', NULL)
                ->where('status', 'concluded')
                ->where('buyer', $params['user']->id);
                if ($request->start_date) {
                    $query3->where('sales.main_date', '>=', $request->start_date);
                }

                if ($request->end_date) {
                    $query3->where('sales.main_date', '<=', $request->end_date);
                }
                $params['value_order_count'] = $query3->sum('totalPrice');

            $query2 = Fund::where('funds.deleted_at', NULL)
                ->where('funds.customer_id', $params['user']->id)
                ->join('users', 'funds.user_id', '=', 'users.id')
                ->where('users.deleted_at', NULL)
                ->select(
                    'users.name as name',
                    'funds.mop as mop',
                    'funds.ref_id as ref_id',
                    'funds.amount as amount',
                    'funds.created_at as created_at'  
                )
                ->orderBy('funds.created_at', 'desc');

                if ($request->start_date) {
                    $query2->where('funds.created_at', '>=', $request->start_date);
                }

                if ($request->end_date) {
                    $query2->where('funds.created_at', '<=', $request->end_date);
                }
                $params['funds'] = $query2->get();

            $query4 = Fund::where('funds.deleted_at', NULL)
                ->where('funds.customer_id', $params['user']->id)
                ->join('users', 'funds.user_id', '=', 'users.id')
                ->where('users.deleted_at', NULL);

                if ($request->start_date) {
                    $query4->where('funds.created_at', '>=', $request->start_date);
                }

                if ($request->end_date) {
                    $query4->where('funds.created_at', '<=', $request->end_date);
                }
                $params['sum_fund'] = $query4->sum('funds.amount');

            //$query5 = Item::where('sales.deleted_at', NULL)
                //->join('sales', 'items.code', '=', 'sales.sale_id')
            $query5 = Item::where('items.deleted_at', NULL)
                ->join('sales', 'items.code', '=', 'sales.sale_id')
                ->where('sales.deleted_at', NULL)
                ->where('sales.buyer', $params['user']->id)
                ->where('sales.status', 'concluded')
                ->orderBy('sales.main_date', 'desc')
                ->select(
                    'items.product_name as name',
                    'items.code as ref_id',
                    'items.qty as qty',
                    'items.totalPrice as amount',
                    'items.created_at as date'  
                );

                if ($request->start_date) {
                    $query5->where('items.created_at', '>=', $request->start_date);
                }

                if ($request->end_date) {
                    $query5->where('items.created_at', '<=', $request->end_date);
                }

                $params['items'] = $query5->orderBy('sales.main_date', 'desc')
                ->get();

            $query6 = Item::where('items.deleted_at', NULL)
                ->join('sales', 'items.code', '=', 'sales.sale_id')
                ->where('sales.deleted_at', NULL)
                ->where('sales.buyer', $params['user']->id)
                ->where('sales.status', 'concluded')
                ->orderBy('sales.main_date', 'desc');

                if ($request->start_date) {
                    $query6->where('items.created_at', '>=', $request->start_date);
                }

                if ($request->end_date) {
                    $query6->where('items.created_at', '<=', $request->end_date);
                }

                $params['item_count'] = $query6->count();

            return $params;
        }
        else{
            return ['error' => 'Customer not found'];
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'c_person' => 'required|string|max:191',
            'email' => 'required|string|max:191|email|unique:users',
            'phone' => 'required|string|max:19',
            'address' => 'required|string|max:191',
        ]);

        $setting = Setting::findOrFail(1);

        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'state' => $request['state'],
            'city' => $request['city'],
            'c_person' => $request['c_person'],
            'unique_id' => 'CS'.rand(1, 999999),
            'address' => $request['address'],
            'credit_unit' => $request['credit_unit'],
            'role' => 0,
            'password' => Hash::make('Father@1989'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'c_person' => 'required|string|max:191',
            'email' => 'required|string|max:191|unique:users,email,'.$user->id,
            'phone' => 'required|string|max:19',
            'address' => 'required|string|max:191',
        ]);

        $user->update([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'state' => $request['state'],
            'credit_unit' => $request['credit_unit'],
            'address' => $request['address'],
            'city' => $request['city'],
            'c_person' => $request['c_person'],
        ]);
        return ['Message' => 'Updated'];
    }

    public function edit($id)
    {
        $user = User::Find($id);
        if ($user) {
           return $user;
        }
        else{
            return ['error' => 'Customer not found'];
        }
    }

    public function delete(Request $request)
    {
        foreach ($request->selected as $id) {
            User::Destroy($id);
        }
        return 'ok';
        
    }

    public function destroy($id)
    {
        User::destroy($id);
        return ['Message' => 'Deleted'];
    }
}
