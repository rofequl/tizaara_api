<?php

namespace App\Http\Controllers;

use App\Product;
use App\SubCategory;
use App\SubSubCategory;
use Illuminate\Http\Request;
use App\Traits\Slug;
use Illuminate\Support\Facades\DB;

class SubSubCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['index', 'slug', 'show']]);
    }

    use Slug;

    public function index(Request $request)
    {
        return DB::table('sub_sub_categories')->join('categories', 'categories.id', '=', 'sub_sub_categories.category_id')
            ->join('sub_categories', 'sub_categories.id', '=', 'sub_sub_categories.sub_category_id')
            ->select('categories.name as categoryName', 'sub_categories.name as subcategoryName', 'sub_sub_categories.*')
            ->orderByRaw('ISNULL(sub_sub_categories.serial), sub_sub_categories.serial ASC')->get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'category_id' => 'required|max:50',
            'sub_category_id' => 'required|max:50',
        ]);

        $check = SubSubCategory::where('name', $request->name)->where('category_id', $request->category_id)
            ->where('sub_category_id', $request->sub_category_id)->first();
        if ($check) return response()->json(['message' => 'The given data was invalid.', 'errors' => ['name' => ['The name has already been taken.']]], 422);

        $data = $request->all();
        $data['slug'] = $this->slugText($request, 'name');
        $subcategory = SubSubCategory::create($data);
        return (array)DB::table('sub_sub_categories')->join('categories', 'categories.id', '=', 'sub_sub_categories.category_id')
            ->join('sub_categories', 'sub_categories.id', '=', 'sub_sub_categories.sub_category_id')
            ->select('categories.name as categoryName', 'sub_categories.name as subcategoryName', 'sub_sub_categories.*')
            ->where('sub_sub_categories.id', $subcategory->id)->first();
    }

    public function show($id)
    {
        return SubSubCategory::select('id', 'name', 'slug')->where('sub_category_id', $id)->orderBy('id', 'DESc')->get();
    }

    public function slug($id)
    {
        return SubSubCategory::where('slug', $id)->first();
    }

    public function subsubcategoryListing(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'sub_category_id' => 'required',
        ]);
        SubSubCategory::where('category_id', '=', $request->category_id)->where('sub_category_id', '=', $request->subcategory_id)->update(['serial' => null]);
        for ($i = 1; $i <= count($request->subsubcategory_id); $i++) {
            $insert = SubSubCategory::where('id', $request->subsubcategory_id[$i - 1])->first();
            $insert->serial = $i;
            $insert->save();
        }
        return response()->json(['result' => 'Success', 'message' => 'Sub-Subcategory has been listing'], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50',
        ]);

        $check = SubSubCategory::where('name', $request->name)->where('category_id', $request->category_id)
            ->where('sub_category_id', $request->sub_category_id)->where('id', '!=', $id)->first();
        if ($check) return response()->json(['message' => 'The given data was invalid.', 'errors' => ['name' => ['The name has already been taken.']]], 422);


        $data = $request->all();
        $data['slug'] = $this->slugText($request, 'name');
        SubSubCategory::findOrFail($id)->update($data);
        return (array)DB::table('sub_sub_categories')->join('categories', 'categories.id', '=', 'sub_sub_categories.category_id')
            ->join('sub_categories', 'sub_categories.id', '=', 'sub_sub_categories.sub_category_id')
            ->select('categories.name as categoryName', 'sub_categories.name as subcategoryName', 'sub_sub_categories.*')
            ->where('sub_sub_categories.id', $id)->first();
    }

    public function destroy($id)
    {
        $product = Product::where('subsubcategory_id', $id)->first();
        if ($product) return response()->json(['result' => 'Error', 'message' => 'Sub-Subcategory already used create a product'], 200);
        $brand = SubSubCategory::findOrFail($id);
        $brand->delete();
        return response()->json(['result' => 'Success', 'message' => 'Sub-Subcategory has been deleted'], 200);
    }
}
