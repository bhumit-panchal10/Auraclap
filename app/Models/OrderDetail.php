<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'orderdetail';
    protected $primaryKey = 'iOrderDetailId';
    protected $fillable = [
        'iOrderDetailId',
        'iOrderId',
        'order_primary_id',
        'iCustomerId',
        'category_id',
        'Ratecard_id',
        'start_otp',
        'end_otp',
        'start_work',
        'order_status',
        'Technicial_id',
        'reason_id',
        'qty',
        'technicial_add_extra_service',
        'rate',
        'photo',
        'service_photo_1',
        'service_photo_2',
        'amount',
        'net_amount',
        'GSTAmount',
        'discount_amount',
        'subcategory_id',
        'isRefund',
        'is_review',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'

    ];
    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id', 'Categories_id');
    }
    public function subcategory()
    {
        return $this->belongsTo(SubCategories::class, 'subcategory_id', 'iSubCategoryId');
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class, 'iOrderId', 'iOrderId');
    }
      public function Technicial()
    {
        return $this->belongsTo(Technicial::class, 'Technicial_id', 'Technicial_id');
    }
    public function customerReviews()
    {
        return $this->hasMany(CustomerReview::class, 'Technicial_id', 'Technicial_id');
    }
    public function invoice()
    {
        return $this->hasOne(OrderInvoice::class, 'order_id', 'iOrderDetailId');
    }
}
