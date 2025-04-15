<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['foundation_id', 'name'];

    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }

    public function classes()
    {
        return $this->hasMany(Classroom::class);
    }
    public function academicYears()
    {
        return $this->belongsToMany(AcademicYear::class);
    }

}
