<?php

namespace App\Http\Controllers;

use App\Address;
use App\Category;
use App\discount_variation;
use App\GeneralSetting;
use App\price_variation;
use App\Product;
use App\Product_stock;
use App\SubCategory;
use App\SubSubCategory;
use App\Traits\FileUpload;
use App\Traits\Slug;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['productListing', 'search', 'searchName']]);
    }

    use FileUpload;
    use Slug;

    public function index(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        if ($length == null && $column == null && $dir == null && $searchValue == null) {
            return Product::select('id', 'name', 'subcategory_id')->orderBy('id', 'DESc')->get();
        }
        $query = Product::with('product_stock')
            ->select('id', 'name', 'num_of_sale', 'thumbnail_img', 'stockManagement', 'quantity', 'priceType', 'unit_price', 'slug')
            ->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('sku', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->latest()->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    public function search(Request $request)
    {
        $search = $request->input('searchProduct');
        if ($search != null) {
            return Product::select('id', 'name', 'thumbnail_img', 'sku')
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('sku', 'like', '%' . $search . '%')->get();
        } else {
            return [];
        }
    }

    public function getProductGroup(Request $request)
    {
        $search = $request->input('searchProduct');
        if ($search != null) {
            $search = json_decode($search);
            return Product::select('id', 'name', 'thumbnail_img', 'sku')
                ->whereIn('id', $search)->get();
        } else {
            return [];
        }
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
            'sku' => 'required|max:255|unique:products',
        ]);

        $colors = [];
        $photos = [];
        $feature = '';
        $flash = '';
        $thumbnail = '';
        $meta_image = '';
        if ($request->color_type == 1) {
            foreach ($request->color_image as $photo) {
                if (array_key_exists("image", $photo)) {
                    $image = $this->saveImagesVue($photo['image'][0], 'path', 'upload/product/color/', 370, 370);
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
                    $image = $this->saveImagesVue($photo, 'path', 'upload/product/product/', 370, 370);
                    array_push($photos, $image);
                }
            }
        }


        if ($request->featured_img != '' && count($request->featured_img) != 0) {
            $feature = $this->saveImagesVue($request->featured_img[0], 'path', 'upload/product/feature/', 290, 300);
        }

        if ($request->flash_deal_img != '' && count($request->flash_deal_img) != 0) {
            $flash = $this->saveImagesVue($request->flash_deal_img[0], 'path', 'upload/product/flash_deal/', 290, 300);
        }

        if ($request->thumbnail_img != '' && count($request->thumbnail_img) != 0) {
            $thumbnail = $this->saveImagesVue($request->thumbnail_img[0], 'path', 'upload/product/thumbnail/', 290, 300);
        }

        if ($request->meta_img != '' && count($request->meta_img) != 0) {
            $meta_image = $this->saveImagesVue($request->meta_img[0], 'path', 'upload/product/meta_image/', 290, 300);
        }

        $product = new Product();
        $product->name = $request->name;
        $product->sort_desc = $request->sort_desc;
        $product->slug = $this->slugText($request, 'name');
        $product->added_by = $request->added_by;
        $product->user_id = Auth::user()->id;
        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->sub_category_id;
        $product->subsubcategory_id = $request->sub_subcategory_id;
        $product->property_options = json_encode($request->properties);
        $product->brand_id = $request->brand_id;
        $product->unit = $request->unit;
        $product->weight = $request->weight;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->height = $request->height;
        $product->tags = json_encode($request->tags);
        $product->product_type = $request->product_type;
        $product->photos = json_encode($photos);
        $product->thumbnail_img = $thumbnail;
        $product->featured_img = $feature;
        $product->flash_deal_img = $flash;
        $product->video_link = $request->video_link;
        $product->colors = json_encode($request->color);
        $product->color_image = json_encode($colors);
        $product->color_type = $request->color_type;
        $product->attributes = json_encode($request->attribute);
        $product->attribute_options = json_encode($request->attribute_options);
        $product->tax = $request->tax;
        $product->tax_type = $request->tax_type;
        $product->discount = $request->discount;
        $product->discount_type = $request->discount_type;
        $product->discount_variation = $request->discountMethod;
        $product->orderQtyLimit = $request->orderQtyLimit;
        $product->orderQtyLimitMax = $request->orderQtyLimitMax;
        $product->orderQtyLimitMin = $request->orderQtyLimitMin;
        $product->priceType = $request->priceType;
        $product->stockManagement = $request->stockManagement;
        $product->unit_price = $request->unit_price;
        $product->currency_id = $request->currency_id;
        $product->quantity = $request->quantity;
        $product->sku = $request->sku;
        $product->description = $request->description;
        $product->shipping_type = $request->shipping_type;
        $product->shipping_cost = $request->shipping_cost;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->meta_img = $meta_image;
        $product->save();

        if ($request->discountMethod == 1) {
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
        $prduct = Product::with('unit', 'discount_variation_data', 'price_variation',
            'subsubcategories', 'subcategory', 'category', 'currency', 'brand', 'product_stock')
            ->where('slug', $id)->first();
        if ($prduct) {
            return $prduct;
        } else {
            return response()->json(['result' => 'Error', 'message' => 'Product not found'], 404);
        }
    }

    public function productListing(Request $request)
    {
        $conditions = [];
        $category_id = $request->category;
        $subcategory_id = $request->subcategory;
        $subsubcategory_id = $request->subsubcategory;

        $keyword = $request->keyword;
        $type = $request->type;

        if ($category_id) $conditions = array_merge($conditions, ['category_id' => $category_id]);
        if ($subcategory_id) $conditions = array_merge($conditions, ['subcategory_id' => $subcategory_id]);
        if ($subsubcategory_id) $conditions = array_merge($conditions, ['subsubcategory_id' => $subsubcategory_id]);

        $product = Product::where($conditions);

        if ($keyword) $product = $product->where('name', 'like', '%' . $keyword . '%');

        $product = $product->with('product_stock')->select('id', 'name', 'thumbnail_img', 'priceType', 'unit_price', 'slug', 'property_options', 'added_by', 'user_id', 'video_link')
            ->paginate(10);

        $collection = new Collection();
        foreach ($product as $products) {
            $supplier = [];
            if ($request->type == 'Suppliers') {
                if ($products->added_by == 'supplier') {
                    $company = DB::table('company_basic_infos')->where('user_id', $products->user_id)->select('name', 'phone', 'address_type',
                        'ope_address_id', 'reg_address_id')->first();
                    if ($company) {
                        $address = DB::table('addresses')->where('id', $company->address_type == 2 ? $company->ope_address_id : $company->reg_address_id)
                            ->pluck('address')->first();
                        $supplier['name'] = $company->name;
                        $supplier['phone'] = $company->phone;
                        $supplier['address'] = $address;
                    } else {
                        $user = User::findOrFail($products->user_id);
                        $supplier['name'] = $user->first_name . ' ' . $user->last_name;
                        $supplier['phone'] = $user->mobile;
                        $supplier['address'] = '';
                    }
                } else {
                    $general_setting = DB::table('general_settings')->get()->first();
                    $supplier['name'] = $general_setting->site_name;
                    $supplier['phone'] = $general_setting->phone;
                    $supplier['address'] = $general_setting->address;
                }
            }
            $collection->push([
                'product' => $products,
                'supplier' => $supplier,
            ]);
        }
        $final_data = [
            'product_details'=>$collection,
            'product_load' => [
                'to' => $product->lastPage(),
                'total' =>  $product->total(),
                'current_page' => $product->currentPage()
            ],
        ];
        return $final_data;
    }

    public function searchName(Request $request)
    {
        return DB::table('products')->where('name', 'like', '%' . $request->n . '%')->select('id', 'name')
            ->skip(0)->take(5)->get()->unique('name');
    }

    public function update(Request $request, $id)
    {
        //return $request->all();
        $this->validate($request, [
            'name' => 'required|max:200',
            'added_by' => 'required|max:10',
            'weight' => 'max:100',
            'length' => 'max:10',
            'width' => 'max:10',
            'height' => 'max:10',
            'video_link' => 'max:100',
            'sku' => 'required|max:255|unique:products,sku,' . $id,
        ]);

        $product = Product::findOrFail($id);
        $colors = [];
        $photos = [];
        if ($request->color_type == 1) {
            foreach ($request->color_image as $photo) {
                if (array_key_exists("image", $photo) && strlen($photo['image'][0]['path']) > 200) {
                    $image = $this->saveImagesVue($photo['image'][0], 'path', 'upload/product/color/', 370, 370);
                    $image = [
                        'name' => $photo['name'],
                        'image' => $image,
                    ];
                    array_push($colors, $image);
                } else {
                    foreach (json_decode($product->color_image) as $pho) {
                        if (strpos($photo['image'][0]['path'], $pho->image)) {
                            $image = [
                                'name' => $photo['name'],
                                'image' => $pho->image,
                            ];
                            array_push($colors, $image);
                        }
                    }
                }
            }
        }

        //return $colors;

        if ($request->photos != '' && is_array($request->photos)) {
            foreach ($request->photos as $photo) {
                if (array_key_exists("path", $photo) && strlen($photo['path']) > 200) {
                    $image = $this->saveImagesVue($photo, 'path', 'upload/product/product/', 370, 370);
                    array_push($photos, $image);
                } else {
                    foreach (json_decode($product->photos) as $pho) {
                        if (strpos($photo['path'], $pho)) {
                            array_push($photos, $pho);
                        }
                    }
                }
            }
        }

        if (count($request->featured_img) > 0 && strlen($request->featured_img[0]['path']) > 200) {
            $feature = $this->saveImagesVue($request->featured_img[0], 'path', 'upload/product/feature/', 290, 300);
            $product->featured_img = $feature;
        }

        if (count($request->flash_deal_img) > 0 && strlen($request->flash_deal_img[0]['path']) > 200) {
            $flash = $this->saveImagesVue($request->flash_deal_img[0], 'path', 'upload/product/flash_deal/', 290, 300);
            $product->flash_deal_img = $flash;
        }

        if (count($request->thumbnail_img) > 0 && strlen($request->thumbnail_img[0]['path']) > 200) {

            $thumbnail = $this->saveImagesVue($request->thumbnail_img[0], 'path', 'upload/product/thumbnail/', 290, 300);
            $product->thumbnail_img = $thumbnail;
        }
        //return $photos;

        if (count($request->thumbnail_img) > 0 && strlen($request->thumbnail_img[0]['path']) > 200) {
            $meta_image = $this->saveImagesVue($request->meta_img[0], 'path', 'upload/product/meta_image/', 290, 300);
            $product->meta_img = $meta_image;
        }


        $product->name = $request->name;
        $product->sort_desc = $request->sort_desc;
        $product->added_by = $request->added_by;
        $product->user_id = Auth::user()->id;
        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->sub_category_id;
        $product->subsubcategory_id = $request->sub_subcategory_id;
        $product->property_options = json_encode($request->properties);
        $product->brand_id = $request->brand_id;
        $product->unit = $request->unit;
        $product->weight = $request->weight;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->height = $request->height;
        $product->tags = json_encode($request->tags);
        $product->product_type = $request->product_type;
        if (count($photos) > 0) {
            $product->photos = json_encode($photos);
        }
        $product->video_link = $request->video_link;
        $product->colors = json_encode($request->color);
        $product->color_image = json_encode($colors);
        $product->color_type = $request->color_type;
        $product->attributes = json_encode($request->attribute);
        $product->attribute_options = json_encode($request->attribute_options);
        $product->tax = $request->tax;
        $product->tax_type = $request->tax_type;
        $product->discount = $request->discount;
        $product->discount_type = $request->discount_type;
        $product->discount_variation = $request->discountMethod;
        $product->orderQtyLimit = $request->orderQtyLimit;
        $product->orderQtyLimitMax = $request->orderQtyLimitMax;
        $product->orderQtyLimitMin = $request->orderQtyLimitMin;
        $product->priceType = $request->priceType;
        $product->stockManagement = $request->stockManagement;
        $product->unit_price = $request->unit_price;
        $product->currency_id = $request->currency_id;
        $product->quantity = $request->quantity;
        $product->description = $request->description;
        $product->shipping_type = $request->shipping_type;
        $product->shipping_cost = $request->shipping_cost;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->save();
        //return $request;

        if ($request->discount_variation == 1) {
            discount_variation::where('product_id', $product->id)->delete();
            foreach ($request->tierDiscount as $dis) {
                $discount = new discount_variation();
                $discount->product_id = $product->id;
                $discount->percent_off = $dis['value'];
                $discount->min_qty = $dis['unit'];
                $discount->save();
            }
        }

        if ($request->priceType == 2) {
            price_variation::where('product_id', $product->id)->delete();
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
            Product_stock::where('product_id', $product->id)->delete();
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

    public function destroy($id)
    {
        //
    }
}
