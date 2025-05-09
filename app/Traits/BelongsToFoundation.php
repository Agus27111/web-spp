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
            // Hanya set foundation_id jika belum ada dan user memiliki foundation_id
            if (empty($model->foundation_id) && Auth::check() && !empty(Auth::user()->foundation_id)) {
                Log::info('BelongsToFoundation Trait - Setting foundation_id from user', [
                    'user_foundation_id' => Auth::user()->foundation_id
                ]);
                $model->foundation_id = Auth::user()->foundation_id;
            }
        });

        static::addGlobalScope('foundation', function (Builder $builder) {
            if (Auth::check() && Auth::user()->role !== 'superadmin' && !empty(Auth::user()->foundation_id)) {
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
        if (Auth::check() && Auth::user()->role !== 'superadmin' && !empty(Auth::user()->foundation_id)) {
            $table = $query->getModel()->getTable();
            return $query->where("{$table}.foundation_id", Auth::user()->foundation_id);
        }
        return $query;
    }
}
