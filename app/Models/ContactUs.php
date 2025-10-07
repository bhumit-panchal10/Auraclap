<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;
    public $table = 'contactus-masters';
    protected $fillable = [
        'id',
        'firstName',
        'lastName',
        'email',
        'phone_number',
        'address',
        'message',
        'created_at',
        'updated_at'
    ];
}
