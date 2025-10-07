<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JoinAsTechnicial extends Model
{
    use HasFactory;
    public $table = 'join_as_technicial';
    protected $fillable = [
        'joinastec_id',
        'name',
        'onboard',
        'email',
        'mobile_no',
        'stateid',
        'city',
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
}
