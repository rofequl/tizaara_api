<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class discount_variation extends Model
{
    public function Product()
    {
        return $this->belongsTo(Product::class);
    }
}
