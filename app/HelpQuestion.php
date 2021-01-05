<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HelpQuestion extends Model
{
    protected $fillable = [
        'category_id', 'subcategory_id', 'slug',  'question', 'answer',
    ];
}
