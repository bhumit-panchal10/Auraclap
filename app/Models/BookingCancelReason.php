<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingCancelReason extends Model
{
    use HasFactory;
    public $table = 'Booking_cancel_reason';
    protected $fillable = [
        'id',
        'reason',
        'created_at',
        'updated_at'

    ];
}
