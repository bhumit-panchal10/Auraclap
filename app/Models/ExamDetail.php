<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categories;
use App\Models\SubCategories;

class ExamDetail extends Model
{
    use HasFactory;
    public $table = 'Exam_details';
    protected $fillable = [
        'Exam_details_id',
        'exam_id',
        'question',
        'option_1',
        'option_2',
        'option_3',
        'option_4',
        'correct_answer',
        'iStatus',
        'isDelete',
        'strIP',
        'created_at',
        'updated_at'

    ];
    public function exam()
    {
        return $this->belongsTo(ExamMaster::class, 'exam_id', 'Exam_master_id');
    }
}
