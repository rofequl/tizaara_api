<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Traits\FileUpload;
use App\Traits\Slug;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['index']]);
    }

    use FileUpload;
    use Slug;

    public function index(Request $request)
    {
        $columns = ['id', 'name', 'logo', 'meta_title'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        if ($length == null && $column == null && $dir == null && $searchValue == null) {
            return Brand::select('id', 'name','logo')->orderBy('id', 'DESc')->get();
        }
        $query = Brand::orderBy($columns[$column], $dir);

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
        if ($request->logo != '') {
            $data['logo'] = $this->saveImages($request, 'logo', 'upload/brands/');
        }

        $data['slug'] = $this->slugText($request,'name');
        return Brand::create($data);
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
        $request->validate([
            'name' => 'required|max:50|unique:brands,name,' . $id,
        ]);

        $data = $request->all();
        if ($request->logo != '' && strlen($request->logo) > 200) {
            $data['logo'] = 'upload/brands/' . $this->saveImages($request, 'image', 'brands');
        }
        $data['slug'] = $this->slugText($request,'name');
        return Brand::findOrFail($id)->update($data);
    }

    public function destroy($id)
    {
        //
    }
}
