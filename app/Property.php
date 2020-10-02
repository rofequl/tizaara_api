<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['name'];

    public function subsubcategory()
    {
        return $this->belongsToMany(SubSubCategory::class, 'property_categories',
            'property_id', 'subsubcategory_id');
    }
}
