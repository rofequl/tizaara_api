<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name', 'code', 'code_a3', 'code_n3', 'lat', 'long'
    ];

    public function division()
    {
        return $this->hasMany(Division::class);
    }
}
