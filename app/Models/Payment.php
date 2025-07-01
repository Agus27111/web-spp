<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Payment extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $dates = ['payment_date'];

    protected $fillable = [
        'foundation_id',
        'student_academic_year_id',
        'fee_id',
        'month',
        'payment_date',
        'original_amount',
        'discount_applied',
        'paid_amount',
        'payment_method',
        'payment_proof',
        'applied_discounts'
    ];

    protected $casts = [
        'applied_discounts' => 'array',
        'payment_date' => 'date',
    ];

    public function setCalculatedFields(): void
    {
        if ($this->applied_discounts) {
            $this->discount_applied = collect($this->applied_discounts)
                ->sum('calculated');
            $this->paid_amount = $this->original_amount - $this->discount_applied;
        }
    }

    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($model) {
            // Hitung nilai otomatis jika tidak diset
            if (!isset($model->discount_applied) && isset($model->applied_discounts)) {
                $model->discount_applied = collect($model->applied_discounts)
                    ->sum('calculated');
            }
            
            if (!isset($model->paid_amount) && isset($model->original_amount)) {
                $model->paid_amount = $model->original_amount - ($model->discount_applied ?? 0);
            }
        });
    }

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }
    public function foundation()
    {
        return $this->belongsTo(Foundation::class, 'foundation_id');
    }

    public function getFeeNameAttribute()
    {
        return $this->fee->feeType->name ?? null;
    }

    public function studentAcademicYear()
    {
        return $this->belongsTo(StudentAcademic::class, 'student_academic_year_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function studentDiscount()
    {
        return $this->hasOneThrough(
            StudentDiscount::class,
            StudentAcademic::class,
            'id', // Foreign key on StudentAcademic table
            'student_id', // Foreign key on StudentDiscount table
            'student_academic_year_id', // Local key on Payment table
            'student_id' // Local key on StudentAcademic table
        );
    }
}
