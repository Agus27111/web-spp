<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Traits\BelongsToFoundation;

class Unit extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $fillable = ['foundation_id', 'name'];

     // Event "creating" untuk memastikan foundation_id terisi
     protected static function booted()
     {
         static::creating(function ($unit) {
             if (Auth::user()->role === 'superadmin') {
                 if (empty($unit->foundation_id)) {
                     $unit->foundation_id = request()->input('foundation_id');
                 }
             } else {
                 $unit->foundation_id = Auth::user()->foundation_id;
             }
         });
     }

    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
    public function academicYears()
    {
        return $this->belongsToMany(AcademicYear::class);
    }


}
