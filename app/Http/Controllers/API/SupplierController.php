<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Supplier;
use Illuminate\Support\Facades\Hash;
use DB;

class SupplierController extends Controller
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

        $query = Supplier::where('deleted_at', NULL);

        if($request->keyword)
        {
            $query->where('supplier_name', 'like', $request->keyword)->orWhere('email', 'like', '%' .$request->keyword. '%')->orWhere('phone', 'like', '%' .$request->keyword. '%')->orWhere('contact_person', 'like', '%' .$request->keyword. '%');
        }

        if ($request->selected==0) {
            $params['suppliers'] =  $query->get();
        }
        else{
            $params['suppliers'] =  $query->paginate($request->selected);
        }


        $query1 = Supplier::where('deleted_at', NULL);

        if($request->keyword)
        {
            $query1->where('supplier_name', 'like', $request->keyword)->orWhere('email', 'like', '%' .$request->keyword. '%')->orWhere('phone', 'like', '%' .$request->keyword. '%')->orWhere('contact_person', 'like', '%' .$request->keyword. '%');
        }

        $params['all'] = $query1->count();
        return $params;
    }

    public function allSuppliers(Request $request)
    {
        $search_term = $request[0];
        $query = Supplier::where('deleted_at', NULL);
        if($search_term){
          $query->where('supplier_name', 'like', '%' . $search_term . '%');  
        }
        return $query->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'supplier_name' => 'required|string|max:255',
            'email' => 'required|string|max:191',
            'phone' => 'required|string|max:18',
            'contact_person' => 'required|string|max:180',
            'address' => 'required|string|max:255',
        ]);

        return Supplier::create([
            'supplier_name' => $request['supplier_name'],
            'contact_person' => $request['contact_person'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'address' => $request['address'],
            'bank_name' => $request['bank_name'],
            'bank_account' => $request['bank_account'],
            'account_name' => $request['account_name'],
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $params = [];
        $params['bar'] = Bar::where('deleted_at', NULL)->where('code', $code)->first();

        if ($params['bar']) {
            $params['fridges'] = Fridge::where('deleted_at', NULL)->where('bar_code', $code)->paginate(10);
            return $params;
        }
        else{
            return response()->json(['error' => 'Such bar does not exist'], 500);
        }

    }

    public function singleSupplier($id){
        $supplier = Supplier::find($id);
        return response()->json($supplier);
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
        $supplier = Supplier::where('deleted_at')->find($id);

        $this->validate($request, [
            'supplier_name' => 'required|string|max:255',
            'email' => 'required|string|max:191',
            'phone' => 'required|string|max:18',
            'contact_person' => 'required|string|max:180',
            'address' => 'required|string|max:255',
        ]);

        $supplier->update([
            'supplier_name' => $request['supplier_name'],
            'contact_person' => $request['contact_person'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'address' => $request['address'],
            'bank_name' => $request['bank_name'],
            'bank_account' => $request['bank_account'],
            'account_name' => $request['account_name'],
        ]);
        return ['message' => "Success"];
    }

    public function destroy(Request $request)
    {
       foreach ($request->selected as $id) {
            Supplier::Destroy($id);
        }
        return 'ok';
    }
}
