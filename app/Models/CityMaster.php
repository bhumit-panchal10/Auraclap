<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityMaster extends Model
{
    use HasFactory;
    public $table = 'city-masters';
    protected $primaryKey = 'cityId';
    protected $fillable = [
        'cityId',
        'stateMasterStateId',
        'cityName',
        'company_percentage',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP',
        'status',
        'ipAddress',
        'createdAt',
        'updatedAt',
    ];
}
