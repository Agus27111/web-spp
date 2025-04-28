<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYearUnit extends Model
{
    protected $table = 'academic_year_unit';

    protected $fillable = [
        'foundation_id',
        'academic_year_id',
        'unit_id',
    ];
}
