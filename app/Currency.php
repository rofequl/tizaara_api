<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['name', 'symbol', 'exchange_rate', 'code'];

    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
