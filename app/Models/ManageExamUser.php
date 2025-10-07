<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categories;
use App\Models\SubCategories;

class ManageExamUser extends Model
{
    use HasFactory;
    public $table = 'Exam_User';
    protected $fillable = [
        'Exam_User_id',
        'login_id',
        'cate_id',
        'exam_id',
        'name',
        'password',
        'iStatus',
        'isDelete',
        'strIP',
        'created_at',
        'updated_at'

    ];
    public function category()
    {

        return $this->belongsTo(Categories::class, 'cate_id', 'Categories_id');
    }
    public function exam()
    {

        return $this->belongsTo(ExamMaster::class, 'exam_id', 'Exam_master_id');
    }
}
