<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class price_variation extends Model
{
    public function Product()
    {
        return $this->belongsTo(Product::class);
    }
}
