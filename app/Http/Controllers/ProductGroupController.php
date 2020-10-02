<?php

namespace App\Http\Controllers;

use App\Product_group;
use Illuminate\Http\Request;

class ProductGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins');
    }

    public function index(Request $request)
    {
        $columns = ['id', 'name', 'group'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        if ($length == null && $column == null && $dir == null && $searchValue == null) {
            return Product_group::with('subcategories', 'subcategories.subsubcategories')
                ->select('id', 'name', 'group')->get();
        }
        $query = Product_group::orderBy($columns[$column], $dir);

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
            'name' => 'required|max:200',
            'linkProduct' => 'required',
        ]);

        $data = $request->all();
        $data['group'] = json_encode($request->linkProduct);
        return Product_group::create($data);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
