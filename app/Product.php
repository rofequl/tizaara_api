<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function product_stock()
    {
        return $this->hasMany(Product_stock::class);
    }

    public function discount_variation()
    {
        return $this->hasMany(discount_variation::class);
    }

    public function price_variation()
    {
        return $this->hasMany(price_variation::class);
    }
}
