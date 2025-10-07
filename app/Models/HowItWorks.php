<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HowItWorks extends Model
{
    use HasFactory;
    public $table = 'how_it_works';
    protected $fillable = [
        'id',
        'category_id',
        'title',
        'image',
        'description',
        'catname',
        'iStatus',
        'isDelete',
        'strIP',
        'created_at',
        'updated_at'

    ];
}
