<?php

namespace App\Http\Controllers;

use App\Property;
use App\Property_category;
use App\SubSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['index', 'show']]);
    }

    public function index(Request $request)
    {
        return DB::table('properties')->get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'property_label' => 'required',
            'property_label*' => 'required',
        ]);

        $position = 1;
        $cat_id = '';

        if ($request->category_id != null) {
            if ($request->sub_category_id != null) {
                if ($request->sub_subcategory_id != null) {
                    $position = 3;
                    $cat_id = $request->sub_subcategory_id;
                } else {
                    $position = 2;
                    $cat_id = $request->sub_category_id;
                }
            } else {
                $position = 1;
                $cat_id = $request->category_id;
            }
        }

        $insert = new Property();
        $insert->name = json_encode($request->property_label);
        $insert->position = $position;
        $insert->cat_id = $cat_id;
        $insert->save();

        return response()->json(['result' => 'Success', 'message' => 'Property add successfully'], 200);

    }

    public function show($id)
    {
        return SubSubCategory::with('property')->findOrFail($id);

    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255|unique:properties,name,' . $id,
        ]);

        $property = Property::findOrFail($id)->update($request->all());

        Property_category::where('property_id', $id)->delete();

        foreach ($request->subcategory_id as $data) {
            $insert = new Property_category();
            $insert->property_id = $id;
            $insert->subsubcategory_id = $data;
            $insert->save();
        }
        return 'ok';
    }

    public function destroy($id)
    {
        $brand = Property::findOrFail($id);
        $brand->delete();
        return response()->json(['result' => 'Success', 'message' => 'Property has been deleted'], 200);
    }
}
