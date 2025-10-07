<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categories;
use App\Models\SubCategories;

class ExamResult extends Model
{
    use HasFactory;
    public $table = 'Exam_Result';
    protected $fillable = [
        'Exam_Result_id',
        'Exam_user_id',
        'Exam_id',
        'Answer',
        'Answer_in_option',
        'Exam_detail_id',
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
    public function examDetail()
    {
        return $this->belongsTo(ExamDetail::class, 'Exam_detail_id', 'Exam_details_id');
    }
}
