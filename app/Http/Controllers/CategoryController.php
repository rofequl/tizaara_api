<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\SubCategory;
use App\Traits\FileUpload;
use App\Traits\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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
        return DB::table('categories')->orderByRaw('ISNULL(serial), serial ASC')->get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50|unique:categories',
        ]);

        $data = $request->all();
        if ($request->banner != '') {
            $data['banner'] = $this->saveImages($request, 'banner', 'upload/category/banner/', 200, 300);
        }
        if ($request->icon != '') {
            $data['icon'] = $this->saveImages($request, 'icon', 'upload/category/icon/', 32, 32);
        }
        $data['slug'] = $this->slugText($request, 'name');
        return Category::create($data);
    }

    public function show($id)
    {
        return Category::where('slug', $id)->first();
    }

    public function categoryListing(Request $request)
    {
        Category::query()->update(['serial' => null]);
        for ($i = 1; $i <= count($request->category_id); $i++) {
            $insert = Category::where('id', $request->category_id[$i - 1])->first();
            $insert->serial = $i;
            $insert->save();
        }
        return response()->json(['result' => 'Success', 'message' => 'Category has been listing'], 200);
    }

    public function homeCategoryListing(Request $request)
    {
        Category::query()->update(['home_serial' => null]);
        for ($i = 1; $i <= count($request->category_id); $i++) {
            $insert = Category::where('id', $request->category_id[$i - 1])->first();
            $insert->home_serial = $i;
            $insert->save();
        }
        return response()->json(['result' => 'Success', 'message' => 'Category has been listing'], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50|unique:categories,name,' . $id,
        ]);
        $category = Category::findOrFail($id);
        $data = $request->all();
        if ($request->banner != '' && strlen($request->banner) > 200) {
            File::delete(public_path($category->banner));
            $data['banner'] = $this->saveImages($request, 'banner', 'upload/category/banner/', 200, 300);
        }
        if ($request->icon != '' && strlen($request->icon) > 200) {
            File::delete(public_path($category->icon));
            $data['icon'] = $this->saveImages($request, 'icon', 'upload/category/icon/', 32, 32);
        }
        $data['slug'] = $this->slugText($request, 'name');
        $category->update($data);
        return Category::findOrFail($id);
    }

    public function destroy($id)
    {
        $product = Product::where('category_id', $id)->first();
        $subcategory = SubCategory::where('category_id', $id)->first();
        if ($product) return response()->json(['result' => 'Error', 'message' => 'Category already used create a product'], 200);
        if ($subcategory) return response()->json(['result' => 'Error', 'message' => 'Category already used create a subcategory'], 200);
        $brand = Category::findOrFail($id);
        File::delete(public_path($brand->icon));
        File::delete(public_path($brand->banner));
        $brand->delete();
        return response()->json(['result' => 'Success', 'message' => 'Category has been deleted'], 200);
    }
}
