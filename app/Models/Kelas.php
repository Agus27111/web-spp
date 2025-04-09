<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unit_id', 'nama'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function siswaTahun()
    {
        return $this->hasMany(SiswaTahun::class);
    }
    public function tarifs()
    {
        return $this->hasMany(Tarif::class);
    }
}
