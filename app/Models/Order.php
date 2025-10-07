<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'order';
    protected $primaryKey = 'iOrderId';
    protected $fillable = [
        'iOrderId',
        'order_primary_id',
        'iCustomerId',
        'iAmount',
        'gst_amount',
        'grand_amount',
        'iDiscount',
        'iNetAmount',
        'isPayment',
        'isDispatched',
        'isDispatchedBy',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP',
        'payment_mode',
        'start_otp',
        'city_id',
        'end_otp',
        'Customer_name',
        'Customer_phone',
        'Customer_Address',
        'Pincode',
        'order_status',
        'order_date',
        'start_otp',
        'end_otp',
        'service_photo_1',
        'service_photo_2',
        'start_work',
        'slot_id',
        'Technicial_id',
        'reason_id',
        'is_review',
        'isRefund',
        'photo',
        'dis_per'

    ];
    public function orderdetail()
    {
        return $this->hasMany(OrderDetail::class, 'iOrderId', 'iOrderId');
    }
    public function slot()
    {
        return $this->belongsTo(Timeslot::class, 'slot_id', 'Time_slot_id');
    }
    
    public function primaryorder()
    {
        return $this->belongsTo(PrimaryOrder::class, 'order_primary_id', 'primaryiOrderId');
    }
    
    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id', 'Categories_id');
    }
    public function subcategory()
    {
        return $this->belongsTo(SubCategories::class, 'subcategory_id', 'iSubCategoryId');
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
