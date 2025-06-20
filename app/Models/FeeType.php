<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeType extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $fillable = ['foundation_id', 'name', 'frequency', 'academic_year_id'];

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}