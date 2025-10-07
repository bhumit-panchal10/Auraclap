<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseURL extends Model
{
    public $table = 'base_url';
    protected $dates = [
        'created_at',
        'updated_at',
     
    ];

    protected $fillable = [
       'id', 'URL'
    ];
}


