<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Product;
use App\Traits\FileUpload;
use App\Traits\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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
        return DB::table('brands')->orderByRaw('ISNULL(serial), serial ASC')->get();
    }

    public function brandListing(Request $request)
    {
        Brand::query()->update(['serial' => null]);
        for ($i = 1; $i <= count($request->brand_id); $i++) {
            $insert = Brand::where('id', $request->brand_id[$i - 1])->first();
            $insert->serial = $i;
            $insert->save();
        }
        return response()->json(['result' => 'Success', 'message' => 'Brand has been listing'], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50|unique:brands',
        ]);

        $data = $request->all();
        if ($request->logo != '') {
            $data['logo'] = $this->saveImages($request, 'logo', 'upload/brands/', 120, 80);
        }

        $data['slug'] = $this->slugText($request, 'name');
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
        $brand = Brand::findOrFail($id);
        $data = $request->all();
        if ($request->logo != '' && strlen($request->logo) > 200) {
            File::delete(public_path($brand->logo));
            $data['logo'] = $this->saveImages($request, 'logo', 'upload/brands/', 120, 80);
        }
        $data['slug'] = $this->slugText($request, 'name');
        $brand->update($data);
        return Brand::findOrFail($id);
    }

    public function destroy($id)
    {
        $product = Product::where('brand_id', $id)->first();
        if ($product) return response()->json(['result' => 'Error', 'message' => 'Brand already used create a product'], 200);
        $brand = brand::findOrFail($id);
        File::delete(public_path($brand->logo));
        $brand->delete();
        return response()->json(['result' => 'Success', 'message' => 'Brand has been deleted'], 200);
    }
}
