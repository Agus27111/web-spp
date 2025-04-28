<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToFoundation
{
    protected static function bootBelongsToFoundation()
    {
        static::creating(function ($model) {
            if (Auth::check() && isset(Auth::user()->foundation_id)) {
                $model->foundation_id = Auth::user()->foundation_id;
            }
        });

        static::addGlobalScope('foundation', function (Builder $builder) {
            if (Auth::check() && isset(Auth::user()->foundation_id)) {
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
        if (Auth::check() && Auth::user()->role !== 'superadmin') {
            $table = $query->getModel()->getTable();
            return $query->where("{$table}.foundation_id", Auth::user()->foundation_id);
        }
        return $query;
    }
}

