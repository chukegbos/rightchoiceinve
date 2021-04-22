<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Ledger;
use App\Account;
use App\AccountType;
use App\Purchase;
use App\Inventory;
use App\InventoryStore;
use App\Setting;
use App\RoomMovement;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;
use App\Bank;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('api');
    }

    public function index(Request $request)
    {
        $params = [];

        $query = Purchase::where('purchases.deleted_at', NULL)
            ->join('inventories', 'purchases.product', '=', 'inventories.id')
            ->groupBy('purchases.purchase_id')
            ->orderBy('purchases.created_at', 'desc')
            ->where('inventories.deleted_at', NULL);

        if ($request->start_date) {
                $query->where('purchases.purchase_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('purchases.purchase_date', '<=', $request->end_date . ' 23:59');
        }

        if ($request->name) {
             $query->where('purchases.purchase_id', 'like', '%' . $request->name . '%');
        }

        if ($request->selected==0) {
            $params['purchases'] =  $query->get();
        }
        else{
            $params['purchases'] =  $query->paginate($request->selected);
        }


        $query1 = Purchase::where('purchases.deleted_at', NULL)
            ->join('inventories', 'purchases.product', '=', 'inventories.id')
            ->groupBy('purchases.purchase_id')
            ->where('inventories.deleted_at', NULL);

        if ($request->start_date) {
                $query1->where('purchases.purchase_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query1->where('purchases.purchase_date', '<=', $request->end_date . ' 23:59');
        }

        if ($request->name) {
             $query1->where('purchases.purchase_id', 'like', '%' . $request->name . '%');
        }

        $params['all'] = $query1->count();
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
        $purchaseId = rand(9,999999);
        $get_price = array();

        foreach ($request->productItems as $item) {
            $purchases = new Purchase ();
            $purchases->purchase_id = $purchaseId;
            $purchases->product = $item['id'];
            $purchases->quantity = $item['quantity'];
            $purchases->store_id = $item['store'];
            $purchases->total_price = $item['amount'];
            $purchases->purchase_date = $item['purchase_date'];
            $purchases->supplier = $item['supplier'];
            $saved = $purchases->save();

            if ($saved) {
                //$inventory = InventoryStore::where('deleted_at', NULL)->where('store_id', $item['store'])->where('inventory_id', $item['id'])->first();
                //$inventory->number = $inventory->number + $item['quantity'];
                //$inventory->update();

                $movement = new RoomMovement ();
                $movement->ref_id = rand(1, 888888);
                $movement->moved = 0;
                $movement->store_id = $item['store'];
                $movement->user_id = auth('api')->user()->id;
                $movement->product_id = $item['id'];
                $movement->status = 'pending';
                $movement->product_id = $item['id'];
                $movement->qty = $item['quantity'];
                $movement->move_type = 1;
                $movement->save();
            }

            array_push($get_price, $item['amount']);
        }

        $totalPrice = array_sum($get_price);
        $ledger_id = $this->ledgerID();

        $account_one = Account::where('deleted_at', NULL)->find($request->account_one);
        $account_type_one = AccountType::where('deleted_at', NULL)->find($account_one->type);

        $account_two = Account::where('deleted_at', NULL)->find($request->account_two);
        $account_type_two = AccountType::where('deleted_at', NULL)->find($account_two->type);

       
            Ledger::create([
                'ledger_id' => $ledger_id,
                'ledger_date' => Carbon::today(),
                'account' => $request->account_one,
                'amount' => $totalPrice,
                'debit' => $totalPrice,
                'credit' => 0,
                'description' => 'Purhcase of Items with ID '. $purchaseId,
                'position' => 1,
            ]);
 
            Ledger::create([
                'ledger_id' => $ledger_id,
                'ledger_date' => Carbon::today(),
                'account' => $request->account_two,
                'amount' => $totalPrice,
                'debit' => 0,
                'credit' => $totalPrice,
                'description' => 'Purhcase of Items with ID '. $purchaseId,
                'position' => 1,
            ]);

        return $purchaseId;
    }

    public function show($purchaseId){
        $purchases =  Purchase::where('purchases.deleted_at', NULL)
            ->where('purchases.purchase_id', $purchaseId)
            ->where('inventories.deleted_at', NULL)
            ->join('inventories', 'purchases.product', '=', 'inventories.id')
            ->orderBy('purchases.created_at', 'Desc')
            ->select(
                'purchases.id as id',
                'purchases.purchase_id as purchase_id',
                'purchases.store_id as store_id',
                'purchases.quantity as quantity',
                'inventories.product_name as product_name',
                'purchases.total_price as total_price',
                'purchases.supplier as supplier',
                'purchases.purchase_date as purchase_date'
            )
            ->get();

        return  response()->json($purchases);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'quantity' => 'required',
            'total_amount' => 'required',
            'total_pay' => 'required',
            'supplier' => 'required',
            'product' => 'required',
        ]);
        $purchase = Purchase::where('deleted_at')->find($id);
        $purchase_quantity = $purchase->quantity;
        $product = Product::findOrFail($request->product);
        $product->quantity = $product->quantity - $purchase_quantity;
        $product->quantity = $product->quantity + $request->quantity;
        $product->update();

        return $purchase->update($request->all());
        //return ['message' => "Success"];
    }

    public function destroy($id)
    {
        $purchase = Purchase::where('deleted_at', NULL)->find($id);
        $product = Inventory::where('deleted_at', NULL)->find($purchase->product);

        if ($product->quantity > $purchase->quantity) {
            $product->quantity = $product->quantity - $purchase->quantity;
            $product->update();
            Purchase::Destroy($id);
            return 'ok';
        }
        else
        {
            return 'You cannot delete this purchase detail because the product has decreased in the Inventory';
        }
        
    }
}
