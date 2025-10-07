<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    public $table = 'Categories';
    protected $primaryKey = 'Categories_id';
    protected $fillable = [
        'Categories_id',
        'Category_name',
        'caption',
        'rating',
        'hours',
        'onwards_amount',
        'city_id',
        'Categories_slug',
        'Categories_img',
        'Categories_icon',
        'display_homepage',
        'home_cate_image',
        'warranty',
        'ratecard_pdf',
        'carousel_image',
        'meta_keyword',
        'meta_description',
        'meta_head',
        'meta_title',
        'meta_body',
        'iStatus',
        'isDelete',
        'strIP',
        'created_at',
        'updated_at'

    ];
    public function subcategories()
    {
        return $this->hasMany(SubCategories::class, 'iCategoryId', 'Categories_id');
    }
    public function rates()
    {
        return $this->hasMany(Managerate::class, 'cate_id', 'Categories_id');
    }
}
