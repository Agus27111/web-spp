<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDiscount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['student_year_id', 'fee_type_id', 'discount_id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
