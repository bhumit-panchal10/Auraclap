<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    use HasFactory;
    public $table = 'Recruitment';
    protected $fillable = [
        'Recruitment_id',
        'job_title',
        'job_type',
        'experience',
        'qualification',
        'location',
        'timing',
        'number_of_opening',
        'salary',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'

    ];
}
