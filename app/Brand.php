<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name', 'meta_title', 'meta_description', 'logo', 'slug'
    ];

    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
