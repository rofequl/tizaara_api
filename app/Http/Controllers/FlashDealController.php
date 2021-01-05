<?php

namespace App\Http\Controllers;

use App\FlashDeal;
use App\FlashDealProduct;
use App\Product;
use App\ProductRequest;
use App\Traits\FileUpload;
use App\Traits\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlashDealController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['flashDealList']]);
    }

    use FileUpload;
    use Slug;

    public function index(Request $request)
    {
        return DB::table('flash_deals')->select('title', 'id', 'banner', 'start_date', 'end_date', 'slug')->get();
    }

    public function flashDealList()
    {
        $collection = collect();
        $flash = DB::table('flash_deals')->where('status', 1)->where('end_date', '>=', date('Y-m-d'))->get();
        foreach ($flash as $flashs) {

            $product = collect();
            $flash_product = DB::table('flash_deal_products')->where('flash_deal_id', $flashs->id)->get();
            foreach ($flash_product as $flash_products) {
                $product_details = Product::with('product_stock')->select('id', 'name', 'thumbnail_img', 'slug', 'stockManagement', 'priceType', 'unit_price')->find($flash_products->product_id);
                $price = '';
                $discount_price = '';
                if ($product_details->priceType == 0) {
                    $discount_price = $product_details->unit_price;
                    if ($flash_products->discount_type == 'flat') {
                        $price = (int)$discount_price - (int)$flash_products->discount;
                    } else {
                        $price = ((int)$discount_price - ((int)$discount_price * (int)$flash_products->discount) / 100);
                    }
                } elseif ($product_details->priceType == 1) {
                    $discount_price = $product_details->product_stock->min('price') . '-' . $product_details->product_stock->max('price');
                    if ($flash_products->discount_type == 'flat') {
                        $price = ((int)$product_details->product_stock->min('price') - (int)$flash_products->discount) . '-' .
                            ((int)$product_details->product_stock->max('price') - (int)$flash_products->discount);
                    } else {
                        $price = ((int)$product_details->product_stock->min('price') - (((int)$product_details->product_stock->min('price') *
                                        (int)$flash_products->discount) / 100)) . '-' .
                            ((int)$product_details->product_stock->max('price') - (((int)$product_details->product_stock->max('price') *
                                        (int)$flash_products->discount) / 100));
                    }
                }
                $product->push([
                    'name' => $product_details->name, 'discount' => $flash_products->discount, 'discount_type' => $flash_products->discount_type,
                    'thumbnail_img' => $product_details->thumbnail_img, 'slug' => $product_details->slug, 'price' => $price, 'discount_price' => $discount_price,
                ]);
            }
            $collection->push([
                'name' => $flashs->title, 'bg_color' => DB::table('colors')->where('id', $flashs->bg_color)->pluck('name')->first(),
                'text_color' => DB::table('colors')->where('id', $flashs->text_color)->pluck('name')->first(),
                'banner' => $flashs->banner, 'slug' => $flashs->slug, 'product' => $product, 'end_date' => $flashs->end_date,
            ]);
        }
        return $collection;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'dateRange' => 'required',
        ]);
        $insert = new FlashDeal();
        $insert->title = $request->title;
        $insert->bg_color = $request->bg_color;
        $insert->text_color = $request->text_color;
        if ($request->banner != '') {
            $insert->banner = $this->saveImages($request, 'banner', 'upload/flashdeals/', 1920, 500);
        }
        $insert->slug = $this->slugText($request, 'title');
        $insert->start_date = date('Y-m-d h:i:s', strtotime($request->dateRange['startDate']));
        $insert->end_date = date('Y-m-d h:i:s', strtotime($request->dateRange['endDate']));
        $insert->save();


        foreach ($request->product_list as $dis) {
            $product = new FlashDealProduct();
            $product->flash_deal_id = $insert->id;
            $product->product_id = $dis['id'];
            $product->discount = $dis['discount'];
            $product->discount_type = $dis['discount_type'];
            $product->save();
        }

        return $insert;

    }

    public function requestFlashDealList(Request $request)
    {
        $product = collect();
        $request = DB::table('product_requests')->where('request_type', 2)->orderBy('id', 'DESC')->get();
        foreach ($request as $item) {
            $data = DB::table('products')->where('id', $item->product_id)->first();
            $user = DB::table('users')->where('id', $item->user_id)->first();
            $status = $item->status == 0 ? 'Request' : 'Approve';
            $product->push([
                'id' => $item->id, 'image' => $data->thumbnail_img, 'product_name' => $data->name, 'user_name' => $user->first_name . ' ' . $user->last_name, 'status' => $status,
                'discount' => $item->discount, 'discount_type' => $item->discount_type,

            ]);
        }
        return $product;
    }

    public function requestFlashDealStore(Request $request)
    {
        $this->validate($request, [
            'request_id' => 'required',
            'flash_id' => 'required',
        ]);

        $product = ProductRequest::findOrFail($request->request_id);
        $insert = new FlashDealProduct();
        $insert->flash_deal_id = $request->flash_id;
        $insert->product_id = $product->product_id;
        $insert->discount = $product->discount;
        $insert->discount_type = $product->discount_type;
        $insert->save();
        $product->status = 1;
        $product->save();
        return $request->request_id;
    }

    public function statusUpdate(Request $request)
    {
        $product = FlashDeal::findOrFail($request->id);
        $product->status = (int)$request->status;
        $product->save();
    }

    public function destroy($id)
    {
        FlashDeal::findOrFail($id)->delete();
        FlashDealProduct::where('flash_deal_id', $id)->delete();
        return response()->json(['result' => 'Success', 'message' => 'Flash deals has been deleted'], 200);
    }
}
