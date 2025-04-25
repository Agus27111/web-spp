<?php

namespace App\Models;

use App\Traits\BelongsToFoundation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes, BelongsToFoundation;

    protected $fillable = ['foundation_id', 'name', 'amount', 'date', 'description', 'payment_proof'];

    public function foundation()
    {
        return $this->belongsTo(Foundation::class);
    }
}
