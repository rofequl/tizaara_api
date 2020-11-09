<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [
        'name', 'banner', 'category_id', 'meta_title', 'slug', 'meta_description'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subsubcategories()
    {
        return $this->hasMany(SubSubCategory::class);
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
