<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name', 'banner', 'icon', 'meta_title', 'slug','meta_description'
    ];

    public function subcategories(){
        return $this->hasMany(SubCategory::class);
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }

}
