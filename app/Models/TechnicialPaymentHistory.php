<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicialPaymentHistory extends Model
{
    use HasFactory;
    public $table = 'Technicial_payment_history';
    protected $fillable = [
        'Tech_pay_history_id',
        'Technical_id',
        'amount',
        'Transaction_reference_id',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'
    ];
}
