<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    use HasFactory;
    public $table = 'Time_slot';
    protected $primaryKey = 'Time_slot_id';
    protected $fillable = [
        'Time_slot_id',
        'strtime',
        'fromtime',
        'totime'


    ];
}
