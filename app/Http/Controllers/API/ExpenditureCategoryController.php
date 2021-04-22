<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ExpenditureCategory;
use Illuminate\Support\Str;

class ExpenditureCategoryController extends Controller
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
        $query =  ExpenditureCategory::where('deleted_at', NULL);
        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        $expenditureCategory = $query->latest()->paginate(10);
        return response()->json($expenditureCategory);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        return ExpenditureCategory::create([
            'name' => ucwords($request->name),
        ]);


    }

    public function show($id)
    {
        $expenditureCategory =  ExpenditureCategory::where('deleted_at', NULL)->find($id);
        if ($expenditureCategory) {
            return response()->json($expenditureCategory);
        }
        return ['error' => 'Not found'];
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        $expenditureCategory = ExpenditureCategory::where('deleted_at', NULL)->find($id);
        $expenditureCategory->name = $request->name;
       
        if($expenditureCategory->save()){
            return ['success'=> 'Data updated successfully'];
        }else{
            return ['error' => 'Opps something went wrong'];
        }
    }

    public function destroy($id)
    {
        $expenditureCategory = ExpenditureCategory::where('deleted_at', NULL)->find($id);
        $expenditureCategory->delete();
    }
}
