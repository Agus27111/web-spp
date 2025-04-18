<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'academic_years';

    protected $fillable = ['name', 'is_active'];

    public function studentAcademics()
    {
        return $this->hasMany(StudentAcademic::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
    public function units()
    {
        return $this->belongsToMany(Unit::class);
    }
    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
}
