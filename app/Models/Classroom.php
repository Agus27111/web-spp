<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model

{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $table = 'classes';

    protected $fillable = ['unit_id', 'name', 'academic_year_id', 'foundation_id'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function studentAcademics()
    {
        return $this->hasMany(StudentAcademic::class);
    }
    public function fees()
    {
        return $this->hasMany(Fee::class);
    }
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }
}
