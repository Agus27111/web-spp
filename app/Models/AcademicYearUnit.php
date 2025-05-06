<?php

namespace App\Models;

use Filament\Forms\Components\Builder;
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

    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'academic_year_unit', 'academic_year_id', 'unit_id')
            ->when(Auth::check() && Auth::user()->role !== 'superadmin', function ($query) {
                $query->where('units.foundation_id', Auth::user()->foundation_id);
            })
            ->withTimestamps();
    }
}
