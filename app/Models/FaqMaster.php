<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqMaster extends Model
{
    use HasFactory;
    public $table = 'faq-masters';
    protected $fillable = [
        'id',
        'question',
        'answer',
        'category',
        'isActive',
        'createdAt',
        'strIP',
        'updatedAt'
    ];
}
