<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $fillable = ['name', 'amount', 'foundation_id'];

    public function studentAcademic()
    {
        return $this->belongsTo(StudentAcademic::class);
    }
    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }
    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }
}
