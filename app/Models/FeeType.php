<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'frequency'];

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }
    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }
}
