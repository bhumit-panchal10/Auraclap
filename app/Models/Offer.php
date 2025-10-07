<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    public $table = 'offer';
    protected $fillable = [
        'id',
        'text',
        'value',
        'startdate',
        'enddate',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'

    ];
}
