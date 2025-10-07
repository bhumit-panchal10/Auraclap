<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'Cart'; // Ensure correct table name
    protected $primaryKey = 'Cart_id'; // Ensure correct primary key
    protected $fillable = [
        'Cart_id',
        'Customer_id',
        'Categories_id',
        'subcate_id',
        'Qty',
        'rate_id',
        'amount',
        'rate',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'

    ];
    public function category()
    {
        return $this->belongsTo(Categories::class, 'Categories_id', 'Categories_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Customer_id', 'Customer_id');
    }
    public function subcategory()
    {
        return $this->belongsTo(SubCategories::class, 'subcate_id', 'iSubCategoryId');
    }

}
