<?php

namespace App\Http\Controllers;

use App\SubCategory;
use Illuminate\Http\Request;
use App\Traits\Slug;

class SubCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['index', 'slug','show']]);
    }

    use Slug;

    public function index(Request $request)
    {
        $columns = ['id', 'name', 'category_id'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        if ($length == null && $column == null && $dir == null && $searchValue == null) {
            return SubCategory::select('id', 'name', 'category_id')->orderBy('id', 'DESc')->get();
        }
        $query = SubCategory::with('category')->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('meta_title', 'like', '%' . $searchValue . '%');
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
            'name' => 'required|max:50|unique:sub_categories',
        ]);

        $data = $request->all();
        $data['slug'] = $this->slugText($request, 'name');
        return SubCategory::create($data);
    }

    public function show($id)
    {
        return SubCategory::select('id', 'name','slug')->with('subsubcategories')->where('category_id', $id)->orderBy('id', 'DESc')->get();
    }

    public function slug($id)
    {
        return SubCategory::where('slug', $id)->first();
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50|unique:sub_categories,name,' . $id,
        ]);
        $data = $request->all();
        $data['slug'] = $this->slugText($request, 'name');
        return SubCategory::findOrFail($id)->update($data);
    }

    public function destroy($id)
    {
        //
    }
}
