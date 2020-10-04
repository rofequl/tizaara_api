<?php

namespace App\Http\Controllers\User;

use App\discount_variation;
use App\Http\Controllers\Controller;
use App\price_variation;
use App\Product;
use App\Product_stock;
use App\Traits\FileUpload;
use App\Traits\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');
    }

    use FileUpload;
    use Slug;

    public function index()
    {
        return Product::where('user_id', Auth::user()->id)->orderBy('id', 'DESc')->get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:200',
            'added_by' => 'required|max:10',
            'weight' => 'max:100',
            'length' => 'max:10',
            'width' => 'max:10',
            'height' => 'max:10',
            'video_link' => 'max:100',
            'orderQtyLimitValue' => 'max:10',
            'sku' => 'required|max:255|unique:products',
        ]);

        $colors = [];
        $photos = [];
        $feature = '';
        $flash = '';
        $thumbnail = '';
        $meta_image = '';
        if ($request->color_image != '') {
            foreach ($request->color_image as $photo) {
                if (array_key_exists("image", $photo)) {
                    $image = $this->saveImagesVue($photo, 'image', 'upload/product/color/');
                    $image = [
                        'name' => $photo['name'],
                        'image' => $image,
                    ];
                    array_push($colors, $image);
                }
            }
        }

        if ($request->photos != '') {
            foreach ($request->photos as $photo) {
                if (array_key_exists("path", $photo)) {
                    $image = $this->saveImagesVue($photo, 'path', 'upload/product/product/');
                    array_push($photos, $image);
                }
            }
        }

        if ($request->featured_img != '') {
            $feature = $this->saveImagesVue2($request->featured_img, 'upload/product/feature/');
        }

        if ($request->flash_deal_img != '') {
            $flash = $this->saveImagesVue2($request->flash_deal_img, 'upload/product/flash_deal/');
        }

        if ($request->thumbnail_img != '') {
            $thumbnail = $this->saveImagesVue2($request->thumbnail_img, 'upload/product/thumbnail/');
        }

        if ($request->meta_img != '') {
            $meta_image = $this->saveImagesVue2($request->meta_img, 'upload/product/meta_image/');
        }

        $product = new Product();
        $product->name = $request->name;
        $product->sort_desc = $request->sort_desc;
        $product->slug = $this->slugText($request, 'name');
        $product->added_by = $request->added_by;
        $product->user_id = Auth::user()->id;
        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->subsubcategory_id = $request->subsubcategory_id;
        $product->property_options = json_encode($request->property_options);
        $product->brand_id = $request->brand_id;
        $product->unit = $request->unit;
        $product->weight = $request->weight;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->height = $request->height;
        $product->tags = json_encode($request->tags);
        $product->product_type = json_encode($request->product_type);
        $product->photos = json_encode($photos);
        $product->thumbnail_img = $thumbnail;
        $product->featured_img = $feature;
        $product->flash_deal_img = $flash;
        $product->video_link = $request->video_link;
        $product->colors = json_encode($request->color);
        $product->color_image = json_encode($colors);
        $product->attributes = json_encode($request->attribute);
        $product->attribute_options = json_encode($request->attribute_options);
        $product->tax = $request->tax;
        $product->tax_type = $request->tax_type;
        $product->discount = $request->discount;
        $product->discount_type = $request->discount_type;
        $product->discount_variation = $request->discount_variation;
        $product->orderQtyLimit = $request->orderQtyLimit;
        $product->orderQtyLimitValue = $request->orderQtyLimitValue;
        $product->priceType = $request->priceType;
        $product->stockManagement = $request->stockManagement;
        $product->unit_price = $request->unit_price;
        $product->currency_id = $request->currency_id;
        $product->quantity = $request->quantity;
        $product->sku = $request->sku;
        $product->description = $request->description;
        $product->linkProduct = json_encode($request->linkProduct);
        $product->shipping_type = $request->shipping_type;
        $product->shipping_cost = $request->shipping_cost;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->meta_img = $meta_image;
        $product->save();

        if ($request->discount_variation == 1) {
            foreach ($request->tierDiscount as $dis) {
                $discount = new discount_variation();
                $discount->product_id = $product->id;
                $discount->percent_off = $dis['value'];
                $discount->min_qty = $dis['unit'];
                $discount->save();
            }
        }

        if ($request->priceType == 2) {
            foreach ($request->tierPrice as $prices) {
                $price = new price_variation();
                $price->product_id = $product->id;
                $price->off_price = $prices['value'];
                $price->min_qty = $prices['min_unit'];
                $price->max_qty = $prices['max_unit'];
                $price->save();
            }
        }

        if ($request->priceType == 1) {
            foreach ($request->priceMenu as $prices) {
                $stock = new Product_stock();
                $stock->product_id = $product->id;
                $stock->variant = json_encode($prices['variant']);
                $stock->sku = $this->sku($prices, $product->id);
                $stock->price = $prices['variant_price'];
                $stock->qty = $prices['quantity'];
                $stock->save();
            }
        }
        return 'done';
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
        //
    }

    public function destroy($id)
    {
        //
    }
}
