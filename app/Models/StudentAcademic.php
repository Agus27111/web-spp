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

    protected $fillable = ['foundation_id', 'student_id', 'academic_year_id', 'class_id', 'status'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }
}
