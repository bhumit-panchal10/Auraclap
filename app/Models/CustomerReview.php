<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReview extends Model
{
    use HasFactory;
    public $table = 'customer_review_rating';
    protected $fillable = [
        'customer_review_rating_id',
        'customer_id',
        'order_id',
        'rating',
        'review',
        'Technicial_id',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'

    ];
}
