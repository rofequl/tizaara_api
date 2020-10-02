<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name', 'division_id'
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function area()
    {
        return $this->hasMany(Area::class);
    }
}
