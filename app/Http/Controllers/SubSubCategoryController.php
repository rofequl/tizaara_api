<?php

namespace App\Http\Controllers;

use App\SubCategory;
use App\SubSubCategory;
use Illuminate\Http\Request;
use App\Traits\Slug;

class SubSubCategoryController extends Controller
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
            return SubSubCategory::select('id', 'name', 'sub_category_id')->orderBy('id', 'DESc')->get();
        }
        $query = SubSubCategory::with('subcategory', 'subcategory.category')->orderBy($columns[$column], $dir);

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
            'name' => 'required|max:50|unique:sub_sub_categories',
            'sub_category_id' => 'required|max:50',
        ]);

        $data = $request->all();
        $data['slug'] = $this->slugText($request, 'name');
        return SubSubCategory::create($data);
    }

    public function show($id)
    {
        return SubSubCategory::select('id', 'name','slug')->where('sub_category_id', $id)->orderBy('id', 'DESc')->get();
    }

    public function slug($id)
    {
        return SubSubCategory::where('slug', $id)->first();
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50|unique:sub_sub_categories,name,' . $id,
        ]);
        $data = $request->all();
        $data['slug'] = $this->slugText($request, 'name');
        return SubSubCategory::findOrFail($id)->update($data);
    }

    public function destroy($id)
    {
        //
    }
}
