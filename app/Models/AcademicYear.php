<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class AcademicYear extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $table = 'academic_years';

    protected $fillable = ['name', 'is_active', 'foundation_id'];

    public function scopeForFoundation($query)
    {
        return $query->where('foundation_id', Auth::user()->foundation_id);
    }

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
    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'academic_year_unit', 'academic_year_id', 'unit_id')
            ->when(Auth::check() && Auth::user()->role !== 'superadmin', function ($query) {
                $query->where('units.foundation_id', Auth::user()->foundation_id);
            })
            ->withTimestamps();
    }



    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'academic_year_id');
    }
}
