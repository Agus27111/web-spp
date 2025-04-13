<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['parent_id', 'name', 'nisn', 'image', 'birth_date'];

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }
    public function studentAcademics()
    {
        return $this->hasMany(StudentAcademic::class);
    }
    public function studentDiscounts()
    {
        return $this->hasMany(StudentDiscount::class);
    }
}
