<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    public $table = 'blogs';

    protected $fillable = [
        'blogId',
        'blogTitle',
        'slugname',
        'blogDescription',
        'blogDate',
        'blogImage',
        'metaTitle',
        'metaKeyword',
        'metaDescription',
        'head',
        'body',
        'strIP',
        'created_at',
        'updated_at'
    ];
}
