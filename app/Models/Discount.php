<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'amount'];

    public function studentAcademic()
    {
        return $this->belongsTo(StudentAcademic::class);
    }
    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }
}
