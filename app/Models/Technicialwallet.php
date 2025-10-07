<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technicialwallet extends Model
{
    use HasFactory;
    protected $table = 'Technicial_wallet';
    protected $primaryKey = 'Technicial_wallet_id';
    protected $fillable = [
        'Technicial_wallet_id',
        'Technicial_id',
        'order_id',
        'Amount',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'

    ];
}
