<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicialSlider extends Model
{
    use HasFactory;
    public $table = 'TechnicialSlider';
    protected $fillable = [
        'id',
        'image',
        'link',
        'created_at',
        'updated_at'
    ];
}
