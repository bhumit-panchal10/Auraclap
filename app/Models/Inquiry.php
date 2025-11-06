<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;
    public $table = 'inquiry';
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'service',
        'message',
        'created_at',
        'updated_at'
    ];
}
