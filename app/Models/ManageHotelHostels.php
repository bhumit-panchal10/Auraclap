<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManageHotelHostels extends Model
{
    use HasFactory;
    public $table = 'ManageHotelHostels';
    protected $fillable = [
        'id',
        'contactname',
        'hotelhostel_name',
        'mobile_no',
        'role',
        'email',
        'company_name',
        'Gst_no',
        'iStatus',
        'isDelete',
        'strIP',
        'created_at',
        'updated_at'

    ];
}
