<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;
    public $table = 'Slider';
    protected $fillable = [
        'id',
        'image',
        'link',
        'created_at',
        'updated_at'
    ];
}
