<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Inventory;
use App\Notification;
use App\User;
use App\Sale;
use App\Store;
use App\Room;
use DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $inventories = Inventory::where('deleted_at', NULL)->get();
        $stores = Store::where('deleted_at', NULL)->get();

        foreach ($inventories as $inventory) {
            foreach ($stores as $store) {
                $product = DB::table('inventory_store')
                    ->where('deleted_at', NULL)
                    ->where('store_id', $store->id)
                    ->where('inventory_id', $inventory->id)
                    ->first();
                if (!$product) {
                    $store->inventory()->attach($inventory->id);
                }

                $room = Room::where('deleted_at', NULL)
                    ->where('store_id', $store->id)
                    ->where('inventory_id', $inventory->id)
                    ->first();
                if (!$room) {
                    $get_room = new Room();
                    $get_room->inventory_id = $inventory->id;
                    $get_room->store_id = $store->id;
                    $get_room->number = 0;
                    $get_room->save();
                }
            }
        }

        /*$threshold_inventories = Inventory::where('deleted_at', NULL)->whereColumn('threshold', '>', 'quantity')->get();

        if ($threshold_inventories) {
            foreach ($threshold_inventories as $inventory) {
                $add = new Notification();
                $add->store = 0;
                $add->purpose = $inventory->product_name. ' has gone beyond threshold';
                $add->save();
            }
        }

        $finished_inventories = Inventory::where('deleted_at', NULL)->whereColumn('threshold', '>', 'quantity')->get();

        if ($finished_inventories) {
            foreach ($finished_inventories as $inventory) {
                $add = new Notification();
                $add->store = 0;
                $add->purpose = $inventory->product_name. ' has gone beyond threshold';
                $add->save();
            }
        }*/
        return view('dashboard');
    }

    public function getUser()
    {
        return Auth::user();
    }
}
