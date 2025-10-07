<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicialPincode extends Model
{
    use HasFactory;
    public $table = 'Technicial_Pincode';
    protected $fillable = [
        'Technicial_Pincode_id',
        'Technicial_id',
        'Pincode_id',
        'state_id',
        'city_id',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'
    ];
    public function state()
    {
        return $this->belongsTo(StateMaster::class, 'state_id', 'stateId');
    }
    public function city()
    {
        return $this->belongsTo(CityMaster::class, 'city_id', 'cityId');
    }
    public function Technicial()
    {
        return $this->belongsTo(Technicial::class, 'Technicial_id', 'Technicial_id');
    }
    public function pincodes()
    {
        return $this->hasMany(Pincode::class, 'pin_id', 'Pincode_id');
    }
}
