<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Purchase;
use App\Inventory;
use App\Item;
use App\Category;
use DB;
use App\Sale;
use App\Account;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('api');
    }


    private function numberTowords($num){
        $ones = array(
            0 =>"Zero",
            1 => "One",
            2 => "Two",
            3 => "Three",
            4 => "Four",
            5 => "Five",
            6 => "Six",
            7 => "Seven",
            8 => "Eight",
            9 => "Nine",
            10 => "Ten",
            11 => "Eleven",
            12 => "Twelve",
            13 => "Thirteen",
            14 => "Fourteen",
            15 => "Fifteen",
            16 => "Sixteen",
            17 => "Seventeen",
            18 => "Eighteen",
            19 => "Nineteen",
            "014" => "Fourteen"
        );

        $tens = array( 
            0 => "Zero",
            1 => "Ten",
            2 => "Twenty",
            3 => "Thirty", 
            4 => "Forty", 
            5 => "Fifty", 
            6 => "Sixty", 
            7 => "Seventy", 
            8 => "Eighty", 
            9 => "Ninety" 
        ); 

        $hundreds = array( 
            "hundred", 
            "Thousand", 
            "Million", 
            "Billion", 
            "Trillion", 
            "Quardrillion" 
        ); 

        /*limit t quadrillion */
        $num = number_format($num,2,".",","); 
        $num_arr = explode(".",$num); 
        $wholenum = $num_arr[0]; 
        $decnum = $num_arr[1]; 
        $whole_arr = array_reverse(explode(",",$wholenum)); 
        krsort($whole_arr,1); 
        $rettxt = ""; 

        foreach($whole_arr as $key => $i){
            
            while(substr($i,0,1)=="0")
            $i=substr($i,1,5);

            if($i < 20){ 
                /* echo "getting:".$i; */
                $rettxt .= $ones[$i]; 
            }
            elseif($i < 100){ 
                if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)]; 
                if(substr($i,1,1)!="0") $rettxt .= " ".$ones[substr($i,1,1)]; 
            }
            else{ 
                if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 
                if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)]; 
                if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)]; 
            } 

            if($key > 0){ 
             $rettxt .= " ".$hundreds[$key]." "; 
            }
        } 

        if($decnum > 0){
            $rettxt .= " and ";
            if($decnum < 20){
                $rettxt .= $ones[$decnum];
            }
            elseif($decnum < 100){
                $rettxt .= $tens[substr($decnum,0,1)];
                $rettxt .= " ".$ones[substr($decnum,1,1)];
            }
        }

        return $rettxt;
    }

    public function getorder($id)
    {
        $params = [];
        $params['sale'] = Sale::where('sales.deleted_at', NULL)
            ->where('sales.sale_id', $id)
            ->join('users', 'sales.market_id', '=', 'users.id')
            ->select(
                'sales.id as id',
                'sales.approved as approved',
                'sales.status as status',
                'sales.sale_id as sale_id',
                'users.name as marketer',
                'sales.user_id as user_id',
                'sales.account as account',
                'sales.initialPrice as initialPrice',
                'sales.totalPrice as totalPrice',
                'sales.discount as discount',
                'sales.amount_paid as amount_paid',
                'sales.main_date as created_at'  
            )
            ->first();

        if ($params['sale'] ) {
            $mainprice = ((7.5/100) * $params['sale']->totalPrice) + $params['sale']->totalPrice;
            $params['word_price'] = $this->numberTowords($mainprice);

            $get_sale = Sale::where('deleted_at', NULL)->where('sale_id', $id)->first();
            $params['account'] = Account::where('deleted_at', NULL)->find($get_sale->account);
            $params['customer'] = User::where('deleted_at', NULL)->find($get_sale->buyer);
            $params['items'] = Item::where('items.code', $id)
                
                ->join('inventories', 'items.product_id', '=', 'inventories.id')
                ->where('inventories.deleted_at', NULL)
                ->select(
                    'items.id as id',
                    'items.product_id as product_id',
                    'items.qty as qty',
                    'items.discount as discount',
                    'inventories.quantity as quantity',
                    'inventories.product_name as product_name',
                    'inventories.price as price' 
                )
                ->get();
            return $params;
        }
        else{
            return ['error' => 'Opps something went wrong'];
        }
        
    }

    public function stat(Request $request)
    {
        $params = [];

        $params['users'] = User::where('deleted_at', NULL)->where('role', '!=', 0)->count();
        $params['customers'] = User::where('deleted_at', NULL)->where('role', 0)->count();
        $params['inventories'] = Inventory::where('deleted_at', NULL)->count();
        $params['categories'] = Category::where('deleted_at', NULL)->count();
        $params['transactions'] = Sale::where('deleted_at', NULL)->where('status', 'concluded')->count();

        $totalPrice = Sale::where('deleted_at', NULL)->where('status', 'concluded')->sum('totalPrice');
        $vat = (7.5/100) * $totalPrice;
        $params['mainPrice'] = $vat + $totalPrice;
        return $params;
    }
}
