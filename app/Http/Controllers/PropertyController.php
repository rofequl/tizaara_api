<?php

namespace App\Http\Controllers;

use App\Property;
use App\Property_category;
use App\SubSubCategory;
use Illuminate\Http\Request;

class PropertyController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['show']]);
    }

    public function index(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        if ($length == null && $column == null && $dir == null && $searchValue == null) {
            return Property::select('id', 'name')->orderBy('id', 'DESc')->get();
        }
        $query = Property::with('subsubcategory')->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->latest()->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
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

        if ($request->category_id != null){
            if ($request->category_id != null)
        }else{

        }

        return $request->all();
        $property = Property::create($request->all());

        foreach ($request->subcategory_id as $data) {
            $insert = new Property_category();
            $insert->property_id = $property->id;
            $insert->subsubcategory_id = $data;
            $insert->save();
        }
        return 'ok';
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
        //
    }
}
