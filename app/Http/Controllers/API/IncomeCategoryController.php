<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\IncomeCategory;
use Illuminate\Support\Str;

class IncomeCategoryController extends Controller
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
        $query =  IncomeCategory::where('deleted_at', NULL);
        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        $incomeCategory = $query->latest()->paginate(10);
        return response()->json($incomeCategory);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        return IncomeCategory::create([
            'name' => ucwords($request->name),
        ]);


    }

    public function show($id)
    {
        $incomeCategory =  IncomeCategory::where('deleted_at', NULL)->find($id);
        if ($incomeCategory) {
            return response()->json($incomeCategory);
        }
        return ['error' => 'Not found'];
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        $incomeCategory = IncomeCategory::where('deleted_at', NULL)->find($id);
        $incomeCategory->name = $request->name;
       
        if($incomeCategory->save()){
            return ['success'=> 'Data updated successfully'];
        }else{
            return ['error' => 'Opps something went wrong'];
        }
    }

    public function destroy($id)
    {
        $incomeCategory = IncomeCategory::where('deleted_at', NULL)->find($id);
        $incomeCategory->delete();
    }
}
