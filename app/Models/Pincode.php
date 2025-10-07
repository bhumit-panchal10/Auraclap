<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StateMaster;
use App\Models\CityMaster;


class Pincode extends Model
{
    use HasFactory;
    public $table = 'Pincode';
    protected $fillable = [
        'pin_id',
        'state_id',
        'city_id',
        'pincode',
        'iStatus',
        'isDelete',
        'strIP',
        'created_at',
        'updated_at'

    ];
    public function state()
    {
        return $this->belongsTo(StateMaster::class, 'state_id', 'stateId');
    }

    public function city()
    {
        return $this->belongsTo(CityMaster::class, 'city_id', 'cityId');
    }

    public function technicials()
    {
        return $this->hasMany(TechnicialPincode::class, 'Pincode_id', 'pin_id');
    }
}
