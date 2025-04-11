<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_year_id',
        'fee_id',
        'month',
        'payment_date',
        'total_fee',
        'total_discount',
        'amount_paid',
        'payment_method',
        'file_pdf'
    ];

    public function studentAcademic()
    {
        return $this->belongsTo(StudentAcademic::class);
    }
    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }
}
