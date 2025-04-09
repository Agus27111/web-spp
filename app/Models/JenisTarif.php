<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisTarif extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nama', 'frekuensi'];

    public function tarifs()
    {
        return $this->hasMany(Tarif::class);
    }
    public function potongans()
    {
        return $this->hasMany(Potongan::class);
    }
}
