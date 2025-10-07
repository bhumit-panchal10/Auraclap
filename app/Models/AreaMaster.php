<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaMaster extends Model
{
    use HasFactory;
    public $table = 'area-masters';
    protected $fillable = [
        'areaId',
        'areaName',
        'areaPincode',
        'priceId',
        'areastateId',
        'areacityId',
        'pickupstarttime',
        'pickupendtime',
        'ispickupArea',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'
    ];
}
