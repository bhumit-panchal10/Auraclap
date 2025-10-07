<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'card_payment';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'order_id',
        'Customer_id',
        'oid',
        'razorpay_payment_id',
        'razorpay_order_id',
        'razorpay_signature',
        'receipt',
        'amount',
        'currency',
        'status',
        'iPaymentType',
        'Remarks',
        'created_at',
        'updated_at',
        'json'


    ];
}
