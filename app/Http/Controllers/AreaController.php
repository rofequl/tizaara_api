<?php

namespace App\Http\Controllers;

use App\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['index']]);
    }

    public function index(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        if ($length == null && $column == null && $dir == null && $searchValue == null) {
            return DB::table('areas')->get();
        }
        $query = Area::with('city.division.country')->orderBy($columns[$column], $dir);

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

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:100|unique:areas',
            'zip_code' => 'required|max:100',
            'city_id' => 'required',
        ]);

        return Area::create($request->all());
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
        $this->validate($request, [
            'name' => 'required|max:100|unique:areas,name,' . $id,
            'zip_code' => 'required|max:100',
            'city_id' => 'required',
        ]);

        return Area::findOrFail($id)->update($request->all());
    }

    public function destroy($id)
    {
        //
    }
}
