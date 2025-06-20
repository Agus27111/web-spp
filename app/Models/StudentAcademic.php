<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAcademic extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $table = 'student_academic_years';

    protected $fillable = ['foundation_id', 'student_id', 'academic_year_id', 'class_id', 'status', 'unit_id'];

    // Relasi ke AcademicYear
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    // Relasi ke Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    // Relasi ke Classroom
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    // Relasi ke Student
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_academic_year_id');
    }
}
