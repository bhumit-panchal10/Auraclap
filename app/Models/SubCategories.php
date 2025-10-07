<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategories extends Model
{
    use HasFactory;
    public $table = 'subcategory';
    protected $primaryKey = 'iSubCategoryId';
    protected $fillable = [
        'iSubCategoryId',
        'iCategoryId',
        'strSubCategoryName',
        'strCategoryName',
        'strSlugName',
        'rate',
        'sub_rat_flag',
        'title',
        'sub_title',
        'SubCategories_img',
        'subCategory_icon',
        'display_homepage',
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
    public function category()
    {
        return $this->belongsTo(Categories::class, 'iCategoryId', 'Categories_id');
    }
     public function rates()
    {
        return $this->hasMany(Managerate::class, 'subcate_id', 'iSubCategoryId');
    }
}
