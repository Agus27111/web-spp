<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Classroom extends Model

{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $table = 'classes';

    protected $fillable = ['unit_id', 'name', 'academic_year_id', 'foundation_id'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->foundation_id) && Auth::check()) {
                $model->foundation_id = Auth::user()->foundation_id;
            }
        });

        // Tambah global scope foundation di pivot model juga
        static::addGlobalScope('foundation', function (Builder $builder) {
            if (Auth::check() && Auth::user()->role !== 'superadmin') {
                $builder->where('foundation_id', Auth::user()->foundation_id);
            }
        });
    }


    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function studentAcademics()
    {
        return $this->hasMany(StudentAcademic::class, 'class_id');
    }
    public function fees()
    {
        return $this->hasMany(Fee::class);
    }
    public function academicYears()
    {
        return $this->belongsToMany(AcademicYear::class, 'academic_year_classroom')
            ->withPivot('foundation_id')
            ->withTimestamps();
    }
    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }
}
