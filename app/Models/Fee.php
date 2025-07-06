<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $fillable = [
        'fee_type_id', 
        'foundation_id', 
        'academic_year_id', 
        'amount'
    ];

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }
    protected static function booted()
    {
        static::creating(function ($fee) {
            // Kalau belum diisi, isi dari FeeType
            if (!$fee->academic_year_id && $fee->feeType) {
                $fee->academic_year_id = $fee->feeType->academic_year_id;
            }
        });

        static::updating(function ($fee) {
            // Optional: pastikan tetap sinkron saat update
            if ($fee->feeType && $fee->academic_year_id != $fee->feeType->academic_year_id) {
                $fee->academic_year_id = $fee->feeType->academic_year_id;
            }
        });
}
}

