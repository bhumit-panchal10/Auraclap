<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrimaryOrder extends Model
{
    use HasFactory;
    protected $table = 'primary_orders';
    protected $primaryKey = 'primaryiOrderId';
    protected $fillable = [
        'primaryiOrderId',
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
        'start_work',
        'slot_id',
        'Technicial_id',
        'reason_id',
        'is_review',
        'isRefund',
        'photo',
        'dis_per',
        'before_cash_payment_otp'

    ];
    public function orders()
    {
        return $this->hasMany(Order::class, 'order_primary_id', 'primaryiOrderId');
    }
    public function orderdetail()
    {
        return $this->hasMany(OrderDetail::class, 'iOrderId', 'iOrderId');
    }
    public function slot()
    {
        return $this->belongsTo(Timeslot::class, 'slot_id', 'Time_slot_id');
    }
}
