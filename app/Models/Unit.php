<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Traits\BelongsToFoundation;
use Illuminate\Support\Facades\Log;

class Unit extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $fillable  = ['name', 'foundation_id'];
    protected $visible  = ['name', 'foundation_id'];

    protected static function booted()
    {
        static::creating(function ($model) {
            Log::info('Unit Model Booted - Before Trait', $model->toArray());
        });

        static::created(function ($model) {
            Log::info('Unit Model Booted - After Create', $model->toArray());
        });
    }

    // Hapus event creating dari model karena sudah ada di trait
    // Pindahkan logika ke trait atau sebaliknya, jangan duplikasi

    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function scopeForFoundation($query)
    {
        if (Auth::user()->role === 'superadmin') {
            return $query;
        }

        return $query->where('units.foundation_id', Auth::user()->foundation_id);
    }

    public function studentAcademics()
    {
        return $this->hasMany(StudentAcademic::class, 'unit_id');
    }

    public function academicYears()
    {
        return $this->belongsToMany(AcademicYear::class, 'academic_year_unit')
            ->using(AcademicYearUnit::class)
            ->withPivot('foundation_id')
            ->withTimestamps();
    }
}
