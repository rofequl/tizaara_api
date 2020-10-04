<?php

namespace App\Http\Controllers;

use App\Color;
use Illuminate\Http\Request;

class ColourController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['index']]);
    }

    public function index(Request $request)
    {
        $columns = ['id', 'name', 'code'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        if ($length == null && $column == null && $dir == null && $searchValue == null) {
            return Color::select('id', 'name', 'code')->orderBy('id', 'DESc')->get();
        }
        $query = Color::orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('code', 'like', '%' . $searchValue . '%');
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
            'name' => 'required|max:30|unique:colors',
            'code' => 'required|max:10|unique:colors',
        ]);

        return Color::create($request->all());
    }


    public function show($id)
    {

    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:30|unique:colors,name,' . $id,
            'code' => 'required|max:10|unique:colors,code,' . $id,
        ]);

        return Color::findOrFail($id)->update($request->all());
    }

    public function destroy($id)
    {
        //
    }
}
