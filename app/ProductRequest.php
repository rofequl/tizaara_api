<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductRequest extends Model
{
    protected $fillable = [
        'request_type', 'product_id', 'discount', 'discount_type', 'user_id',
    ];
}
