<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\DealsOption;


class UserSubscribeDeal extends Model
{
    use HasFactory;
    public $table = 'user_subscribe_deal';
    protected $primaryKey = 'user_subscribe_deal_id';
    protected $fillable = [
        'user_subscribe_deal_id',
        'customer_id',
        'vendorid',
        'deal_id',
        'deal_option_id',
        'deal_title',
        'deal_des',
        'dealvalidity_fromdate',
        'dealvalidity_todate',
        'option_title',
        'regular_price',
        'pricecut_price',
        'monthly_voucher',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at'
    ];
    public function customername()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'Customer_GUID');
    }

    public function options()
    {
        return $this->belongsTo(DealsOption::class, 'deal_id', 'deal_id');
    }
}
