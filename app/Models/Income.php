<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $table = 'incomes';

    protected $fillable = [
        'foundation_id',
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

    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }
}
