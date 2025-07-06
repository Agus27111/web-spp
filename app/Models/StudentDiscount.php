<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDiscount extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $fillable = ['foundation_id', 'student_id', 'fee_type_id', 'discount_id', 'is_active'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

   public function discount()
{
    return $this->belongsTo(Discount::class);
}

public function activeDiscount()
{
    return $this->belongsTo(Discount::class)->where('is_active', true);
}

}
