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
                $builder->where('foundation_id', Auth::user()->foundation_id);
            }
        });
    }

    public function foundation()
    {
        return $this->belongsTo(\App\Models\Foundation::class);
    }
}
