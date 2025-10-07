<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicialLedger extends Model
{
    use HasFactory;
    public $table = 'Technicial_ledger';
    protected $fillable = [
        'Technicial_ledger_id',
        'Technicial_id',
        'comments',
        'opening_bal',
        'Cr',
        'Dr',
        'closing_bal',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'
    ];
    public function technician()
    {
        return $this->belongsTo(Technicial::class, 'Technicial_id', 'Technicial_id')->with('state');
    }
}
