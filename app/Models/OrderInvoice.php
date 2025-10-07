<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderInvoice extends Model
{
    use HasFactory;
    public $table = 'order_invoice';
    protected $primaryKey = 'invoice_id';
    protected $fillable = [
        'invoice_id',
        'order_id',
        'date',
        'created_at',
        'updated_at',
       
    ];
}
