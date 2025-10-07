<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressMaster extends Model
{
    use HasFactory;
    protected $table = 'Address_Master'; // Ensure correct table name
    protected $primaryKey = 'Address_Master_id'; // Ensure correct primary key
    protected $fillable = [
        'Address_Master_id',
        'Customer_id',
        'Address',
        'created_at',
        'updated_at',
        'house_flat_no',
        'street_address',
        'Type',
    ];
}
