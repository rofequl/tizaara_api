<?php

namespace App\Http\Controllers;

use App\Product;
use App\SubCategory;
use App\SubSubCategory;
use Illuminate\Http\Request;
use App\Traits\Slug;
use Illuminate\Support\Facades\DB;

class SubCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['index', 'slug', 'show']]);
    }

    use Slug;

    public function index(Request $request)
    {
        return DB::table('sub_categories')->join('categories', 'categories.id', '=', 'sub_categories.category_id')
            ->select('categories.name as categoryName', 'sub_categories.*')->get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
        ]);

        $check = SubCategory::where('name', $request->name)->where('category_id', $request->category_id)->first();
        if ($check) return response()->json(['message' => 'The given data was invalid.', 'errors' => ['name' => ['The name has already been taken.']]], 422);

        $data = $request->all();
        $data['slug'] = $this->slugText($request, 'name');
        $subcategory = SubCategory::create($data);
        return (array)DB::table('sub_categories')->join('categories', 'categories.id', '=', 'sub_categories.category_id')
            ->select('categories.name as categoryName', 'sub_categories.*')->where('sub_categories.id', $subcategory->id)->first();
    }

    public function show($id)
    {
        return SubCategory::select('id', 'name', 'slug')->with('subsubcategories')->where('category_id', $id)->orderBy('id', 'DESc')->get();
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

        $check = SubCategory::where('name', $request->name)->where('category_id', $request->category_id)->where('id', '!=', $id)->first();
        if ($check) return response()->json(['message' => 'The given data was invalid.', 'errors' => ['name' => ['The name has already been taken.']]], 422);

        $data = $request->all();
        $data['slug'] = $this->slugText($request, 'name');
        SubCategory::findOrFail($id)->update($data);
        return SubCategory::join('categories', 'categories.id', '=', 'sub_categories.category_id')
            ->select('categories.name as categoryName', 'sub_categories.*')->findOrFail($id);
    }

    public function destroy($id)
    {
        $product = Product::where('subcategory_id', $id)->first();
        $subsubcategory = SubSubCategory::where('sub_category_id', $id)->first();
        if ($product) return response()->json(['result' => 'Error', 'message' => 'Sub-Category already used create a product'], 200);
        if ($subsubcategory) return response()->json(['result' => 'Error', 'message' => 'Sub-Category already used create a Sub-Subcategory'], 200);
        $brand = SubCategory::findOrFail($id);
        $brand->delete();
        return response()->json(['result' => 'Success', 'message' => 'Sub-Category has been deleted'], 200);
    }
}
