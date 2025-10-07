<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CMSMaster extends Model
{
    use HasFactory;
    public $table = 'cms';
    protected $fillable = [
        'id',
        'strTitle',
        'strDescription',
        'slugname',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'
    ];
}
