<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicialWalletPayment extends Model
{
    use HasFactory;
    protected $table = 'Technicial_wallet_payment';
    protected $primaryKey = 'Technicial_wallet_payment_id';
    protected $fillable = [
        'Technicial_wallet_payment_id',
        'order_id',
        'oid',
        'razorpay_payment_id',
        'razorpay_order_id',
        'razorpay_signature',
        'receipt',
        'amount',
        'currency',
        'status',
        'Remarks',
        'created_at',
        'updated_at',
        'json',
        'Technicial_id'


    ];
}
