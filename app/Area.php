<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['name', 'city_id', 'zip_code'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
