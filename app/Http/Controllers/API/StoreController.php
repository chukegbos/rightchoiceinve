<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Store;
use App\StoreRequest;
use App\InventoryStore;
use App\Sale;
use App\Debtor;
use App\User;
use App\Room;
use App\Ledger;
use App\Inventory;
use App\RoomMovement;
use App\StoreUser;
use App\Account;
use App\AccountType;
use Illuminate\Support\Facades\Hash;
use DB;

class StoreController extends Controller
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
        $query = Store::where('deleted_at', NULL);

        if ($request->name) {
            $query->where('stores.name', 'like', '%' . $request->name . '%')->orWhere('stores.code', 'like', '%' . $request->name . '%');
        }

        if ($request->selected==0) {
            $params['stores'] =  $query->get();
        }
        else{
            $params['stores'] =  $query->paginate($request->selected);
        }
        
        $query1 = Store::where('deleted_at', NULL);

        if ($request->name) {
            $query1->where('stores.name', 'like', '%' . $request->name . '%')->orWhere('stores.code', 'like', '%' . $request->name . '%');
        }

        $params['all'] = $query1->count();

        return $params;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        $code = rand(3,99888888);

        Store::create([
            'name' => ucwords($request->name),
            'address' => $request->address,
            'code' => $code,
            'stock_limit' => ($request->stock_limit) ? $request->stock_limit :'Unlimited',
            'target' => ($request->target) ? $request->target :'None',
        ]);

        $store = Store::where('deleted_at', NULL)->where('code', $code)->first();
        $inventories = Inventory::where('deleted_at', NULL)->get();

        foreach ($inventories as $inventory) {
            $search_term = InventoryStore::where('deleted_at', NULL)->where('inventory_id', $inventory->id)->where('store_id', $store->id)->first();
            if (!$search_term) {
                InventoryStore::create([
                    'inventory_id' => $inventory->id,
                    'store_id' => $store->id,
                    'number' => 0,
                ]);
            }

            $search_term1 = Room::where('deleted_at', NULL)->where('inventory_id', $inventory->id)->where('store_id', $store->id)->first();
            if (!$search_term1) {
                Room::create([
                    'inventory_id' => $inventory->id,
                    'store_id' => $store->id,
                    'number' => 0,
                ]);
            }
        }

        return ['message' => "Success"];
    }

    public function getStore($code)
    {
        $store = Store::where('deleted_at', NULL)->where('code', $code)->first();
        if ($store) {
            return $store;
        }
    }

    public function show(Request $request, $code, $id)
    {
        
        $params = [];
        $query = InventoryStore::where('inventory_store.deleted_at', NULL)->where('inventory_store.store_id', $id)
            ->join('inventories', 'inventory_store.inventory_id', '=', 'inventories.id')
            ->join('categories', 'inventories.category', '=', 'categories.id')
            ->where('categories.deleted_at', NULL)
            ->where('inventories.deleted_at', NULL)
            ->orderBy('inventories.created_at', 'desc');
            if ($request->name) {
                $query->where('inventories.product_name', 'like', '%' . $request->name . '%')->orWhere('inventories.product_id', 'like', '%' . $request->name . '%')->orWhere('categories.name', 'like', '%' . $request->name . '%');
            }

            //return $request;
            $query->select(
                'inventories.id as id',
                'inventories.category as category',
                'categories.name as name',
                'inventories.product_id as product_id',
                'inventories.product_name as product_name',
                'inventory_store.number as quantity',
                'inventory_store.updated_at as updated_at',
                'inventories.cost_price as cost_price',
                'inventories.price as price',
                'inventories.unit as unit',
                'inventories.threshold as threshold'
            );

            if ($request->selected==0) {
                $params['inventories'] =  $query->get();
            }
            else{
                $params['inventories']  =  $query->paginate($request->selected);
            }
        
        $query1 = InventoryStore::where('inventory_store.deleted_at', NULL)->where('inventory_store.store_id', $id)
            ->join('inventories', 'inventory_store.inventory_id', '=', 'inventories.id')
            ->join('categories', 'inventories.category', '=', 'categories.id')
            ->where('inventories.deleted_at', NULL)
            ->where('categories.deleted_at', NULL);
            if ($request->name) {
                $query1->where('inventories.product_name', 'like', '%' . $request->name . '%')->orWhere('inventories.product_id', 'like', '%' . $request->name . '%')->orWhere('categories.name', 'like', '%' . $request->name . '%');
            }

        $params['all'] = $query1->count();

        return $params;
    }

    public function showroom(Request $request, $code, $id)
    {
        
        $params = [];
        $query = Room::where('rooms.deleted_at', NULL)->where('rooms.store_id', $id)
            ->join('inventories', 'rooms.inventory_id', '=', 'inventories.id')
            ->join('categories', 'inventories.category', '=', 'categories.id')
            ->where('categories.deleted_at', NULL)
            ->where('inventories.deleted_at', NULL)
            ->orderBy('inventories.created_at', 'desc');
            if ($request->name) {
                $query->where('inventories.product_name', 'like', '%' . $request->name . '%')->orWhere('inventories.product_id', 'like', '%' . $request->name . '%')->orWhere('categories.name', 'like', '%' . $request->name . '%');
            }

            //return $request;
            $query->select(
                'inventories.id as id',
                'inventories.category as category',
                'categories.name as name',
                'inventories.product_id as product_id',
                'inventories.product_name as product_name',
                'rooms.number as quantity',
                'rooms.updated_at as updated_at',
                'inventories.cost_price as cost_price',
                'inventories.price as price',
                'inventories.unit as unit',
                'inventories.threshold as threshold'
            );

            if ($request->selected==0) {
                $params['inventories'] =  $query->get();
            }
            else{
                $params['inventories']  =  $query->paginate($request->selected);
            }
        
        $query1 = Room::where('rooms.deleted_at', NULL)->where('rooms.store_id', $id)
            ->join('inventories', 'rooms.inventory_id', '=', 'inventories.id')
            ->join('categories', 'inventories.category', '=', 'categories.id')
            ->where('inventories.deleted_at', NULL)
            ->where('categories.deleted_at', NULL);
            if ($request->name) {
                $query1->where('inventories.product_name', 'like', '%' . $request->name . '%')->orWhere('inventories.product_id', 'like', '%' . $request->name . '%')->orWhere('categories.name', 'like', '%' . $request->name . '%');
            }

        $params['all'] = $query1->count();

        return $params;
    }
    public function storemovement(Request $request)
    {
        $user = auth('api')->user();
        $params = [];

        $query = RoomMovement::where('rooom_movement.deleted_at', NULL)->where('rooom_movement.move_type', 1)
            ->join('inventories', 'rooom_movement.product_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL)
            ->join('stores', 'rooom_movement.store_id', '=', 'stores.id')
            ->where('stores.deleted_at', NULL)
            ->orderBy('rooom_movement.created_at', 'desc')
            ->select(
                'rooom_movement.id as id',
                'inventories.product_name as product_name',
                'rooom_movement.qty as quantity',
                'rooom_movement.status as status',
                'rooom_movement.moved as moved',
                'rooom_movement.user_id as user_id',
                'rooom_movement.manager_id as manager_id',
                'stores.name as store_name'
            );

            if ($request->store_id) {
                $query->where('rooom_movement.store_id', $request->store_id);
            }
        
            if ($request->status) {
                $query->where('rooom_movement.status', $request->status);
            }
            
            if ($request->selected==0) {
                $params['inventories'] =  $query->get();
            }
            else{
                $params['inventories']  =  $query->paginate($request->selected);
            }
            
        $query1 = RoomMovement::where('rooom_movement.deleted_at', NULL)->where('rooom_movement.move_type', 1)
            ->join('inventories', 'rooom_movement.product_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL)
            ->join('stores', 'rooom_movement.store_id', '=', 'stores.id')
            ->where('stores.deleted_at', NULL);
            if ($request->store_id) {
                $query1->where('rooom_movement.store_id', $request->store_id);
            }
        
            if ($request->status) {
                $query1->where('rooom_movement.status', $request->status);
            }

        $params['all'] = $query1->count();

        return $params;
    }

    public function getInventory(Request $request)
    {
        //return $request->name;
        $query = DB::table('inventory_store')->where('inventory_store.deleted_at', NULL);

            if ($request->store_code) {
                $get_store = Store::where('deleted_at', NULL)->where('code', $request->store_code)->first();
                $myaccount = User::find(auth('api')->user()->id);
                $myaccount->store = $get_store->id;
                $myaccount->update();
                
                $query->where('inventory_store.store_id', $get_store->id);
            }
            else
            {
                $query->where('inventory_store.store_id', auth('api')->user()->store);
            }

            if ($request->name) {
                $query->where('inventories.product_name', 'like', '%' . $request->name . '%');
            }

            $query->join('inventories', 'inventory_store.inventory_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL);
            
        $inventory = $query->paginate(10);
        return response()->json($inventory);
    }

    public function gettotal(Request $request)
    {
        $get_price = array();
        foreach ($request->productItems as $product) {
            //$getProduct= json_decode(json_encode($product), true);
            //$getProduct = json_decode($product);
            //$each_price = ($product['qty'] * $product['price']) - ($product['qty'] * $product['discount']);

            $each_price = ($product['qty'] * $product['price']) - (($product['discount']/100) * ($product['qty'] * $product['price']));
            array_push($get_price, $each_price);
        }
        return array_sum($get_price);
    }

    public function allInventory(Request $request)
    {
        //return $request->name;
        $query = DB::table('inventory_store')->where('inventory_store.deleted_at', NULL);

            if ($request->store_code) {
                $get_store = Store::where('deleted_at', NULL)->where('code', $request->store_code)->first();
                $myaccount = User::find(auth('api')->user()->id);
                $myaccount->store = $get_store->id;
                $myaccount->update();
                
                $query->where('inventory_store.store_id', $get_store->id);
            }
            else
            {
                $query->where('inventory_store.store_id', auth('api')->user()->store);
            }

            $query->join('inventories', 'inventory_store.inventory_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL);
            
        $inventory = $query->get();
        return response()->json($inventory);
    }

    public function tradeinventory(Request $request)
    {
        $query = DB::table('inventory_store')->where('inventory_store.deleted_at', NULL);

            if ($request->store_code) {
                $get_store = Store::where('deleted_at', NULL)->where('code', $request->store_code)->first();
                $myaccount = User::find(auth('api')->user()->id);
                $myaccount->store = $get_store->id;
                $myaccount->update();
                
                $query->where('inventory_store.store_id', $get_store->id);
            }
            else
            {
                $query->where('inventory_store.store_id', auth('api')->user()->store);
            }

            $query->join('inventories', 'inventory_store.inventory_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL);
            
        $inventory = $query->get();
        return response()->json($inventory);
    }

    public function loadinventory(Request $request)
    {
        $search_term = $request[0];
        $inventory = InventoryStore::where('inventory_store.deleted_at', NULL)
            ->where('inventory_store.store_id', auth()->user()->store)
            ->join('inventories', 'inventory_store.inventory_id', '=', 'inventories.id')
            ->where('inventories.product_name', 'like', '%' . $search_term . '%')
            ->where('inventories.deleted_at', NULL)
            ->get();
        return $inventory;
    }


    public function discharge()
    {
        $inventory = StoreRequest::where('store_request.deleted_at', NULL)
            ->join('inventories', 'store_request.product_id', '=', 'inventories.id')
            ->where('inventories.deleted_at', NULL)
            ->join('stores', 'store_request.store_id', '=', 'stores.id')
            ->where('stores.deleted_at', NULL)
            ->orderBy('store_request.created_at', 'desc')
            ->select(
                'store_request.req_id as req_id',
                'stores.name as store_name',
                'store_request.id as id',
                'inventories.product_name as product_name',
                'inventories.quantity as quantity',
                'store_request.qty as qty',
                'store_request.sender_id as sender_id',
                'store_request.created_at as created_at',
                'store_request.approved_by as approved_by',
                'store_request.reciever_id as reciever_id',
                'store_request.status as status'
            )
            ->groupBy('store_request.req_id')
        ->paginate(10);
        return response()->json($inventory);
    }  

    public function approve(Request $request, $id)
    {
        $store_request = StoreRequest::where('deleted_at')->find($id);
        $product = Inventory::where('deleted_at')->find($store_request->product_id);

        $this->validate($request, [
            //'qty' => 'required',
        ]);

        if ($product->quantity < $store_request->qty) {
            return ['error' => "You cannot disburse more than you dont have in your warehouse"];
        }
        $store_request->update([
            'qty' => $request->qty,
            'approved_by' => auth('api')->user()->id,
            'status' => 'approved',
        ]);
        return ['message' => "Success"];
    }

    public function decline($id)
    {
        $store_request = StoreRequest::where('deleted_at')->find($id);
    
        $store_request->update([
            'status' => 'declined',
        ]);
        return ['message' => "Success"];
    }

    public function accept($id)
    {
        $req = StoreRequest::where('deleted_at')->find($id);

        $inventory = Inventory::where('deleted_at', NULL)->find($req->product_id);
        if ($inventory) {
            $inventory->quantity = $inventory->quantity - $req->qty;
            $inventory->update();

            $store_inventory = DB::table('inventory_store')
                ->where('deleted_at', NULL)
                ->where('store_id', $req->store_id)
                ->where('inventory_id', $req->product_id)
                ->first();

            $store_inventory_update = DB::table('inventory_store')
                ->where('deleted_at', NULL)
                ->where('store_id', $req->store_id)
                ->where('inventory_id', $req->product_id)
                ->update(['number' => $store_inventory->number + $req->qty, 'threshold' => $inventory->threshold]);

            //Updating Status
            $req->reciever_id = auth('api')->user()->id;
            $req->status = 'concluded';
            $req->update();
            return $req;
        }
        else
        {
            return ['error' => "Product does not exist!!!"];
        }
        
    }

    public function acceptall(Request $request)
    {
        foreach ($request->productItems as $item) {
            $req = StoreRequest::where('deleted_at')->find($item['id']);

            $inventory = Inventory::where('deleted_at', NULL)->find($req->product_id);
            if ($inventory) {
                $inventory->quantity = $inventory->quantity - $req->qty;
                $inventory->update();

                $store_inventory = DB::table('inventory_store')
                    ->where('deleted_at', NULL)
                    ->where('store_id', $req->store_id)
                    ->where('inventory_id', $req->product_id)
                    ->first();

                $store_inventory_update = DB::table('inventory_store')
                    ->where('deleted_at', NULL)
                    ->where('store_id', $req->store_id)
                    ->where('inventory_id', $req->product_id)
                    ->update(['number' => $store_inventory->number + $req->qty, 'threshold' => $inventory->threshold]);

                //Updating Status
                $req->reciever_id = auth('api')->user()->id;
                $req->status = 'concluded';
                $req->update();
                return $req;
            }
        }
    }
    
    public function storereq(Request $request){
        $random_number = rand(2,988888888);

        foreach ($request->productItems as $item) {
            $StoreRequest = StoreRequest::create([
                'req_id' => $random_number,
                'product_id' => $item['id'],
                'store_id' => auth('api')->user()->store,
                'qty' => $item['quantity'],
                'status' => 'pending',
                'sender_id' => auth('api')->user()->id,
            ]);
        }
        return StoreRequest::where('req_id', $random_number)->first();
    }

    public function updatereq(Request $request){
        foreach ($request->productItems as $item) {
            $store_request = StoreRequest::where('deleted_at')->find($item['id']);

            $store_request->update([
                'qty' => $item['qty'],
                'approved_by' => auth('api')->user()->id,
                'status' => 'approved',
            ]);
            
        }
        return ['message' => "Success"];
    }


    public function update(Request $request, $id)
    {
        $bar = Store::where('deleted_at')->find($id);
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        $bar->update([
            'address' => $request->address,
            'target' => $request->target,
            'stock_limit' => $request->stock_limit,
            'name' => ucwords($request->name),
        ]);
        return ['message' => "Success"];
    }

    public function destroy(Request $request)
    {
       foreach ($request->selected as $id) {
            if ($id!=1) {
                Store::Destroy($id);
            }
        }
        return 'ok';
    }

    public function sacceptall(Request $request)
    {
        foreach ($request->selected as $id) {
            $room = RoomMovement::find($id);
            $room->status = 'accepted';
            $room->manager_id = auth('api')->user()->id;
            $room->update();

            $inventory = InventoryStore::where('deleted_at', NULL)->where('store_id', $room->store_id)->where('inventory_id', $room->product_id)->first();
                $inventory->number = $inventory->number + $room->qty;
                $inventory->update();
        }
        return 'ok';
    }

    public function srejectall(Request $request)
    {
       foreach ($request->selected as $id) {
            $room = RoomMovement::find($id);
            $room->status = 'rejected';
            $room->update();

            $inventory = InventoryStore::where('deleted_at', NULL)->where('store_id', 1)->where('inventory_id', $room->product_id)->first();
                $inventory->number = $inventory->number + $room->qty;
                $inventory->update();
        }
        return 'ok';
    }
    public function reports(Request $request)
    {
        $params = [];

        $query = Sale::where('sales.deleted_at', NULL);

        //Optional where
        if ($request->start_date) {
            $query->where('sales.main_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('sales.main_date', '<=', $request->end_date . ' 23:59');
        }

        if ($request->staff) {
            $query->where('sales.user_id', $request->staff);
        }

        $report_data = $query->where('sales.status', 'pending')->orderBy('sales.main_date', 'Desc')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->select(
                'sales.id as id',
                'sales.sale_id as sale_id',
                'users.name as user_name',
                'sales.initialPrice as initialPrice',
                'sales.totalPrice as totalPrice',
                'sales.discount as discount',
                'sales.main_date as created_at'  
            )->paginate(10);

        $params['report_data'] = $report_data;
        $params['users'] = User::where('deleted_at', NULL)->where('role', '!=', 0)->get();

        return $params;
    }

    public function orders(Request $request)
    {
        $params = [];

        $query = Sale::where('sales.deleted_at', NULL);

        //Optional where
        if ($request->start_date) {
            $query->where('sales.main_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('sales.main_date', '<=', $request->end_date . ' 23:59');
        }

        if ($request->staff) {
            $query->where('sales.user_id', $request->staff);
        }

        if ($request->buyer) {
            $query->where('sales.buyer', $request->buyer);
        }

        if ($request->store) {
            $query->where('sales.store_id', $request->store);
        }

        if ($request->customer) {
            $query->where('users.name', 'like', '%' . $request->customer . '%');
        }

        $query->where('sales.status', '!=', 'pending')->orderBy('sales.main_date', 'Desc')
            ->join('users', 'sales.buyer', '=', 'users.id')
            ->select(
                'sales.id as id',
                'sales.sale_id as sale_id',
                'users.name as user_name',
                'sales.initialPrice as initialPrice',
                'sales.totalPrice as totalPrice',
                'sales.discount as discount',
                'sales.mop as mop',
                'sales.store_id as store_id',
                'sales.status as status',
                'sales.main_date as created_at'  
            );

            if ($request->selected==0) {
                $params['report_data'] =  $query->get();
            }
            else{
                $params['report_data']  =  $query->paginate($request->selected);
            }
         
        $query1 = Sale::where('sales.deleted_at', NULL);

        //Optional where
        if ($request->start_date) {
            $query1->where('sales.main_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query1->where('sales.main_date', '<=', $request->end_date . ' 23:59');
        }

        if ($request->staff) {
            $query1->where('sales.user_id', $request->staff);
        }

        if ($request->buyer) {
            $query1->where('sales.buyer', $request->buyer);
        }

        if ($request->customer) {
            $query1->where('users.name', 'like', '%' . $request->customer . '%');
        }

        $params['all'] = $query1->where('sales.status', '!=', 'pending')->orderBy('sales.main_date', 'Desc')
            ->join('users', 'sales.buyer', '=', 'users.id')->count();

        $params['users'] = User::where('deleted_at', NULL)->where('role', '!=', 0)->get();

        return $params;
    }

    public function quotes(Request $request)
    {
        $params = [];

        $query = Sale::where('sales.deleted_at', NULL);

        //Optional where
        if ($request->start_date) {
            $query->where('sales.main_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('sales.main_date', '<=', $request->end_date . ' 23:59');
        }

        if ($request->staff) {
            $query->where('sales.user_id', $request->staff);
        }

        if ($request->buyer) {
            $query->where('sales.buyer', $request->buyer);
        }

        if ($request->customer) {
            $query->where('users.name', 'like', '%' . $request->customer . '%');
        }

        $query->where('sales.status', '=', 'pending')->where('sales.approved', '=', 0)->orderBy('sales.main_date', 'Desc')
            ->join('users', 'sales.buyer', '=', 'users.id')
            ->select(
                'sales.id as id',
                'sales.sale_id as sale_id',
                'users.name as user_name',
                'sales.initialPrice as initialPrice',
                'sales.totalPrice as totalPrice',
                'sales.discount as discount',
                'sales.mop as mop',
                'sales.status as status',
                'sales.main_date as created_at'  
            );

            if ($request->selected==0) {
                $params['report_data'] =  $query->get();
            }
            else{
                $params['report_data']  =  $query->paginate($request->selected);
            }
         
        $query1 = Sale::where('sales.deleted_at', NULL);

        //Optional where
        if ($request->start_date) {
            $query1->where('sales.main_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query1->where('sales.main_date', '<=', $request->end_date . ' 23:59');
        }

        if ($request->staff) {
            $query1->where('sales.user_id', $request->staff);
        }

        if ($request->buyer) {
            $query1->where('sales.buyer', $request->buyer);
        }

        if ($request->customer) {
            $query1->where('users.name', 'like', '%' . $request->customer . '%');
        }

        $params['all'] = $query1->where('sales.status', '=', 'pending')
            ->where('sales.approved', '=', 0)
            ->orderBy('sales.main_date', 'Desc')
            ->join('users', 'sales.buyer', '=', 'users.id')
            ->count();

        $params['users'] = User::where('deleted_at', NULL)->where('role', '!=', 0)->get();

        return $params;
    }

    public function invoice(Request $request)
    {
        $params = [];

        $query = Sale::where('sales.deleted_at', NULL);

        //Optional where
        if ($request->start_date) {
            $query->where('sales.main_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('sales.main_date', '<=', $request->end_date . ' 23:59');
        }

        if ($request->staff) {
            $query->where('sales.user_id', $request->staff);
        }

        if ($request->buyer) {
            $query->where('sales.buyer', $request->buyer);
        }

        if ($request->customer) {
            $query->where('users.name', 'like', '%' . $request->customer . '%');
        }

        $query->where('sales.status', '=', 'pending')->where('sales.approved', '=', 1)->orderBy('sales.main_date', 'Desc')
            ->join('users', 'sales.buyer', '=', 'users.id')
            ->select(
                'sales.id as id',
                'sales.sale_id as sale_id',
                'users.name as user_name',
                'sales.initialPrice as initialPrice',
                'sales.totalPrice as totalPrice',
                'sales.discount as discount',
                'sales.mop as mop',
                'sales.status as status',
                'sales.main_date as created_at'  
            );

            if ($request->selected==0) {
                $params['report_data'] =  $query->get();
            }
            else{
                $params['report_data']  =  $query->paginate($request->selected);
            }
         
        $query1 = Sale::where('sales.deleted_at', NULL);

        //Optional where
        if ($request->start_date) {
            $query1->where('sales.main_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query1->where('sales.main_date', '<=', $request->end_date . ' 23:59');
        }

        if ($request->staff) {
            $query1->where('sales.user_id', $request->staff);
        }

        if ($request->buyer) {
            $query1->where('sales.buyer', $request->buyer);
        }

        if ($request->customer) {
            $query1->where('users.name', 'like', '%' . $request->customer . '%');
        }

        $params['all'] = $query1->where('sales.status', '=', 'pending')
            ->where('sales.approved', '=', 1)
            ->orderBy('sales.main_date', 'Desc')
            ->join('users', 'sales.buyer', '=', 'users.id')
            ->count();

        $params['users'] = User::where('deleted_at', NULL)->where('role', '!=', 0)->get();

        return $params;
    }
    public function debtors(Request $request)
    {
        $params = [];
        
        $query = Debtor::where('debtors.deleted_at', NULL)->where('debtors.status', 0);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->trans_id) {
            $query->where('trans_id', $request->trans_id);
        }

        if ($request->selected==0) {
            $params['report_data'] =  $query->get();
        }
        else{
            $params['report_data'] =  $query->paginate($request->selected);
        }
        

        $query1 = Debtor::where('debtors.deleted_at', NULL)->where('debtors.status', 0);

        if ($request->user_id) {
            $query1->where('user_id', $request->user_id);
        }

        if ($request->trans_id) {
            $query1->where('trans_id', $request->trans_id);
        }
        $params['all'] = $query1->count();

        return $params;
    }

    public function debtorview($sale_id)
    {
        $params = [];
        $query = Debtor::where('debtors.deleted_at', NULL)->where('debtors.trans_id', $sale_id);

        $report_data = $query->orderBy('debtors.created_at', 'Desc')
            ->join('sales', 'debtors.trans_id', '=', 'sales.sale_id')
            ->join('users', 'debtors.user_id', '=', 'users.id')
            ->select(
                'sales.sale_id as sale_id',
                'users.name as customer',
                'sales.cart as cart',
                'sales.totalPrice as totalPrice',
                'sales.amount_paid as amount_paid',
                'debtors.amount as amount',
                'debtors.amount_paid as amount_paid',
                'debtors.status as status'
            )->paginate(10);

        $report_data->transform(function($order, $key){
            $order->cart = unserialize($order->cart);
            return $order;
        });

        $params['report_data'] = $report_data;
        return $params;
    }

    public function addDebt(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required',
        ]);

        $first_debt = Debtor::where('deleted_at', NULL)->where('trans_id', $request->sale_id)->where('status', 0)->first();
        $first_debt->amount_paid = $request->amount;
        $first_debt->update();


        $first_debt_id = $first_debt->id;
        $first_debt_amount = $first_debt->amount;

        $debts = Debtor::where('deleted_at', NULL)->where('trans_id', $request->sale_id)->get();
        foreach ($debts as $debt) {
            $debtor = Debtor::where('deleted_at', NULL)->find($debt->id);
            $debtor->status = 1;
            $debtor->update();
        }

        if ($first_debt_amount != $request->amount) {
            $set_debt = new Debtor();
            $set_debt->user_id = $debtor->getOriginal('user_id');
            $set_debt->trans_id = $request->sale_id;
            $set_debt->amount = $first_debt_amount - $request->amount;
            $set_debt->status = 0;
            $set_debt->save();
        }

        $ledger_id = $this->ledgerID();

        $account_one = Account::where('deleted_at', NULL)->find($request->account_one);
        $account_type_one = AccountType::where('deleted_at', NULL)->find($account_one->type);

        $account_two = Account::where('deleted_at', NULL)->find($request->account_two);
        $account_type_two = AccountType::where('deleted_at', NULL)->find($account_two->type);

        Ledger::create([
            'ledger_id' => $ledger_id,
            'ledger_date' => Carbon::today(),
            'account' => $request->account_one,
            'amount' => $request->amount,
            'debit' => $request->amount,
            'credit' => 0,
            'description' => 'Debt payment with sales ID'.$request->sale_id,
            'position' => 1,
        ]);

        Ledger::create([
            'ledger_id' => $ledger_id,
            'ledger_date' => Carbon::today(),
            'account' => $request->account_two,
            'amount' => $request->amount,
            'debit' => 0,
            'credit' => $request->amount,
            'description' => 'Debt payment with sales ID '.$request->sale_id,
            'position' => 2,
        ]);

        return 'ok';
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
}
