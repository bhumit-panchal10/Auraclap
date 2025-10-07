<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicialService extends Model
{
    use HasFactory;
    public $table = 'Technicial_Service';
    protected $fillable = [
        'Tech_service_id',
        'Technicial_id',
        'Category_id',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'
    ];
    public function state()
    {
        return $this->belongsTo(StateMaster::class, 'stateid', 'stateId');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'Category_id');
    }

    public function Technicial()
    {
        return $this->belongsTo(Technicial::class, 'Technicial_id', 'Technicial_id');
    }
}
