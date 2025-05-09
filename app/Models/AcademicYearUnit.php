<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Auth;

class AcademicYearUnit extends Pivot
{
    protected $table = 'academic_year_unit';

    protected $fillable = [
        'foundation_id',
        'academic_year_id',
        'unit_id',
    ];

    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         if (empty($model->foundation_id) && Auth::check()) {
    //             $model->foundation_id = Auth::user()->foundation_id;
    //         }
    //     });

    //     static::addGlobalScope('foundation', function (Builder $builder) {
    //         if (Auth::check() && Auth::user()->role !== 'superadmin') {
    //             $builder->where('foundation_id', Auth::user()->foundation_id);
    //         }
    //     });
    // }

    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
