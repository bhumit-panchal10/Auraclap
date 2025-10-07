<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCouponApplyed extends Model
{
    use HasFactory;
    public $table = 'customercouponapplyed';
    protected $fillable = [
        'id',
        'offerId',
        'customerId',
        'driverId',
        'created_at',
        'updated_at',
        'strIP',
        'result'
    ];
}
