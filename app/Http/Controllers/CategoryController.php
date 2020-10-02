<?php

namespace App\Http\Controllers;

use App\Category;
use App\Traits\FileUpload;
use App\Traits\Slug;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['index', 'show']]);
    }

    use FileUpload;
    use Slug;

    public function index(Request $request)
    {
        $columns = ['id', 'name', 'banner', 'icon'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        if ($length == null && $column == null && $dir == null && $searchValue == null) {
            return Category::with('subcategories', 'subcategories.subsubcategories')
                ->select('id', 'name', 'icon', 'slug')->get();
        }
        $query = Category::orderBy($columns[$column], $dir);

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
            'name' => 'required|max:50|unique:brands',
        ]);

        $data = $request->all();
        if ($request->banner != '') {
            $data['banner'] = $this->saveImages($request, 'banner', 'upload/category/banner/');
        }
        if ($request->icon != '') {
            $data['icon'] = $this->saveImages($request, 'icon', 'upload/category/icon/');
        }
        $data['slug'] = $this->slugText($request, 'name');
        return Category::create($data);
    }

    public function show($id)
    {
        return Category::where('slug', $id)->first();
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50|unique:categories,name,' . $id,
        ]);

        $data = $request->all();
        if ($request->banner != '' && strlen($request->banner) > 200) {
            $data['banner'] = $this->saveImages($request, 'banner', 'upload/category/banner/');
        }
        if ($request->icon != '' && strlen($request->icon) > 200) {
            $data['icon'] = $this->saveImages($request, 'icon', 'upload/category/icon/');
        }
        $data['slug'] = $this->slugText($request, 'name');
        return Category::findOrFail($id)->update($data);
    }

    public function destroy($id)
    {
        //
    }
}
