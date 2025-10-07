<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categories;
use App\Models\SubCategories;

class Managerate extends Model
{
    use HasFactory;
    public $table = 'Rate';
    protected $fillable = [
        'rate_id',
        'cate_id',
        'subcate_id',
        'title',
        'description',
        'how_it_work_description',
        'amount',
        'time',
        'city_id',
        'iStatus',
        'isDelete',
        'strIP',
        'created_at',
        'updated_at'

    ];
    public function category()
    {
        return $this->belongsTo(Categories::class, 'cate_id', 'Categories_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategories::class, 'subcate_id', 'iSubCategoryId');
    }
    public function city()
    {
        return $this->belongsTo(CityMaster::class, 'city_id', 'cityId');
    }
}
