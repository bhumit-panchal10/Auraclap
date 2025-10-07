<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoMaster extends Model
{
    use HasFactory;
    public $table = 'video-Master';
    protected $fillable = [
        'id',
        'title',
        'link',
        'created_at',
        'updated_at'

    ];
}
