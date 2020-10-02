<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubSubCategory extends Model
{
    protected $fillable = [
        'name', 'sub_category_id', 'meta_title', 'slug', 'meta_description'
    ];

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function property()
    {
        return $this->belongsToMany(Property::class,'property_categories','subsubcategory_id');
    }
}