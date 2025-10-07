<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categories;
use App\Models\SubCategories;

class ExamMaster extends Model
{
    use HasFactory;
    public $table = 'Exam-master';
    protected $fillable = [
        'Exam_master_id',
        'Exam_name',
        'cat_id',
        'iStatus',
        'isDelete',
        'strIP',
        'created_at',
        'updated_at'

    ];
    public function category()
    {

        return $this->belongsTo(Categories::class, 'cat_id', 'Categories_id');
    }

    public function examDetails()
    {
        return $this->hasMany(ExamDetail::class, 'exam_id', 'Exam_master_id');
    }
}
