<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'incomes';

    protected $fillable = [
        'academic_year_id',
        'source',
        'amount',
        'description',
        'date',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
