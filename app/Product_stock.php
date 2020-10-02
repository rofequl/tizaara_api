<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_stock extends Model
{
    public function Product()
    {
        return $this->belongsTo(Product::class);
    }
}
