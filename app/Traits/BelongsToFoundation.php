<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait BelongsToFoundation
{
    protected static function bootBelongsToFoundation()
    {
        static::creating(function ($model) {
            // Skip jika foundation_id sudah ada
            if (!empty($model->foundation_id)) {
                return;
            }

            // 1. Coba dapatkan dari relasi unit jika ada
            if (empty($model->foundation_id) && !empty($model->unit_id)) {
                if (method_exists($model, 'unit') && $model->unit) {
                    $model->foundation_id = $model->unit->foundation_id;
                    Log::info('Set foundation_id from unit relation', [
                        'foundation_id' => $model->unit->foundation_id
                    ]);
                } else {
                    $model->foundation_id = \App\Models\Unit::where('id', $model->unit_id)
                        ->value('foundation_id');
                }
            }

            // 2. Coba dapatkan dari relasi academic year jika ada
            if (empty($model->foundation_id) && !empty($model->academic_year_id)) {
                if (method_exists($model, 'academicYear') && $model->academicYear) {
                    $model->foundation_id = $model->academicYear->foundation_id;
                    Log::info('Set foundation_id from academic year relation', [
                        'foundation_id' => $model->academicYear->foundation_id
                    ]);
                } else {
                    $model->foundation_id = \App\Models\AcademicYear::where('id', $model->academic_year_id)
                        ->value('foundation_id');
                }
            }

            // 3. Fallback ke user (hanya untuk non-superadmin)
            if (
                empty($model->foundation_id) && Auth::check() &&
                Auth::user()->role !== 'superadmin' &&
                !empty(Auth::user()->foundation_id)
            ) {
                $model->foundation_id = Auth::user()->foundation_id;
                Log::info('Set foundation_id from user', [
                    'foundation_id' => Auth::user()->foundation_id
                ]);
            }

            // Validasi akhir
            if (empty($model->foundation_id)) {
                Log::error('Failed to set foundation_id for model', [
                    'model' => get_class($model),
                    'id' => $model->id ?? null,
                    'data' => $model->toArray()
                ]);
            }
        });

        static::addGlobalScope('foundation', function (Builder $builder) {
            if (
                Auth::check() && Auth::user()->role !== 'superadmin' &&
                !empty(Auth::user()->foundation_id)
            ) {
                $table = $builder->getModel()->getTable();
                $builder->where("{$table}.foundation_id", Auth::user()->foundation_id);
            }
        });
    }

    public function foundation()
    {
        return $this->belongsTo(\App\Models\Foundation::class);
    }

    public function scopeForFoundation($query)
    {
        if (
            Auth::check() && Auth::user()->role !== 'superadmin' &&
            !empty(Auth::user()->foundation_id)
        ) {
            $table = $query->getModel()->getTable();
            return $query->where("{$table}.foundation_id", Auth::user()->foundation_id);
        }
        return $query;
    }
}
