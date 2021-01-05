<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HelpCategory extends Model
{
    protected $fillable = [
        'name', 'icon', 'slug'
    ];
}
