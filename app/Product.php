<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function product_stock()
    {
        return $this->hasMany(Product_stock::class);
    }

    public function discount_variation_data()
    {
        return $this->hasMany(discount_variation::class);
    }

    public function price_variation()
    {
        return $this->hasMany(price_variation::class);
    }

    public function subsubcategories()
    {
        return $this->belongsTo(SubSubCategory::class, 'subsubcategory_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
