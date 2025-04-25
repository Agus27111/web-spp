<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $fillable = [
        'foundation_id',
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
    public function foundation()
{
    return $this->belongsTo(Foundation::class);
}

}
