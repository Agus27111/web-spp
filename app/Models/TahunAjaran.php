<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TahunAjaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nama', 'is_aktif'];

    public function siswaTahun() { return $this->hasMany(SiswaTahun::class); }
    public function tarifs() { return $this->hasMany(Tarif::class); }
}
