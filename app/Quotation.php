<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'product', 'email', 'quantity', 'unit_id',
    ];
}
