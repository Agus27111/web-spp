<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDiscount extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $fillable = ['foundation_id', 'student_year_id', 'fee_type_id', 'discount_id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
    public function foundation()
{
    return $this->belongsTo(Foundation::class);
}

}
